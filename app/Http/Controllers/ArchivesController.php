<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Volunteer;
use App\Models\User;
use App\Models\Event;
use App\Models\Task;
use App\Models\Ministry;
use Illuminate\Support\Carbon;

class ArchivesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get paginated results and then transform the items
        $archivedRoles = User::where('is_archived', true)
            ->with(['archiver', 'ministry'])
            ->paginate(10);

        // Transform the items collection
        $archivedRoles->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'role' => ucfirst($user->role),
                'ministry' => $user->ministry->ministry_name ?? 'No Ministry',
                'archived_date' => $user->archived_at ? $user->archived_at->format('Y-m-d') : 'N/A',
                'reason' => $user->archive_reason ?? 'N/A',
                'archived_by' => $user->archiver ? $user->archiver->email : 'Unknown',
            ];
        });

        $archivedVolunteers = Volunteer::where('is_archived', true)
            ->with('archiver', 'detail')
            ->paginate(10);

        $archivedVolunteers->getCollection()->transform(function ($volunteer) {
            return [
                'id' => $volunteer->id,
                'name' => $volunteer->nickname ?? $volunteer->detail?->full_name ?? 'No Name',
                'email' => $volunteer->email_address ?? 'No email',
                'archived_date' => $volunteer->archived_at ? $volunteer->archived_at->format('Y-m-d') : 'N/A',
                'reason' => $volunteer->archive_reason,
                'archived_by' => $volunteer->archiver->email ?? 'Unknown',
            ];
        });

        $archivedEvents = Event::where('is_archived', true)
            ->with('archiver')
            ->paginate(10);

        $archivedEvents->getCollection()->transform(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'date' => $event->date ? $event->date->format('Y-m-d') : 'N/A',
                'archived_date' => $event->archived_at ? $event->archived_at->format('Y-m-d') : 'N/A',
                'reason' => $event->archive_reason,
                'archived_by' => $event->archiver->email ?? 'Unknown',
            ];
        });

        $archivedTasks = Task::where('is_archived', true)
            ->with('archiver')
            ->paginate(10);

        $archivedTasks->getCollection()->transform(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'due_date' => $task->due_date ? Carbon::parse($task->due_date)->format('Y-m-d') : null,
                'archived_date' => $task->archived_at ? Carbon::parse($task->archived_at)->format('Y-m-d') : null,
                'reason' => $task->archive_reason,
                'archived_by' => $task->archiver->email ?? 'Unknown',
            ];
        });

        return view('admin_archives', [
            'roles' => $archivedRoles,
            'volunteers' => $archivedVolunteers,
            'tasks' => $archivedTasks,
            'events' => $archivedEvents,
            'user' => $user,
        ]);
    }
    public function paginate(Request $request)
    {
        $tab = $request->get('tab', 'roles');
        $page = $request->get('page', 1);

        $perPage = 10;

        switch ($tab) {
            case 'roles':
                $items = User::where('is_archived', true)
                    ->with(['archiver', 'ministry'])
                    ->paginate($perPage, ['*'], 'roles_page', $page);

                $items->getCollection()->transform(function ($user) {
                    return [
                        'id' => $user->id,
                        'email' => $user->email,
                        'role' => ucfirst($user->role),
                        'ministry' => $user->ministry->ministry_name ?? 'No Ministry',
                        'archived_date' => $user->archived_at ? $user->archived_at->format('Y-m-d') : 'N/A',
                        'reason' => $user->archive_reason ?? 'N/A',
                        'archived_by' => $user->archiver ? $user->archiver->email : 'Unknown',
                    ];
                });
                break;

            case 'volunteers':
                $items = Volunteer::where('is_archived', true)
                    ->with('archiver', 'detail')
                    ->paginate($perPage, ['*'], 'volunteers_page', $page);

                $items->getCollection()->transform(function ($volunteer) {
                    return [
                        'id' => $volunteer->id,
                        'name' => $volunteer->nickname ?? $volunteer->detail?->full_name ?? 'No Name',
                        'email' => $volunteer->email_address ?? 'No email',
                        'archived_date' => $volunteer->archived_at ? $volunteer->archived_at->format('Y-m-d') : 'N/A',
                        'reason' => $volunteer->archive_reason,
                        'archived_by' => $volunteer->archiver->email ?? 'Unknown',
                    ];
                });
                break;

            case 'events':
                $items = Event::where('is_archived', true)
                    ->with('archiver')
                    ->paginate($perPage, ['*'], 'events_page', $page);

                $items->getCollection()->transform(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'date' => $event->date ? $event->date->format('Y-m-d') : 'N/A',
                        'archived_date' => $event->archived_at ? $event->archived_at->format('Y-m-d') : 'N/A',
                        'reason' => $event->archive_reason,
                        'archived_by' => $event->archiver->email ?? 'Unknown',
                    ];
                });
                break;

            case 'tasks':
                $items = Task::where('is_archived', true)
                    ->with('archiver')
                    ->paginate($perPage, ['*'], 'tasks_page', $page);

                $items->getCollection()->transform(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'status' => $task->status,
                        'due_date' => $task->due_date ? Carbon::parse($task->due_date)->format('Y-m-d') : null,
                        'archived_date' => $task->archived_at ? Carbon::parse($task->archived_at)->format('Y-m-d') : null,
                        'reason' => $task->archive_reason,
                        'archived_by' => $task->archiver->email ?? 'Unknown',
                    ];
                });
                break;

            default:
                return response()->json(['error' => 'Invalid tab'], 400);
        }

        return response()->json([
            'items' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'links' => $items->links('vendor.pagination.tailwind')->toHtml(),
            ]
        ]);
    }
}