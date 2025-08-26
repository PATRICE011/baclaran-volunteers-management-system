<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Volunteer;
use App\Models\Ministry;
use App\Models\Event;
use App\Models\Task;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get counts from models, excluding archived records
        $attendanceData = $this->getVolunteerAttendanceData();

        // Count only non-archived volunteers
        $totalVolunteers = Volunteer::where('is_archived', false)->count();

        $activeVolunteers = Volunteer::where('is_archived', false)
            ->whereHas('detail', function ($query) {
                $query->where('volunteer_status', 'Active');
            })
            ->count();

        $inactiveVolunteers = Volunteer::where('is_archived', false)
            ->whereHas('detail', function ($query) {
                $query->where('volunteer_status', '!=', 'Active');
            })
            ->count();

        // Count only non-archived upcoming events
        $upcomingEvents = Event::where('is_archived', false)
            ->where('date', '>=', now())
            ->where('date', '<=', now()->addDays(30))
            ->count();

        // Count only non-deleted ministries (using soft deletes)
        $activeMinistries = Ministry::whereNotNull('parent_id')
            ->count();

        // Task completion rate (excluding archived tasks)
        $totalTasks = Task::where('is_archived', false)->count();
        $completedTasks = Task::where('is_archived', false)
            ->where('status', 'completed')
            ->count();
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Get parent ministries with volunteer counts (excluding archived volunteers)
        $parentMinistries = Ministry::whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->with([
                        'children' => function ($subQuery) {
                            $subQuery->withCount([
                                'volunteers as volunteers_count' => function ($q) {
                                    $q->where('is_archived', false);
                                }
                            ]);
                        }
                    ])
                        ->withCount([
                            'volunteers as volunteers_count' => function ($q) {
                                $q->where('is_archived', false);
                            }
                        ]);
                }
            ])
            ->get()
            ->map(function ($ministry) {
                // Count volunteers in direct children
                $directChildrenVolunteers = $ministry->children->sum('volunteers_count');

                // Count volunteers in grandchildren (sub-sub-ministries)
                $grandChildrenVolunteers = $ministry->children->reduce(function ($carry, $child) {
                    return $carry + $child->children->sum('volunteers_count');
                }, 0);

                $totalVolunteers = $directChildrenVolunteers + $grandChildrenVolunteers;

                return [
                    'name' => $ministry->ministry_name,
                    'volunteers' => $totalVolunteers,
                    'color' => $this->getMinistryColor($ministry->ministry_name)
                ];
            });

        // Recent volunteers (non-archived)
        $recentVolunteers = Volunteer::with(['detail.ministry'])
            ->where('is_archived', false)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Upcoming tasks (next 7 days, not completed, not archived)
        $upcomingTasks = Task::where('is_archived', false)
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->take(3)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'date' => Carbon::parse($task->due_date)->format('Y-m-d'),
                    'assignee' => $task->volunteer->detail->full_name ?? 'Unassigned',
                    'priority' => strtolower($task->priority ?? 'medium'),
                    'status' => strtolower($task->status ?? 'pending')
                ];
            });

        $metrics = [
            'totalVolunteers' => $totalVolunteers,
            'activeVolunteers' => $activeVolunteers,
            'inactiveVolunteers' => $inactiveVolunteers,
            'upcomingEvents' => $upcomingEvents,
            'taskCompletionRate' => $taskCompletionRate,
            'activeMinistries' => $activeMinistries,
            'recentVolunteers' => $recentVolunteers,
            'upcomingTasks' => $upcomingTasks,
            'ministryData' => $parentMinistries,
            'attendanceData' => $attendanceData
        ];

        return view('dashboard', compact('user', 'metrics'));
    }

    private function getVolunteerAttendanceData()
    {
        // Get the start and end dates for the last 30 days
        $endDate = now();
        $startDate = now()->subDays(30);

        // Initialize result with all weeks set to empty arrays
        $result = [
            'Week 1' => [],
            'Week 2' => [],
            'Week 3' => [],
            'Week 4' => []
        ];

        // Get all event_volunteer records where attendance is present in the last 30 days
        // Only include non-archived events and volunteers
        $attendanceRecords = DB::table('event_volunteer')
            ->join('events', 'event_volunteer.event_id', '=', 'events.id')
            ->join('volunteers', 'event_volunteer.volunteer_id', '=', 'volunteers.id')
            ->where('events.is_archived', false)
            ->where('volunteers.is_archived', false)
            ->where('attendance_status', 'present')
            ->whereBetween('events.date', [$startDate, $endDate])
            ->select('events.date', 'event_volunteer.volunteer_id')
            ->get();

        // Group by week and track unique volunteers
        foreach ($attendanceRecords as $record) {
            $date = Carbon::parse($record->date);
            $daysDiff = $date->diffInDays($startDate);

            // Calculate week number (1-4)
            $weekNumber = min(max(floor($daysDiff / 7) + 1, 1), 4);
            $weekKey = 'Week ' . $weekNumber;

            // Track unique volunteers per week
            if (!in_array($record->volunteer_id, $result[$weekKey])) {
                $result[$weekKey][] = $record->volunteer_id;
            }
        }

        // Convert arrays to counts
        return array_map('count', $result);
    }

    private function getMinistryColor($ministryName)
    {
        // Simple color mapping based on ministry name
        $colors = [
            'bg-blue-500',
            'bg-green-500',
            'bg-purple-500',
            'bg-yellow-500',
            'bg-red-500',
            'bg-indigo-500',
            'bg-pink-500',
            'bg-teal-500'
        ];

        $index = crc32($ministryName) % count($colors);
        return $colors[$index];
    }
}