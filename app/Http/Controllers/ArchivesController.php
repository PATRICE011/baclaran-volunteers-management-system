<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Volunteer;
use App\Models\User;

class ArchivesController extends Controller
{

    // ArchivesController.php
    public function index()
    {
        $user = Auth::user();

        // Archived roles (users with admin/staff roles)
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

        return view('admin_archives', [
            'roles' => $archivedRoles,
            'volunteers' => [],
            'ministries' => [],
            'tasks' => [],
            'events' => [],
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
