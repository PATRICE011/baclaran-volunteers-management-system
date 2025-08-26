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

    // In EventController.php
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'allow_pre_registration' => 'boolean',
            'pre_registration_deadline' => 'nullable|date|before:date',
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
            'allow_pre_registration' => 'boolean',
            'pre_registration_deadline' => 'nullable|date|before:date',
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully!',
            'event' => $event
        ]);
    }
    public function getAvailableVolunteers(Request $request, Event $event)
    {
        $user = Auth::user();
        $ministryId = $user->ministry_id;

        $query = Volunteer::with(['detail.ministry'])
            ->whereDoesntHave('events', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->where('is_archived', false);

        // For staff users, filter by their ministry
        if ($user->isStaff() && $ministryId) {
            $query->whereHas('detail', function ($q) use ($ministryId) {
                $q->where('ministry_id', $ministryId);
            });
        }

        $volunteers = $query->get()
            ->map(function ($volunteer) {
                return [
                    'id' => $volunteer->id,
                    'full_name' => $volunteer->detail->full_name ?? 'No Name',
                    'ministry_name' => $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
                    'profile_picture_url' => $volunteer->profile_picture_url,
                ];
            });

        return response()->json($volunteers);
    }
    public function preRegister(Request $request, Event $event)
    {
        if (!$event->allow_pre_registration) {
            return response()->json([
                'success' => false,
                'message' => 'Pre-registration is not enabled for this event'
            ], 400);
        }

        if ($event->pre_registration_deadline && now()->gt($event->pre_registration_deadline)) {
            return response()->json([
                'success' => false,
                'message' => 'Pre-registration deadline has passed'
            ], 400);
        }

        $volunteerId = $request->input('volunteer_id');
        $volunteer = Volunteer::findOrFail($volunteerId);

        // Check if user is staff and volunteer belongs to their ministry
        if (auth()->user()->isStaff()) {
            $userMinistryId = auth()->user()->ministry_id;
            $volunteerMinistryId = $volunteer->detail->ministry_id ?? null;

            if ($userMinistryId != $volunteerMinistryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only pre-register volunteers from your ministry'
                ], 403);
            }
        }

        // Fix: Specify the table name to avoid ambiguity
        if ($event->volunteers()->where('event_volunteer.volunteer_id', $volunteerId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Volunteer is already registered for this event'
            ], 400);
        }

        $event->volunteers()->attach($volunteerId, [
            'attendance_status' => 'pending',
            'checked_in_at' => null,
            'pre_registered_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volunteer pre-registered successfully'
        ]);
    }


    public function getEventVolunteers(Event $event)
    {
        $user = Auth::user();
        $volunteers = $event->volunteers()
            ->with(['detail.ministry'])
            ->get()
            ->map(function ($volunteer) use ($user) {
                $data = [
                    'id' => $volunteer->id,
                    'full_name' => $volunteer->detail->full_name ?? 'No Name',
                    'ministry_name' => $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
                    'profile_picture_url' => $volunteer->profile_picture_url,
                ];

                // Add email for admin users
                if ($user->isAdmin()) {
                    $data['email'] = $volunteer->email ?? 'No Email';
                }

                return $data;
            });

        return response()->json($volunteers);
    }
    public function getPreRegisteredVolunteers(Event $event)
    {
        $user = Auth::user();
        $volunteers = $event->volunteers()
            ->with(['detail.ministry'])
            ->wherePivotNotNull('pre_registered_at') // Only get pre-registered volunteers
            ->get()
            ->map(function ($volunteer) use ($user) {
                $data = [
                    'id' => $volunteer->id,
                    'full_name' => $volunteer->detail->full_name ?? 'No Name',
                    'ministry_name' => $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
                    'profile_picture_url' => $volunteer->profile_picture_url,
                ];

                // Add email for admin users
                if ($user->isAdmin()) {
                    $data['email'] = $volunteer->email ?? 'No Email';
                }

                return $data;
            });

        return response()->json($volunteers);
    }
    public function searchVolunteers(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
            'event_id' => 'required|exists:events,id'
        ]);

        $searchTerm = $request->query('q');
        $eventId = $request->query('event_id');
        $user = Auth::user();

        try {
            $query = Volunteer::with(['detail.ministry'])
                ->whereHas('detail', function ($q) use ($searchTerm) {
                    $q->where('full_name', 'like', "%{$searchTerm}%");
                })
                ->whereDoesntHave('events', function ($q) use ($eventId) {
                    $q->where('event_id', $eventId);
                })
                ->where('is_archived', false);

            // For staff users, filter by ministry
            if ($user->isStaff() && $user->ministry_id) {
                $query->whereHas('detail', function ($q) use ($user) {
                    $q->where('ministry_id', $user->ministry_id);
                });
            }

            $volunteers = $query->limit(10)
                ->get()
                ->map(function ($volunteer) {
                    return [
                        'id' => $volunteer->id,
                        'full_name' => $volunteer->detail->full_name ?? 'No Name',
                        'ministry_name' => $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
                        'profile_picture_url' => $volunteer->profile_picture_url ?? '/images/default-profile.png',
                        'email' => $volunteer->email ?? 'No Email' // Include email for display
                    ];
                });

            return response()->json($volunteers->toArray());
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
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



    public function addVolunteer(Event $event, Volunteer $volunteer)
    {
        if ($event->volunteers()->where('volunteer_id', $volunteer->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Volunteer is already assigned to this event'
            ], 400);
        }

        $event->volunteers()->attach($volunteer->id, [
            'attendance_status' => 'pending',
            'checked_in_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volunteer added to event successfully'
        ]);
    }

    public function removeVolunteer(Event $event, Volunteer $volunteer)
    {
        $event->volunteers()->detach($volunteer->id);

        return response()->json([
            'success' => true,
            'message' => 'Volunteer removed from event successfully'
        ]);
    }

    public function showPreRegisterModal(Event $event)
    {
        $user = Auth::user();

        // Check if pre-registration is allowed
        if (!$event->allow_pre_registration) {
            return response()->json([
                'success' => false,
                'message' => 'Pre-registration is not enabled for this event'
            ], 400);
        }

        // Check if deadline has passed
        if ($event->pre_registration_deadline && now()->gt($event->pre_registration_deadline)) {
            return response()->json([
                'success' => false,
                'message' => 'Pre-registration deadline has passed'
            ], 400);
        }

        // Get available volunteers
        $query = Volunteer::with(['detail.ministry'])
            ->whereDoesntHave('events', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->where('is_archived', false);

        // For staff users, filter by their ministry
        if ($user->isStaff() && $user->ministry_id) {
            $query->whereHas('detail', function ($q) use ($user) {
                $q->where('ministry_id', $user->ministry_id);
            });
        }

        $volunteers = $query->get()
            ->map(function ($volunteer) {
                return [
                    'id' => $volunteer->id,
                    'full_name' => $volunteer->detail->full_name ?? 'No Name',
                    'ministry_name' => $volunteer->detail->ministry->ministry_name ?? 'No Ministry',
                    'profile_picture_url' => $volunteer->profile_picture_url,
                ];
            });

        return response()->json([
            'success' => true,
            'volunteers' => $volunteers,
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'allow_pre_registration' => $event->allow_pre_registration,
                'pre_registration_deadline' => $event->pre_registration_deadline
            ]
        ]);
    }
    public function archive(Event $event)
    {
        $validated = request()->validate([
            'reason' => 'required|string|max:255'
        ]);

        $event->update([
            'is_archived' => true,
            'archive_reason' => $validated['reason'],
            'archived_at' => now(),
            'archived_by' => Auth::id(),

        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event archived successfully!'
        ]);
    }

    public function restore(Event $event)
    {
        try {
            $event->update(['is_archived' => false]);
            return response()->json([
                'success' => true,
                'message' => 'Event restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forceDelete(Event $event)
    {
        try {
            $event->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Event permanently deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids');
        $count = Event::where('is_archived', true)
            ->whereIn('id', $ids)
            ->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'restored_count' => $count,
            'message' => "$count event(s) restored successfully"
        ]);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids');
        $count = Event::where('is_archived', true)
            ->whereIn('id', $ids)
            ->forceDelete();

        return response()->json([
            'success' => true,
            'deleted_count' => $count,
            'message' => "$count event(s) permanently deleted"
        ]);
    }
}