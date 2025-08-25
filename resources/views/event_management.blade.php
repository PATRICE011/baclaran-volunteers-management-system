@extends('components.layout')
@section('title', 'Events')
@section('content')
    @include('components.navs')

    <div class="md:ml-64">
        <div class="flex-1 min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Events Management</h1>
                            <p class="mt-2 text-gray-600">Organize and manage church events and activities</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            @if(auth()->user()->role !== 'staff')
                                <button onclick="openAddModal()"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset">
                                    Add Event
                                </button>
                            @endif

                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Events</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalEvents }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Upcoming</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $upcomingEvents }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-100 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">This Month</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $thisMonthEvents }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">Recent Events</h2>
                            <div class="mt-4 sm:mt-0 flex space-x-3">
                                <div class="relative">
                                    <input type="text" id="searchInput" placeholder="Search events..."
                                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <select id="eventFilter"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="all">All Events</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="past">Past</option>
                                    <!-- <option value="archived">Archived</option> -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Event Details
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date & Time
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pre-Registration
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="eventsTableBody">
                                @foreach($events as $event)
                                    @php
                                        $preRegStatus = $event->allow_pre_registration ?
                                            ($event->pre_registration_deadline && now()->gt($event->pre_registration_deadline) ?
                                                'Closed' : 'Open') : 'Disabled';
                                        $preRegStatusClass = $preRegStatus === 'Open' ? 'bg-green-100 text-green-800' :
                                            ($preRegStatus === 'Closed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800');
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <!-- Event Details Column -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                                    <div class="text-sm text-gray-500">{{ Str::limit($event->description, 50) }}
                                                    </div>
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        Pre-registered: {{ $event->preRegisteredVolunteers->count() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Date & Time Column -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $event->date->format('M j, Y') }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} -
                                                {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                            </div>
                                            @if($event->pre_registration_deadline)
                                                <div class="text-xs text-gray-400 mt-1">
                                                    Deadline: {{ $event->pre_registration_deadline->format('M j, Y g:i A') }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Status Column -->
                                        <td class="px-6 py-4">
                                            @php
                                                $now = now();
                                                $eventDate = \Carbon\Carbon::parse($event->date);
                                                if ($eventDate->isPast()) {
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $status = 'Past';
                                                } elseif ($eventDate->isToday()) {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $status = 'Today';
                                                } else {
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                    $status = 'Upcoming';
                                                }
                                            @endphp
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                                {{ $status }}
                                            </span>
                                        </td>

                                        <!-- Pre-Registration Column -->
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $preRegStatusClass }}">
                                                {{ $preRegStatus }}
                                            </span>
                                            @if($event->allow_pre_registration)
                                                <button onclick="showPreRegVolunteers({{ $event->id }})"
                                                    class="mt-1 text-xs text-blue-600 hover:text-blue-800">
                                                    View Pre-registered
                                                </button>
                                            @endif
                                        </td>

                                        <!-- Actions Column -->
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button onclick="openEditModal({{ $event->id }})"
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors duration-150">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button onclick="archiveEvent({{ $event->id }})"
                                                    class="text-orange-600 hover:text-orange-900 p-1 rounded transition-colors duration-150">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 8l4 4 4-4m0 6l-4-4-4 4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- pre preg modal -->

        <div id="preRegModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Pre-registered Volunteers</h3>
                        <button onclick="closePreRegModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="preRegSearch" placeholder="Search volunteers..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div class="h-64 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Volunteer
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ministry
                                    </th>
                                    @if(auth()->user()->isAdmin())
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="preRegTableBody">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button onclick="closePreRegModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Event Modal -->
        <div id="addModal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-start justify-center py-4">
            <div class="relative w-full max-w-3xl mx-auto my-4">
                <div
                    class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
                    <!-- Header with gradient background -->
                    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">Create New Event</h3>
                                    <p class="text-blue-100 text-sm">Fill in the details below to add your event</p>
                                </div>
                            </div>
                            <button onclick="closeAddModal()"
                                class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-xl transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-8">
                        <form id="addEventForm" class="space-y-8">
                            <!-- Event Information Card -->
                            <div
                                class="bg-gradient-to-br from-gray-50 to-blue-50/30 p-6 rounded-2xl border border-gray-100">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                    </div>
                                    <h4 class="text-xl font-semibold text-gray-800">Event Information</h4>
                                </div>

                                <div class="space-y-6">
                                    <!-- Event Title -->
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            Event Title
                                            <span class="text-red-500 ml-1">*</span>
                                            <div
                                                class="w-1 h-1 bg-blue-500 rounded-full ml-2 opacity-0 group-focus-within:opacity-100 transition-opacity">
                                            </div>
                                        </label>
                                        <input type="text" id="addTitle"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300"
                                            placeholder="Enter a compelling event title" required>
                                    </div>

                                    <!-- Date and Time Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="group">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                Date
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <input type="date" id="addDate"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300"
                                                required>
                                        </div>

                                        <div class="group">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Start Time <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <input type="time" id="addStartTime"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300"
                                                required>
                                        </div>

                                        <div class="group">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                End Time <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <input type="time" id="addEndTime"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                                        <textarea id="addDescription" rows="4"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 resize-none"
                                            placeholder="Describe your event in detail..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Settings Card -->
                            <div
                                class="bg-gradient-to-br from-indigo-50 to-purple-50/30 p-6 rounded-2xl border border-indigo-100">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                            </path>
                                        </svg>
                                    </div>
                                    <h4 class="text-xl font-semibold text-gray-800">Registration Settings</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Allow
                                            Pre-registration</label>
                                        <select id="addAllowPreRegistration"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 bg-white hover:border-gray-300">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pre-registration
                                            Deadline</label>
                                        <input type="datetime-local" id="addPreRegistrationDeadline"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 bg-white hover:border-gray-300">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                                <button type="button" onclick="addEvent()"
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 px-8 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 font-semibold text-lg shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 transform hover:scale-[1.02]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span>Create Event</span>
                                </button>
                                <button type="button" onclick="closeAddModal()"
                                    class="flex-1 bg-gray-100 text-gray-700 py-4 px-8 rounded-xl hover:bg-gray-200 focus:ring-4 focus:ring-gray-500/20 transition-all duration-200 font-semibold text-lg flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Cancel</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Event Modal -->
        <div id="editModal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-start justify-center py-4">
            <div class="relative w-full max-w-3xl mx-auto my-4">
                <div
                    class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
                    <!-- Header with gradient background -->
                    <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-teal-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">Edit Event</h3>
                                    <p class="text-green-100 text-sm">Update your event details</p>
                                </div>
                            </div>
                            <button onclick="closeEditModal()"
                                class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-xl transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-8">
                        <form id="editEventForm" class="space-y-8">
                            <input type="hidden" id="editEventId">

                            <!-- Event Information Card -->
                            <div
                                class="bg-gradient-to-br from-gray-50 to-emerald-50/30 p-6 rounded-2xl border border-gray-100">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                    </div>
                                    <h4 class="text-xl font-semibold text-gray-800">Event Information</h4>
                                </div>

                                <div class="space-y-6">
                                    <!-- Event Title -->
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            Event Title
                                            <span class="text-red-500 ml-1">*</span>
                                            <div
                                                class="w-1 h-1 bg-emerald-500 rounded-full ml-2 opacity-0 group-focus-within:opacity-100 transition-opacity">
                                            </div>
                                        </label>
                                        <input type="text" id="editTitle"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 bg-white hover:border-gray-300"
                                            placeholder="Enter a compelling event title" required>
                                    </div>

                                    <!-- Date and Time Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="group">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                Date
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <input type="date" id="editDate"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 bg-white hover:border-gray-300"
                                                required>
                                        </div>

                                        <div class="group">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Start Time <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <input type="time" id="editStartTime"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 bg-white hover:border-gray-300"
                                                required>
                                        </div>

                                        <div class="group">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                End Time <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <input type="time" id="editEndTime"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 bg-white hover:border-gray-300"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                                        <textarea id="editDescription" rows="4"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 bg-white hover:border-gray-300 resize-none"
                                            placeholder="Describe your event in detail..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Settings Card -->
                            <div
                                class="bg-gradient-to-br from-teal-50 to-cyan-50/30 p-6 rounded-2xl border border-teal-100">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                            </path>
                                        </svg>
                                    </div>
                                    <h4 class="text-xl font-semibold text-gray-800">Registration Settings</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Allow
                                            Pre-registration</label>
                                        <select id="editAllowPreRegistration"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-200 bg-white hover:border-gray-300">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pre-registration
                                            Deadline</label>
                                        <input type="datetime-local" id="editPreRegistrationDeadline"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-200 bg-white hover:border-gray-300">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                                <button type="button" onclick="updateEvent()"
                                    class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-4 px-8 rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:ring-4 focus:ring-emerald-500/20 transition-all duration-200 font-semibold text-lg shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 transform hover:scale-[1.02]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Update Event</span>
                                </button>
                                <button type="button" onclick="closeEditModal()"
                                    class="flex-1 bg-gray-100 text-gray-700 py-4 px-8 rounded-xl hover:bg-gray-200 focus:ring-4 focus:ring-gray-500/20 transition-all duration-200 font-semibold text-lg flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Cancel</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Check Modal -->
        <div id="attendanceModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-start justify-center py-4">
            <div
                class="relative w-11/12 md:w-3/5 lg:w-1/2 mx-auto my-4 p-6 border shadow-lg rounded-2xl bg-white max-w-4xl">
                <div class="mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Attendance Management</h3>
                                <p class="text-sm text-gray-500">Manage attendance for this event</p>
                            </div>
                        </div>
                        <button onclick="closeAttendanceModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200 hover:bg-gray-100 p-2 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Search and Add Volunteer Section -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1 relative">
                                <input type="text" id="volunteerSearch" placeholder="Search volunteers to add..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <div id="searchLoading" class="absolute right-3 top-2.5 hidden">
                                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Search Results -->
                        <div id="searchResults"
                            class="mt-4 hidden max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody id="searchResultsBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Quick Actions -->
                    <div class="mb-6 flex flex-wrap gap-2">
                        <button type="button" onclick="markAllAs('present')"
                            class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium hover:bg-green-200 transition-all duration-200">
                            Mark All Present
                        </button>
                        <button type="button" onclick="markAllAs('absent')"
                            class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium hover:bg-red-200 transition-all duration-200">
                            Mark All Absent
                        </button>
                        <button type="button" onclick="clearAllAttendance()"
                            class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium hover:bg-gray-200 transition-all duration-200">
                            Clear All
                        </button>
                    </div>

                    <!-- Attendance Summary -->
                    <div class="mb-6 grid grid-cols-3 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600" id="presentCount">0</div>
                            <div class="text-sm text-green-600">Present</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-red-600" id="absentCount">0</div>
                            <div class="text-sm text-red-600">Absent</div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-blue-600" id="totalCount">0</div>
                            <div class="text-sm text-blue-600">Total Volunteers</div>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Attendee</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ministry</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Attendance</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="attendanceTableBody">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 pt-6 justify-end border-t border-gray-200 mt-6">
                        <button type="button" onclick="saveAttendance()"
                            class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Save Attendance
                        </button>
                        <button type="button" onclick="closeAttendanceModal()"
                            class="bg-gray-300 text-gray-700 py-2 px-6 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Modal -->
        <div id="alertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-1/4 mx-auto p-6 border w-96 shadow-lg rounded-xl bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div id="alertIcon" class="mr-3">
                                <!-- Icon will be inserted here by JavaScript -->
                            </div>
                            <h3 id="alertTitle" class="text-lg font-semibold text-gray-900">Alert</h3>
                        </div>
                        <button onclick="closeAlertModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mb-6">
                        <p id="alertMessage" class="text-gray-600">Message goes here</p>
                    </div>
                    <div class="flex justify-end">
                        <button onclick="closeAlertModal()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-1/4 mx-auto p-6 border w-96 shadow-lg rounded-xl bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="mr-3 p-2 bg-yellow-100 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900">Confirm Action</h3>
                        </div>
                        <button onclick="closeConfirmModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mb-6">
                        <p id="confirmMessage" class="text-gray-600">Are you sure you want to perform this action?</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button onclick="closeConfirmModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            Cancel
                        </button>
                        <button id="confirmActionButton" onclick="confirmAction()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Archive Reason Modal -->
        <div id="archiveReasonModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-start justify-center py-4">
            <div class="relative w-full max-w-md mx-auto my-4 p-6 border shadow-lg rounded-2xl bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">Archive Event</h3>
                        <button onclick="closeArchiveReasonModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Archiving *</label>
                        <textarea id="archiveReason" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Enter the reason for archiving this event..." required></textarea>
                    </div>

                    <div class="flex space-x-3 pt-6 border-t border-gray-200">
                        <button type="button" onclick="confirmArchive()"
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                            Archive Event
                        </button>
                        <button type="button" onclick="closeArchiveReasonModal()"
                            class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Archive Reason Modal -->
    <div id="archiveReasonModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-md shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Archive Event</h3>
                    <button onclick="closeArchiveReasonModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Archiving *</label>
                    <textarea id="archiveReason" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Enter the reason for archiving this event..." required></textarea>
                </div>

                <div class="flex space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="confirmArchive()"
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                        Archive Event
                    </button>
                    <button type="button" onclick="closeArchiveReasonModal()"
                        class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // Search Volunteers
        let searchTimeout;
        document.getElementById('volunteerSearch').addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = this.value.trim();
                if (searchTerm.length >= 1) {
                    searchVolunteers();
                } else {
                    document.getElementById('searchResults').classList.add('hidden');
                }
            }, 300);
        });

        function searchVolunteers() {
            const searchTerm = document.getElementById('volunteerSearch').value.trim();
            const eventId = document.getElementById('attendanceModal').dataset.eventId;

            fetch(`/events/volunteers/search?q=${encodeURIComponent(searchTerm)}&event_id=${eventId}`, {
                headers: {
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const resultsBody = document.getElementById('searchResultsBody');
                    resultsBody.innerHTML = '';

                    if (!Array.isArray(data)) {
                        throw new Error('Invalid data format received');
                    }

                    if (data.length === 0) {
                        resultsBody.innerHTML = '<tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">No volunteers found</td></tr>';
                    } else {
                        data.forEach(volunteer => {
                            const row = document.createElement('tr');
                            row.className = 'hover:bg-gray-50';
                            row.innerHTML = `
                                                                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                                                                        <div class="flex items-center">
                                                                                                            <img class="w-8 h-8 rounded-full mr-3" src="${volunteer.profile_picture_url || '/images/default-profile.png'}" alt="${volunteer.full_name}">
                                                                                                            <div>
                                                                                                                <div class="text-sm font-medium text-gray-900">${volunteer.full_name}</div>
                                                                                                                <div class="text-xs text-gray-500">${volunteer.ministry_name}</div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                                                                                        <button onclick="addVolunteerToEvent(${volunteer.id})" class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                                                                                            Add
                                                                                                        </button>
                                                                                                    </td>
                                                                                                `;
                            resultsBody.appendChild(row);
                        });
                    }

                    document.getElementById('searchResults').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error searching volunteers:', error);
                    toastr.error('Error searching volunteers. Please try again.');
                    document.getElementById('searchResults').classList.add('hidden');
                });
        }

        // Search Functionality
        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#eventsTableBody tr');

            rows.forEach(row => {
                const title = row.querySelector('td:first-child .text-sm.font-medium').textContent.toLowerCase();
                const description = row.querySelector('td:first-child .text-sm.text-gray-500').textContent.toLowerCase();

                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter Functionality
        document.getElementById('eventFilter').addEventListener('change', function () {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#eventsTableBody tr');
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

            rows.forEach(row => {
                const dateStr = row.querySelector('td:nth-child(2)').textContent;
                // Parse the displayed date (e.g., "Jan 1, 2023")
                const eventDate = new Date(dateStr);
                const isArchived = row.classList.contains('archived');

                let shouldShow = true;

                switch (filterValue) {
                    case 'upcoming':
                        shouldShow = eventDate >= today;
                        break;
                    case 'past':
                        shouldShow = eventDate < today;
                        break;
                    case 'archived':
                        shouldShow = isArchived;
                        break;
                    // 'all' shows everything
                }

                row.style.display = shouldShow ? '' : 'none';
            });
        });

        // Add Volunteer to Event
        function addVolunteerToEvent(volunteerId) {
            const eventId = document.getElementById('attendanceModal').dataset.eventId;

            fetch(`/events/${eventId}/volunteers/${volunteerId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        // Refresh the attendance list
                        openAttendanceModal(eventId);
                        document.getElementById('searchResults').classList.add('hidden');
                        document.getElementById('volunteerSearch').value = '';
                    } else {
                        toastr.error(data.message || 'Error adding volunteer');
                    }
                })
                .catch(error => {
                    console.error('Error adding volunteer:', error);
                    toastr.error('Error adding volunteer');
                });
        }

        // Remove Volunteer from Event
        function removeVolunteerFromEvent(volunteerId) {
            if (!confirm('Are you sure you want to remove this volunteer from the event?')) {
                return;
            }

            const eventId = document.getElementById('attendanceModal').dataset.eventId;

            fetch(`/events/${eventId}/volunteers/${volunteerId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        // Refresh the attendance list
                        openAttendanceModal(eventId);
                    } else {
                        toastr.error(data.message || 'Error removing volunteer');
                    }
                })
                .catch(error => {
                    console.error('Error removing volunteer:', error);
                    toastr.error('Error removing volunteer');
                });
        }

        // Search Functionality
        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#eventsTableBody tr');

            rows.forEach(row => {
                const title = row.querySelector('td:first-child .text-sm.font-medium').textContent.toLowerCase();
                const description = row.querySelector('td:first-child .text-sm.text-gray-500').textContent.toLowerCase();

                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter Functionality
        document.getElementById('eventFilter').addEventListener('change', function () {
            const filterValue = this.value;
            const rows = document.querySelectorAll('#eventsTableBody tr');
            const now = new Date();

            rows.forEach(row => {
                const dateStr = row.querySelector('td:nth-child(2)').textContent;
                const eventDate = new Date(dateStr);
                const isArchived = row.classList.contains('archived');

                let shouldShow = true;

                switch (filterValue) {
                    case 'upcoming':
                        shouldShow = eventDate >= now;
                        break;
                    case 'past':
                        shouldShow = eventDate < now;
                        break;
                    case 'archived':
                        shouldShow = isArchived;
                        break;
                    // 'all' shows everything
                }

                row.style.display = shouldShow ? '' : 'none';
            });
        });

        // Modal Functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('addDate').value = today;
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('addEventForm').reset();
        }

        function openEditModal(eventId) {
            fetch(`/events/${eventId}`)
                .then(response => response.json())
                .then(event => {
                    // Format date for input type="date"
                    const eventDate = new Date(event.date);
                    const formattedDate = eventDate.toISOString().split('T')[0];

                    // Populate form fields
                    document.getElementById('editEventId').value = event.id;
                    document.getElementById('editTitle').value = event.title;
                    document.getElementById('editDate').value = formattedDate;
                    document.getElementById('editStartTime').value = event.start_time;
                    document.getElementById('editEndTime').value = event.end_time;
                    document.getElementById('editDescription').value = event.description || '';
                    document.getElementById('editAllowPreRegistration').value = event.allow_pre_registration ? '1' : '0';
                    if (event.pre_registration_deadline) {
                        const deadline = new Date(event.pre_registration_deadline);
                        const formattedDeadline = deadline.toISOString().slice(0, 16);
                        document.getElementById('editPreRegistrationDeadline').value = formattedDeadline;
                    } else {
                        document.getElementById('editPreRegistrationDeadline').value = '';
                    }

                    // Show modal
                    document.getElementById('editModal').classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                })
                .catch(error => {
                    console.error('Error fetching event:', error);
                    toastr.error('Error loading event data');
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openAttendanceModal(eventId) {
            fetch(`/events/${eventId}/volunteers`)
                .then(response => response.json())
                .then(volunteers => {
                    const tableBody = document.getElementById('attendanceTableBody');
                    tableBody.innerHTML = '';

                    volunteers.forEach(volunteer => {
                        const row = document.createElement('tr');
                        row.className = 'attendance-row hover:bg-gray-50 transition-colors duration-150';
                        row.dataset.volunteerId = volunteer.id;

                        row.innerHTML = `
                                                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                                                    <div class="flex items-center">
                                                                                                        <img class="w-10 h-10 rounded-full mr-3 object-cover" 
                                                                                                             src="${volunteer.profile_picture_url}" 
                                                                                                             alt="${volunteer.full_name}">
                                                                                                        <div>
                                                                                                            <div class="text-sm font-medium text-gray-900">${volunteer.full_name}</div>
                                                                                                            <div class="text-xs text-gray-500">ID: ${volunteer.id}</div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                                                                        ${volunteer.ministry_name}
                                                                                                    </span>
                                                                                                </td>
                                                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                                                    <select class="attendance-select px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" 
                                                                                                            data-volunteer-id="${volunteer.id}"
                                                                                                            onchange="updateAttendanceSummary()">
                                                                                                        <option value="">Select Status</option>
                                                                                                        <option value="present" ${volunteer.pivot.attendance_status === 'present' ? 'selected' : ''}>✅ Present</option>
                                                                                                        <option value="absent" ${volunteer.pivot.attendance_status === 'absent' ? 'selected' : ''}>❌ Absent</option>
                                                                                                    </select>
                                                                                                </td>
                                                                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                                                                    <button onclick="removeVolunteerFromEvent(${volunteer.id})" class="text-red-600 hover:text-red-900 p-1 rounded transition-colors duration-150">
                                                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </td>
                                                                                            `;

                        tableBody.appendChild(row);
                    });

                    // Update summary counts
                    updateAttendanceSummary();

                    // Show modal
                    document.getElementById('attendanceModal').classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                    document.getElementById('attendanceModal').dataset.eventId = eventId;
                })
                .catch(error => {
                    console.error('Error fetching volunteers:', error);
                    toastr.error('Error loading volunteer data');
                });
        }

        function closeAttendanceModal() {
            document.getElementById('attendanceModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Event Actions
        function addEvent() {
            const title = document.getElementById('addTitle').value;
            const date = document.getElementById('addDate').value;
            const startTime = document.getElementById('addStartTime').value;
            const endTime = document.getElementById('addEndTime').value;
            const description = document.getElementById('addDescription').value;
            const allowPreRegistration = document.getElementById('addAllowPreRegistration').value;
            const preRegistrationDeadline = document.getElementById('addPreRegistrationDeadline').value;



            if (!title || !date || !startTime || !endTime) {
                toastr.error('Please fill in all required fields');
                return;
            }

            // Prepare data
            const data = {
                title: title,
                date: date,
                start_time: startTime,
                end_time: endTime,
                description: description,
                allow_pre_registration: allowPreRegistration,
                pre_registration_deadline: preRegistrationDeadline,
                _token: "{{ csrf_token() }}"
            };

            // Send AJAX request
            fetch("{{ route('events.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        closeAddModal();
                        // Reload the page to show the new event
                        window.location.reload();
                    } else {
                        toastr.error(data.message || 'Error adding event');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error adding event');
                });
        }

        function updateEvent() {
            const eventId = document.getElementById('editEventId').value;
            const title = document.getElementById('editTitle').value;
            const date = document.getElementById('editDate').value;
            const startTime = document.getElementById('editStartTime').value;
            const endTime = document.getElementById('editEndTime').value;
            const description = document.getElementById('editDescription').value;
            const allowPreRegistration = document.getElementById('editAllowPreRegistration').value;
            const preRegistrationDeadline = document.getElementById('editPreRegistrationDeadline').value;

            if (!title || !date || !startTime || !endTime) {
                toastr.error('Please fill in all required fields');
                return;
            }

            // Prepare data
            const data = {
                title: title,
                date: date,
                start_time: startTime,
                end_time: endTime,
                description: description,
                allow_pre_registration: allowPreRegistration,
                pre_registration_deadline: preRegistrationDeadline,
                _token: "{{ csrf_token() }}"
            };

            // Send AJAX request
            fetch(`/events/${eventId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        closeEditModal();
                        // Reload the page to show the updated event
                        window.location.reload();
                    } else {
                        toastr.error(data.message || 'Error updating event');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error updating event');
                });
        }

        // Archive Event
        function archiveEvent(eventId) {
            // Store the event ID in the modal
            document.getElementById('archiveReasonModal').dataset.eventId = eventId;
            // Clear any previous reason
            document.getElementById('archiveReason').value = '';
            // Show the modal
            document.getElementById('archiveReasonModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeArchiveReasonModal() {
            document.getElementById('archiveReasonModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function confirmArchive() {
            const eventId = document.getElementById('archiveReasonModal').dataset.eventId;
            const reason = document.getElementById('archiveReason').value.trim();

            if (!reason) {
                toastr.error('Please provide a reason for archiving');
                return;
            }

            fetch(`/events/${eventId}/archive`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        closeArchiveReasonModal();
                        window.location.reload();
                    } else {
                        toastr.error(data.message || 'Error archiving event');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error archiving event');
                });
        }
        // Close modals when clicking outside
        document.addEventListener('click', function (event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const attendanceModal = document.getElementById('attendanceModal');
            const archiveReasonModal = document.getElementById('archiveReasonModal');

            if (event.target === addModal) {
                closeAddModal();
            }

            if (event.target === editModal) {
                closeEditModal();
            }

            if (event.target === attendanceModal) {
                closeAttendanceModal();
            }

            if (event.target === archiveReasonModal) {
                closeArchiveReasonModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeAttendanceModal();
                closeArchiveReasonModal();
            }
        });
        // Attendance Functions
        function updateAttendanceSummary() {
            const selects = document.querySelectorAll('.attendance-select');
            const counts = {
                present: 0,
                absent: 0,
                total: selects.length
            };

            selects.forEach(select => {
                if (select.value === 'present') counts.present++;
                if (select.value === 'absent') counts.absent++;
            });

            document.getElementById('presentCount').textContent = counts.present;
            document.getElementById('absentCount').textContent = counts.absent;
            document.getElementById('totalCount').textContent = counts.total;
        }

        function markAllAs(status) {
            const selects = document.querySelectorAll('.attendance-select');
            selects.forEach(select => {
                select.value = status;
            });
            updateAttendanceSummary();
        }

        function clearAllAttendance() {
            const selects = document.querySelectorAll('.attendance-select');
            selects.forEach(select => {
                select.value = '';
            });
            updateAttendanceSummary();
        }

        function saveAttendance() {
            const eventId = document.getElementById('attendanceModal').dataset.eventId;
            const attendance = {};

            document.querySelectorAll('.attendance-select').forEach(select => {
                const volunteerId = select.dataset.volunteerId;
                attendance[volunteerId] = select.value;
            });

            fetch(`/events/${eventId}/attendance/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    attendance: attendance,
                    _token: "{{ csrf_token() }}"
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        closeAttendanceModal();
                    } else {
                        toastr.error(data.message || 'Error saving attendance');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error saving attendance');
                });
        }

        // Close modals when clicking outside
        document.addEventListener('click', function (event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const attendanceModal = document.getElementById('attendanceModal');

            if (event.target === addModal) {
                closeAddModal();
            }

            if (event.target === editModal) {
                closeEditModal();
            }

            if (event.target === attendanceModal) {
                closeAttendanceModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeAttendanceModal();
            }
        });

        function showPreRegVolunteers(eventId) {
            fetch(`/events/${eventId}/volunteers`)
                .then(response => response.json())
                .then(volunteers => {
                    const tableBody = document.getElementById('preRegTableBody');
                    tableBody.innerHTML = '';

                    volunteers.forEach(volunteer => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';

                        let rowContent = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full mr-3" 
                                     src="${volunteer.profile_picture_url || '/images/default-profile.png'}" 
                                     alt="${volunteer.full_name}">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">${volunteer.full_name}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${volunteer.ministry_name}
                        </td>
                    `;

                        // Add email column for admin users
                        if (volunteer.email) {
                            rowContent += `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${volunteer.email}
                            </td>
                        `;
                        }

                        row.innerHTML = rowContent;
                        tableBody.appendChild(row);
                    });

                    document.getElementById('preRegModal').classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                })
                .catch(error => {
                    console.error('Error fetching pre-registered volunteers:', error);
                    toastr.error('Error loading pre-registered volunteers');
                });
        }

        function closePreRegModal() {
            document.getElementById('preRegModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Add search functionality for pre-reg modal
        document.getElementById('preRegSearch').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#preRegTableBody tr');

            rows.forEach(row => {
                const name = row.querySelector('td:first-child .text-sm.font-medium').textContent.toLowerCase();
                const ministry = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(3)') ? row.querySelector('td:nth-child(3)').textContent.toLowerCase() : '';

                if (name.includes(searchTerm) || ministry.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection