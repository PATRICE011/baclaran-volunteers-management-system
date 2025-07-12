<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Volunteer;
use App\Models\User;
use App\Models\Event;

class ArchivesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $archivedRoles = User::where('is_archived', true)
            ->with('archiver')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'description' => $user->email,
                    'permissions' => $this->getPermissionsForRole($user->role),
                    'archived_date' => $user->archived_at?->format('Y-m-d'),
                    'reason' => $user->archive_reason,
                    'archived_by' => $user->archiver->full_name ?? 'Unknown',
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
                    'archived_date' => $volunteer->archived_at?->format('Y-m-d'),
                    'reason' => $volunteer->archive_reason,
                    'archived_by' => $volunteer->archiver->full_name ?? 'Unknown',
                ];
            });

        $archivedEvents = Event::where('is_archived', true)
            ->with('archiver')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->title,
                    'description' => $event->description,
                    'date' => $event->date?->format('Y-m-d'),
                    'start_time' => $event->start_time,
                    'end_time' => $event->end_time,
                    'archived_date' => $event->archived_at?->format('Y-m-d'),
                    'reason' => $event->archive_reason,
                    'archived_by' => $event->archiver->full_name ?? 'Unknown',
                ];
            });

        return view('admin_archives', [
            'roles' => $archivedRoles,
            'volunteers' => $archivedVolunteers,
            'ministries' => [], // Keep empty for now unless you implement ministry archiving
            'tasks' => [], // Keep empty for now unless you implement task archiving
            'events' => $archivedEvents,
            'user' => $user,
        ]);
    }

    private function getPermissionsForRole($role)
    {
        // Map roles to permissions (customize as needed)
        $permissions = [
            'admin' => ['manage_users', 'manage_content', 'manage_settings'],
            'staff' => ['manage_content', 'view_reports'],
        ];

        return $permissions[$role] ?? [];
    }
}