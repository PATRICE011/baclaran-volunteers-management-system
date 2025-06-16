<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Volunteer;
use Carbon\Carbon;

class SchedulesController extends Controller
{
    //


    public function index()
    {
        $user = Auth::user();
        $upcomingEvents = Event::with(['volunteers.detail.ministry'])
            ->whereBetween('date', [now(), now()->addDays(7)])
            ->get();

        $events = Event::with(['volunteers.detail.ministry'])
            ->whereMonth('date', now()->month)
            ->get();

        $availableVolunteers = Volunteer::with(['detail.ministry'])
            ->whereHas('detail', function ($query) {
                $query->where('volunteer_status', 'Active');
            })
            ->get();

        return view('admin_schedule', compact('events', 'upcomingEvents', 'availableVolunteers', 'user'));
    }
}
