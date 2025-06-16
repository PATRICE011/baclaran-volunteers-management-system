<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SchedulesController extends Controller
{
    //


   public function index()
{
    $user = Auth::user();

    // Fetch upcoming events within the next 7 days, including volunteers and their associated ministries
    $upcomingEvents = Event::with(['volunteers.detail.ministry'])
        ->whereBetween('date', [now(), now()->addDays(7)])
        ->get();

    // Fetch all events in the current month, including volunteers and their associated ministries
    $events = Event::with(['volunteers.detail.ministry'])
        ->whereMonth('date', now()->month)
        ->get();

    // Fetch active volunteers, ensuring their associated ministry details are included
    $availableVolunteers = Volunteer::with(['detail.ministry'])
        ->whereHas('detail', function ($query) {
            $query->where('volunteer_status', 'Active');
        })
        ->get();

    // Log the available active volunteers for debugging
    Log::info('Active Volunteers:', $availableVolunteers->toArray());

    // Pass data to the view
    return view('admin_schedule', compact('events', 'upcomingEvents', 'availableVolunteers', 'user'));
}
}
