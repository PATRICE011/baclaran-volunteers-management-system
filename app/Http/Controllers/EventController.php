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
        $ministries = Ministry::all();

        // Update these queries to exclude archived events
        $events = Event::withCount('volunteers')
            ->with(['ministry', 'volunteers' => function ($query) {
                $query->with('detail.ministry')->take(3);
            }])
            ->where('is_archived', false)
            ->orderBy('date', 'desc')
            ->paginate(10);

        // Only count non-archived events
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
    public function create()
    {
        $ministries = Ministry::all();
        return view('events.create', compact('ministries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'volunteers' => 'required|array',
            'volunteers.*' => 'exists:volunteers,id',
        ]);

        $event = Event::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        $event->volunteers()->attach($validated['volunteers']);

        // Load relationships for the response
        $event->load('volunteers.detail');
        $event->volunteers_count = $event->volunteers->count();

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully!',
            'event' => $event
        ]);
    }
    public function show(Event $event)
    {
        $event->load('volunteers.detail', 'ministry');
        return view('events.show', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'ministry_id' => 'nullable|exists:ministries,id',
            'volunteers' => 'required|array',
            'volunteers.*' => 'exists:volunteers,id',
        ]);

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'ministry_id' => $validated['ministry_id'],
        ]);

        $event->volunteers()->sync($validated['volunteers']);

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }

    public function archive(Event $event)
    {
        $event->update(['is_archived' => true]);
        return redirect()->route('events.index')->with('success', 'Event archived successfully!');
    }

    public function restore(Event $event)
    {
        $event->update(['is_archived' => false]);
        return redirect()->route('events.index')->with('success', 'Event restored successfully!');
    }

    public function showAttendance(Event $event)
    {
        $event->load('volunteers.detail.ministry');
        return view('events.attendance', compact('event'));
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

        return redirect()->route('events.index')->with('success', 'Attendance saved successfully!');
    }


   public function getVolunteers(Request $request)
{
    $search = $request->input('search');
    $ministry = $request->input('ministry');

    $volunteers = Volunteer::with('detail.ministry')
        ->when($search, function ($query) use ($search) {
            $query->whereHas('detail', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        })
        ->when($ministry, function ($query) use ($ministry) {
            $query->whereHas('detail', function ($q) use ($ministry) {
                $q->where('ministry_id', $ministry);
            });
        })
        ->get()
        ->map(function ($volunteer) {
            return [
                'id' => $volunteer->id,
                'full_name' => $volunteer->detail->full_name ?? 'No Name',
                'ministry' => $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
                'profile_picture' => $volunteer->profile_picture_url,
            ];
        });

    return response()->json($volunteers);
}
}
