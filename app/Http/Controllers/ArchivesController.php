<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Volunteer;

class ArchivesController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();
        $archivedVolunteers = Volunteer::with('archiver')
            ->where('is_archived', true)
            ->get()
            ->map(function ($volunteer) {
                return [
                    'id' => $volunteer->id,
                    'name' => $volunteer->detail->full_name ?? 'Unknown',
                    'email' => $volunteer->email_address,
                    'archived_date' => $volunteer->archived_at->format('Y-m-d'),
                    'reason' => $volunteer->archive_reason,
                    'archived_by' => $volunteer->archiver->name ?? 'Unknown',
                ];
            });

        return view('admin_archives', [
            'volunteers' => $archivedVolunteers,
            'ministries' => [], // Add other entities similarly
            'tasks' => [],
            'events' => [],
            'user' => $user,
        ]);
    }
}
