<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Volunteer;
use App\Models\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Load ministries with active volunteer counts for sub-ministries only
        $ministries = Ministry::with([
            'children.children' => function ($query) {
                $query->withCount([
                    'volunteerDetails as active_volunteers_count' => function ($q) {
                        $q->whereHas('volunteer', function ($q) {
                            $q->where('is_archived', false);
                        })
                            ->where('volunteer_status', 'Active');
                    }
                ]);
            }
        ])
            ->whereNull('parent_id')
            ->get();

        $events = Event::withCount('volunteers')
            ->where('is_archived', false)
            ->orderBy('date', 'desc')
            ->paginate(10);

        $totalEvents = Event::where('is_archived', false)->count();
        $upcomingEvents = Event::where('is_archived', false)
            ->where('date', '>=', today())
            ->count();
        $thisMonthEvents = Event::where('is_archived', false)
            ->whereMonth('date', now()->month)
            ->count();

        return view('event_management', compact(
            'user',
            'events',
            'totalEvents',
            'upcomingEvents',
            'thisMonthEvents',
            'ministries'
        ));
    }

    public function show(Event $event)
    {
        return response()->json($event);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $event = Event::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully!',
            'event' => $event
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully!'
        ]);
    }

    public function archive(Event $event)
    {
        $event->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Event archived successfully!'
        ]);
    }

    public function getEventVolunteers(Event $event)
    {
        $volunteers = $event->volunteers()
            ->with(['detail.ministry'])
            ->get()
            ->map(function ($volunteer) {
                return [
                    'id' => $volunteer->id,
                    'full_name' => $volunteer->detail->full_name ?? 'No Name',
                    'ministry_name' => $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
                    'profile_picture_url' => $volunteer->profile_picture_url,
                    'pivot' => [
                        'attendance_status' => $volunteer->pivot->attendance_status
                    ]
                ];
            });

        return response()->json($volunteers);
    }

    public function saveAttendance(Request $request, Event $event)
    {
        $attendance = $request->input('attendance', []);

        foreach ($attendance as $volunteerId => $status) {
            $event->volunteers()->updateExistingPivot($volunteerId, [
                'attendance_status' => $status,
                'checked_in_at' => $status === 'present' ? now() : null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully!'
        ]);
    }
}