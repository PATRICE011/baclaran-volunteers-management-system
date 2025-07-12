<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        // Get counts from models
        $totalVolunteers = Volunteer::count();
        $activeVolunteers = Volunteer::where('is_archived', false)->count();
        $upcomingEvents = Event::where('date', '>=', now())
            ->where('date', '<=', now()->addDays(30))
            ->count();

        // Count ministries excluding parent ministries
        $activeMinistries = Ministry::whereNotNull('parent_id')->count();

        // Task completion rate (assuming 'status' field has 'completed' value)
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Get parent ministries with volunteer counts
        $parentMinistries = Ministry::whereNull('parent_id')
            ->withCount(['volunteerDetails as volunteers_count'])
            ->get()
            ->map(function ($ministry) {
                return [
                    'name' => $ministry->ministry_name,
                    'volunteers' => $ministry->volunteers_count,
                    'color' => $this->getMinistryColor($ministry->ministry_name)
                ];
            });

        // Recent volunteers (active, not archived)
        $recentVolunteers = Volunteer::with('detail.ministry')
            ->where('is_archived', false)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($volunteer) {
                return [
                    'id' => $volunteer->id,
                    'name' => $volunteer->detail->full_name ?? 'No Name',
                    'role' => $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
                    'avatar' => $this->getInitials($volunteer->detail->full_name ?? 'NN'),
                    'status' => strtolower($volunteer->detail->volunteer_status ?? 'active'),
                    'joined' => $volunteer->created_at->format('Y-m-d')
                ];
            });

        // Upcoming tasks (next 7 days, not completed)
        $upcomingTasks = Task::where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->take(3)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'date' => Carbon::parse($task->due_date)->format('Y-m-d'), // Fixed this line
                    'assignee' => $task->volunteer->detail->full_name ?? 'Unassigned',
                    'priority' => strtolower($task->priority ?? 'medium'),
                    'status' => strtolower($task->status ?? 'pending')
                ];
            });

        $metrics = [
            'totalVolunteers' => $totalVolunteers,
            'activeVolunteers' => $activeVolunteers,
            'upcomingEvents' => $upcomingEvents,
            'taskCompletionRate' => $taskCompletionRate,
            'activeMinistries' => $activeMinistries,
            'recentVolunteers' => $recentVolunteers,
            'upcomingTasks' => $upcomingTasks,
            'ministryData' => $parentMinistries
        ];

        return view('dashboard', compact('user', 'metrics'));
    }

    private function getInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }

        return $initials;
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
