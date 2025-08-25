<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Volunteer;
use App\Models\User;
use App\Models\Event;
use App\Models\Task;
use App\Models\Ministry; // Add this import
use Illuminate\Support\Carbon;

class ArchivesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $archivedRoles = User::where('is_archived', true)
            ->with(['archiver', 'ministry'])
            ->get()
            ->map(function ($user) {
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
            ->get()
            ->map(function ($volunteer) {
                return [
                    'id' => $volunteer->id,
                    'name' => $volunteer->nickname ?? $volunteer->detail?->full_name ?? 'No Name',
                    'email' => $volunteer->email_address ?? 'No email',
                    'archived_date' => $volunteer->archived_at ? $volunteer->archived_at->format('Y-m-d') : 'N/A',
                    'reason' => $volunteer->archive_reason,
                    'archived_by' => $volunteer->archiver->email ?? 'Unknown',
                ];
            });

        // $archivedMinistries = Ministry::where('is_archived', true)
        //     ->with('archiver')
        //     ->get()
        //     ->map(function ($ministry) {
        //         return [
        //             'id' => $ministry->id,
        //             'name' => $ministry->name,
        //             'description' => $ministry->description,
        //             'archived_date' => $ministry->archived_at ? $ministry->archived_at->format('Y-m-d') : 'N/A',
        //             'reason' => $ministry->archive_reason,
        //             'archived_by' => $ministry->archiver->email ?? 'Unknown',
        //         ];
        //     });

        $archivedEvents = Event::where('is_archived', true)
            ->with('archiver')
            ->get()
            ->map(function ($event) {
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
            ->get()
            ->map(function ($task) {
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
}