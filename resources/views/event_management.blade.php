@extends('components.layout')
@section('title','Events')
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
                        <button onclick="openAddModal()" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Event
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                                <input type="text" id="searchInput" placeholder="Search events..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <select id="eventFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all">All Events</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="past">Past</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendees</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="eventsTableBody">
                            @foreach($events as $event)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $event->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $event->date->format('M j, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex -space-x-2">
                                        @foreach($event->volunteers->take(3) as $volunteer)
                                        <img class="w-8 h-8 rounded-full border-2 border-white"
                                            src="{{ $volunteer->profile_picture_url }}"
                                            alt="{{ $volunteer->detail->full_name ?? 'Volunteer' }}">
                                        @endforeach
                                        @if($event->volunteers_count > 3)
                                        <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">+{{ $event->volunteers_count - 3 }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ $status }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="openEditModal({{ $event->id }})" class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors duration-150">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="openAttendanceModal({{ $event->id }})" class="text-purple-600 hover:text-purple-900 p-1 rounded transition-colors duration-150">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="archiveEvent({{ $event->id }})" class="text-orange-600 hover:text-orange-900 p-1 rounded transition-colors duration-150">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 6l-4-4-4 4"></path>
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

    <!-- Add Event Modal -->
    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-6xl shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Add New Event</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="addEventForm" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Event Details -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Event Details
                            </h4>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                                    <input type="text" id="addTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter event title">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                    <input type="date" id="addDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                        <input type="time" id="addStartTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                        <input type="time" id="addEndTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea id="addDescription" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Enter event description"></textarea>
                                </div>
                                <!-- <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ministry</label>
                                    <select id="addMinistry" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select Ministry</option>
                                        @foreach($ministries as $ministry)
                                        <option value="{{ $ministry->id }}">{{ $ministry->ministry_name }}</option>
                                        @endforeach
                                    </select>
                                </div> -->
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Attendees -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 mb-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    Attendees
                                </div>
                                <span id="attendeeCount" class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">0 selected</span>
                            </h4>

                            <!-- Search Bar -->
                            <div class="relative mb-4">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" id="attendeeSearch" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Search attendees..." oninput="filterAttendees()">
                            </div>

                            <!-- Ministry Filter -->
                            <div class="mb-4">
                                <select id="ministryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" onchange="filterAttendees()">
                                    <option value="">All Ministries</option>
                                    @foreach($ministries as $ministry)
                                    <option value="{{ $ministry->id }}">{{ $ministry->ministry_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Attendees List -->
                            <div class="max-h-80 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-white" style="scrollbar-width: thin;">
                                <div id="attendeesList">
                                    <!-- Original attendees with updated styling -->
                                    <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" data-name="John Doe" data-ministry="Worship">
                                        <input type="checkbox" id="attendee1" name="attendees[]" value="1" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="updateAttendeeCount()">
                                        <label for="attendee1" class="flex items-center flex-1 cursor-pointer">
                                            <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="John Doe">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">John Doe</div>
                                                <div class="text-xs text-gray-500">Ministry: Worship</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" data-name="Jane Smith" data-ministry="Hospitality">
                                        <input type="checkbox" id="attendee2" name="attendees[]" value="2" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="updateAttendeeCount()">
                                        <label for="attendee2" class="flex items-center flex-1 cursor-pointer">
                                            <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Jane Smith">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                                <div class="text-xs text-gray-500">Ministry: Hospitality</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" data-name="Robert Johnson" data-ministry="Ushering">
                                        <input type="checkbox" id="attendee3" name="attendees[]" value="3" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="updateAttendeeCount()">
                                        <label for="attendee3" class="flex items-center flex-1 cursor-pointer">
                                            <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/men/62.jpg" alt="Robert Johnson">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">Robert Johnson</div>
                                                <div class="text-xs text-gray-500">Ministry: Ushering</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" data-name="Sarah Williams" data-ministry="Children">
                                        <input type="checkbox" id="attendee4" name="attendees[]" value="4" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="updateAttendeeCount()">
                                        <label for="attendee4" class="flex items-center flex-1 cursor-pointer">
                                            <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Sarah Williams">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">Sarah Williams</div>
                                                <div class="text-xs text-gray-500">Ministry: Children</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- No results message -->
                                <div id="noResults" class="text-center py-8 text-gray-500 hidden">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm">No attendees found</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons - Full Width -->
                    <div class="lg:col-span-2 flex space-x-3 pt-6 border-t border-gray-200">
                        <button type="button" onclick="addEvent()" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Event
                        </button>
                        <button type="button" onclick="closeAddModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Event Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <!-- just like an add modal but field with current data to edit -->
        <div class="relative top-10 mx-auto p-6 border w-full max-w-6xl shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Add New Event</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="addEventForm" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Event Details -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Event Details
                            </h4>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                                    <input type="text" id="addTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter event title">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                    <input type="date" id="addDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                        <input type="time" id="addStartTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                        <input type="time" id="addEndTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea id="addDescription" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Enter event description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Attendees -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 mb-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    Attendees
                                </div>
                                <span id="attendeeCount" class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">0 selected</span>
                            </h4>

                            <!-- Search Bar -->
                            <div class="relative mb-4">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" id="attendeeSearch" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Search attendees..." oninput="filterAttendees()">
                            </div>

                            <!-- Ministry Filter -->
                            <div class="mb-4">
                                <select id="ministryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" onchange="filterAttendees()">
                                    <option value="">All Ministries</option>
                                    @foreach($ministries as $ministry)
                                    <option value="{{ $ministry->id }}">{{ $ministry->ministry_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Attendees List -->
                            <div class="max-h-80 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-white" style="scrollbar-width: thin;">
                                <div id="attendeesList">
                                    <!-- Original attendees with updated styling -->
                                    <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" data-name="John Doe" data-ministry="Worship">
                                        <input type="checkbox" id="attendee1" name="attendees[]" value="1" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="updateAttendeeCount()">
                                        <label for="attendee1" class="flex items-center flex-1 cursor-pointer">
                                            <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="John Doe">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">John Doe</div>
                                                <div class="text-xs text-gray-500">Ministry: Worship</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" data-name="Jane Smith" data-ministry="Hospitality">
                                        <input type="checkbox" id="attendee2" name="attendees[]" value="2" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="updateAttendeeCount()">
                                        <label for="attendee2" class="flex items-center flex-1 cursor-pointer">
                                            <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Jane Smith">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                                <div class="text-xs text-gray-500">Ministry: Hospitality</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" data-name="Robert Johnson" data-ministry="Ushering">
                                        <input type="checkbox" id="attendee3" name="attendees[]" value="3" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="updateAttendeeCount()">
                                        <label for="attendee3" class="flex items-center flex-1 cursor-pointer">
                                            <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/men/62.jpg" alt="Robert Johnson">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">Robert Johnson</div>
                                                <div class="text-xs text-gray-500">Ministry: Ushering</div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" data-name="Sarah Williams" data-ministry="Children">
                                        <input type="checkbox" id="attendee4" name="attendees[]" value="4" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="updateAttendeeCount()">
                                        <label for="attendee4" class="flex items-center flex-1 cursor-pointer">
                                            <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Sarah Williams">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">Sarah Williams</div>
                                                <div class="text-xs text-gray-500">Ministry: Children</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- No results message -->
                                <div id="noResults" class="text-center py-8 text-gray-500 hidden">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm">No attendees found</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons - Full Width -->
                    <div class="lg:col-span-2 flex space-x-3 pt-6 border-t border-gray-200">
                        <button type="button" onclick="addEvent()" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Event
                        </button>
                        <button type="button" onclick="closeAddModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Check Modal -->
    <div id="attendanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative my-8 mx-auto p-6 border w-11/12 md:w-3/5 lg:w-1/2 shadow-lg rounded-2xl bg-white max-w-4xl">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Attendance Check</h3>
                            <p class="text-sm text-gray-500">Mark attendance for event attendees</p>
                        </div>
                    </div>
                    <button onclick="closeAttendanceModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 hover:bg-gray-100 p-2 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Quick Actions -->
                <div class="mb-6 flex flex-wrap gap-2">
                    <button type="button" onclick="markAllAs('present')" class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium hover:bg-green-200 transition-all duration-200">
                        Mark All Present
                    </button>
                    <button type="button" onclick="markAllAs('absent')" class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium hover:bg-red-200 transition-all duration-200">
                        Mark All Absent
                    </button>
                    <button type="button" onclick="clearAllAttendance()" class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium hover:bg-gray-200 transition-all duration-200">
                        Clear All
                    </button>
                </div>

                <!-- Attendance Summary -->
                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600" id="presentCount">0</div>
                        <div class="text-sm text-green-600">Present</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-red-600" id="absentCount">0</div>
                        <div class="text-sm text-red-600">Absent</div>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ministry</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="attendanceTableBody">
                            <tr class="attendance-row hover:bg-gray-50 transition-colors duration-150" data-name="John Doe" data-ministry="Worship">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="John Doe">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">John Doe</div>
                                            <div class="text-xs text-gray-500">ID: 001</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Worship</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select class="attendance-select px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="updateAttendanceSummary()">
                                        <option value="">Select Status</option>
                                        <option value="present">✅ Present</option>
                                        <option value="absent">❌ Absent</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="attendance-row hover:bg-gray-50 transition-colors duration-150" data-name="Jane Smith" data-ministry="Hospitality">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Jane Smith">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                            <div class="text-xs text-gray-500">ID: 002</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800">Hospitality</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select class="attendance-select px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="updateAttendanceSummary()">
                                        <option value="">Select Status</option>
                                        <option value="present">✅ Present</option>
                                        <option value="absent">❌ Absent</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="attendance-row hover:bg-gray-50 transition-colors duration-150" data-name="Robert Johnson" data-ministry="Ushering">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/men/62.jpg" alt="Robert Johnson">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Robert Johnson</div>
                                            <div class="text-xs text-gray-500">ID: 003</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Ushering</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select class="attendance-select px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="updateAttendanceSummary()">
                                        <option value="">Select Status</option>
                                        <option value="present">✅ Present</option>
                                        <option value="absent">❌ Absent</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="attendance-row hover:bg-gray-50 transition-colors duration-150" data-name="Sarah Williams" data-ministry="Children">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="w-10 h-10 rounded-full mr-3 object-cover" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Sarah Williams">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Sarah Williams</div>
                                            <div class="text-xs text-gray-500">ID: 004</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Children</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select class="attendance-select px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" onchange="updateAttendanceSummary()">
                                        <option value="">Select Status</option>
                                        <option value="present">✅ Present</option>
                                        <option value="absent">❌ Absent</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-6 justify-end border-t border-gray-200 mt-6">
                    <button type="button" onclick="saveAttendance()" class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Attendance
                    </button>
                    <button type="button" onclick="closeAttendanceModal()" class="bg-gray-300 text-gray-700 py-2 px-6 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>
<script>
    // Add this global variable at the top of the script
    let selectedVolunteers = new Set();

    // Update fetchVolunteers function
    function fetchVolunteers(search = '', ministry = '') {
        $.ajax({
            url: "{{ route('events.volunteers') }}",
            method: 'GET',
            data: {
                search,
                ministry
            },
            success: function(response) {
                $('#attendeesList').empty();

                if (response.length > 0) {
                    response.forEach(function(volunteer) {
                        const isChecked = selectedVolunteers.has(volunteer.id.toString());
                        $('#attendeesList').append(`
                        <div class="attendee-item flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-150" 
                             data-name="${volunteer.full_name}" 
                             data-ministry="${volunteer.ministry}">
                            <input type="checkbox" id="attendee${volunteer.id}" 
                                   name="attendees[]" value="${volunteer.id}" 
                                   class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                   onchange="updateAttendeeCount()"
                                   ${isChecked ? 'checked' : ''}>
                            <label for="attendee${volunteer.id}" class="flex items-center flex-1 cursor-pointer">
                                <img class="w-10 h-10 rounded-full mr-3 object-cover" 
                                     src="${volunteer.profile_picture}" 
                                     alt="${volunteer.full_name}">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">${volunteer.full_name}</div>
                                    <div class="text-xs text-gray-500">${volunteer.ministry}</div>
                                </div>
                            </label>
                        </div>
                    `);
                    });
                    $('#noResults').addClass('hidden');
                } else {
                    $('#noResults').removeClass('hidden');
                }
            }
        });
    }

    // Modal Functions
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        fetchVolunteers();
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('addEventForm').reset();
        selectedVolunteers.clear();
        updateAttendeeCount();
    }

    function openEditModal(eventId) {
        $.ajax({
            url: `/event/${eventId}`,
            method: 'GET',
            success: function(event) {
                // Populate form with event data
                $('#editTitle').val(event.title);
                $('#editDate').val(event.date);
                $('#editStartTime').val(event.start_time);
                $('#editEndTime').val(event.end_time);
                $('#editDescription').val(event.description);
                $('#editMinistry').val(event.ministry_id);

                // Check volunteers
                event.volunteers.forEach(volunteer => {
                    $(`#editAttendee${volunteer.id}`).prop('checked', true);
                });

                updateAttendeeCount();
                document.getElementById('editModal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        });
    }


    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('editEventForm').reset();
    }

    function openAttendanceModal(eventId) {
        // In a real app, you would fetch attendance data based on eventId
        document.getElementById('attendanceModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeAttendanceModal() {
        document.getElementById('attendanceModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function saveAttendance(eventId) {
        const attendance = {};
        $('.attendance-select').each(function() {
            const volunteerId = $(this).data('volunteer-id');
            attendance[volunteerId] = $(this).val();
        });

        $.ajax({
            url: `/event/${eventId}/attendance/save`,
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                attendance: attendance
            },
            success: function() {
                showAlert('Attendance saved successfully!', 'success');
                closeAttendanceModal();
            }
        });
    }

    // Event Actions
    function addEvent() {
        const title = document.getElementById('addTitle').value;
        const date = document.getElementById('addDate').value;
        const startTime = document.getElementById('addStartTime').value;
        const endTime = document.getElementById('addEndTime').value;
        const description = document.getElementById('addDescription').value;
        const ministryField = document.getElementById('addMinistry');
        const ministryId = ministryField ? ministryField.value : null;
        // Get selected attendees
        const attendeeCheckboxes = document.querySelectorAll('#addEventForm input[name="attendees[]"]:checked');
        const attendees = Array.from(attendeeCheckboxes).map(cb => cb.value);

        if (!title || !date || !startTime || !endTime || attendees.length === 0) {
            showAlert('Please fill in all required fields and select at least one attendee', 'error');
            return;
        }

        // Prepare data
        const data = {
            title: title,
            date: date,
            start_time: startTime,
            end_time: endTime,
            description: description,
            ministry_id: ministryId,
            volunteers: attendees,
            _token: "{{ csrf_token() }}"
        };


        // Send AJAX request
        $.ajax({
            url: "{{ route('events.store') }}",
            method: 'POST',
            data: data,
            success: function(response) {
                showAlert('Event added successfully!', 'success');
                closeAddModal();

                // Instead of reloading the page, we'll add the new event to the table
                // This prevents pagination reset and provides a smoother experience
                addEventToTable(response.event);
            },
            error: function(xhr) {
                let errorMessage = 'Error adding event';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                showAlert(errorMessage, 'error');
            }
        });
    }

    function addEventToTable(event) {
        // Format date and time
        const eventDate = new Date(event.date);
        const formattedDate = eventDate.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });

        const startTime = new Date(`1970-01-01T${event.start_time}`);
        const endTime = new Date(`1970-01-01T${event.end_time}`);

        const formattedStart = startTime.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit'
        });

        const formattedEnd = endTime.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit'
        });

        // Create status badge
        const now = new Date();
        let statusClass, status;

        if (eventDate < now) {
            statusClass = 'bg-gray-100 text-gray-800';
            status = 'Past';
        } else if (eventDate.toDateString() === now.toDateString()) {
            statusClass = 'bg-green-100 text-green-800';
            status = 'Today';
        } else {
            statusClass = 'bg-blue-100 text-blue-800';
            status = 'Upcoming';
        }

        // Create attendees avatars
        let attendeesHtml = '';
        const volunteers = event.volunteers.slice(0, 3);

        volunteers.forEach(volunteer => {
            attendeesHtml += `
            <img class="w-8 h-8 rounded-full border-2 border-white"
                src="${volunteer.profile_picture_url}"
                alt="${volunteer.detail.full_name}">
        `;
        });

        if (event.volunteers_count > 3) {
            attendeesHtml += `
            <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                +${event.volunteers_count - 3}
            </div>
        `;
        }

        // Create the new row
        const newRow = `
        <tr class="hover:bg-gray-50 transition-colors duration-150">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${event.title}</div>
                        <div class="text-sm text-gray-500">${event.description}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formattedDate}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formattedStart} - ${formattedEnd}
            </td>
            <td class="px-6 py-4">
                <div class="flex -space-x-2">
                    ${attendeesHtml}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">${status}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                    <button onclick="openEditModal(${event.id})" class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="openAttendanceModal(${event.id})" class="text-purple-600 hover:text-purple-900 p-1 rounded transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <button onclick="archiveEvent(${event.id})" class="text-orange-600 hover:text-orange-900 p-1 rounded transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 6l-4-4-4 4"></path>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `;

        // Add the new row to the top of the table
        const tableBody = document.getElementById('eventsTableBody');
        if (tableBody) {
            tableBody.insertAdjacentHTML('afterbegin', newRow);
        }

        // Update stats counters
        updateStatsCounters();
    }

    function updateStatsCounters() {
        // Update total events count
        const totalCount = document.querySelector('.bg-white.rounded-xl.shadow-sm.border.border-gray-200.p-6:nth-child(1) .text-2xl');
        if (totalCount) {
            totalCount.textContent = parseInt(totalCount.textContent) + 1;
        }

        // Update upcoming events count if applicable
        const now = new Date();
        const eventDate = new Date();
        if (eventDate >= now) {
            const upcomingCount = document.querySelector('.bg-white.rounded-xl.shadow-sm.border.border-gray-200.p-6:nth-child(2) .text-2xl');
            if (upcomingCount) {
                upcomingCount.textContent = parseInt(upcomingCount.textContent) + 1;
            }
        }

        // Update this month events count if applicable
        const currentMonth = now.getMonth();
        if (eventDate.getMonth() === currentMonth) {
            const monthCount = document.querySelector('.bg-white.rounded-xl.shadow-sm.border.border-gray-200.p-6:nth-child(3) .text-2xl');
            if (monthCount) {
                monthCount.textContent = parseInt(monthCount.textContent) + 1;
            }
        }
    }

    function filterAttendees() {
        const searchTerm = $('#attendeeSearch').val();
        const ministryFilter = $('#ministryFilter').val();
        fetchVolunteers(searchTerm, ministryFilter);
    }

    function updateAttendeeCount() {
        const checkedBoxes = document.querySelectorAll('input[name="attendees[]"]:checked');
        const count = checkedBoxes.length;
        document.getElementById('attendeeCount').textContent = `${count} selected`;

        // Update selected volunteers set
        selectedVolunteers.clear();
        checkedBoxes.forEach(checkbox => {
            selectedVolunteers.add(checkbox.value);
        });
    }

    // Initialize count on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateAttendeeCount();
    });

    function updateEvent() {
        const title = document.getElementById('editTitle').value;
        const date = document.getElementById('editDate').value;
        const startTime = document.getElementById('editStartTime').value;
        const endTime = document.getElementById('editEndTime').value;
        const description = document.getElementById('editDescription').value;

        // Get selected attendees
        const attendeeCheckboxes = document.querySelectorAll('#editEventForm input[name="attendees[]"]:checked');
        const attendees = Array.from(attendeeCheckboxes).map(cb => cb.value);

        if (!title || !date || !startTime || !endTime || attendees.length === 0) {
            showAlert('Please fill in all required fields and select at least one attendee', 'error');
            return;
        }

        // Here you would typically send the data to your Laravel backend
        // For demo purposes, we'll just show a success message
        showAlert('Event updated successfully with ' + attendees.length + ' attendees!', 'success');
        closeEditModal();
    }

    function archiveEvent(eventId) {
        if (confirm('Are you sure you want to archive this event?')) {
            $.ajax({
                url: `/event/${eventId}/archive`,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    showAlert('Event archived successfully!', 'success');
                    location.reload();
                }
            });
        }
    }

    // Alert System
    function showAlert(message, type = 'info') {
        const toastrType = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        } [type] || 'info';

        toastr[toastrType](message, '', {
            positionClass: 'toast-top-right',
            timeOut: 3000
        });
    }

    function removeAlert(alertId) {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                alertElement.remove();
            }, 300);
        }
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
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
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeAttendanceModal();
        }
    });

    // Initialize date input with today's date
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('addDate').value = today;
    });
    // attendance


    function updateAttendanceSummary() {
        const selects = document.querySelectorAll('.attendance-select');
        const counts = {
            present: 0,
            absent: 0
        };

        selects.forEach(select => {
            if (select.value === 'present') counts.present++;
            if (select.value === 'absent') counts.absent++;
        });

        document.getElementById('presentCount').textContent = counts.present;
        document.getElementById('absentCount').textContent = counts.absent;
    }

    // Mark all attendees with specific status
    function markAllAs(status) {
        const selects = document.querySelectorAll('.attendance-select');
        selects.forEach(select => {
            select.value = status;
        });
        updateAttendanceSummary();
    }

    // Clear all attendance
    function clearAllAttendance() {
        const selects = document.querySelectorAll('.attendance-select');
        const notes = document.querySelectorAll('.attendance-notes');

        selects.forEach(select => {
            select.value = '';
        });

        notes.forEach(note => {
            note.value = '';
        });

        updateAttendanceSummary();
    }

    // Toggle select all checkboxes
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.attendee-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateAttendeeCount();
        updateAttendanceSummary();
    });
</script>

@endsection