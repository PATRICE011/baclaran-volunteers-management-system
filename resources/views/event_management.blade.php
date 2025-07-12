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
        <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Add New Event</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="addEventForm" class="space-y-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Event Details
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Event Title *</label>
                                <input type="text" id="addTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter event title" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                                <input type="date" id="addDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                                    <input type="time" id="addStartTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                                    <input type="time" id="addEndTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="addDescription" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Enter event description"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 pt-6 border-t border-gray-200">
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
        <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Event</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="editEventForm" class="space-y-6">
                    <input type="hidden" id="editEventId">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Event Details
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Event Title *</label>
                                <input type="text" id="editTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter event title" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                                <input type="date" id="editDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                                    <input type="time" id="editStartTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                                    <input type="time" id="editEndTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="editDescription" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Enter event description"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 pt-6 border-t border-gray-200">
                        <button type="button" onclick="updateEvent()" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 font-medium">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Event
                        </button>
                        <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-400 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 font-medium">
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
                            <!-- Will be populated by JavaScript -->
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
        // Fetch event data
        fetch(`/events/${eventId}`)
            .then(response => response.json())
            .then(event => {
                // Populate form fields
                document.getElementById('editEventId').value = event.id;
                document.getElementById('editTitle').value = event.title;
                document.getElementById('editDate').value = event.date;
                document.getElementById('editStartTime').value = event.start_time;
                document.getElementById('editEndTime').value = event.end_time;
                document.getElementById('editDescription').value = event.description;

                // Show modal
                document.getElementById('editModal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            })
            .catch(error => {
                console.error('Error fetching event:', error);
                showAlert('Error loading event data', 'error');
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openAttendanceModal(eventId) {
        // Fetch volunteers for this event
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
                showAlert('Error loading volunteer data', 'error');
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

        if (!title || !date || !startTime || !endTime) {
            showAlert('Please fill in all required fields', 'error');
            return;
        }

        // Prepare data
        const data = {
            title: title,
            date: date,
            start_time: startTime,
            end_time: endTime,
            description: description,
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
                showAlert('Event added successfully!', 'success');
                closeAddModal();
                // Reload the page to show the new event
                window.location.reload();
            } else {
                showAlert(data.message || 'Error adding event', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error adding event', 'error');
        });
    }

    function updateEvent() {
        const eventId = document.getElementById('editEventId').value;
        const title = document.getElementById('editTitle').value;
        const date = document.getElementById('editDate').value;
        const startTime = document.getElementById('editStartTime').value;
        const endTime = document.getElementById('editEndTime').value;
        const description = document.getElementById('editDescription').value;

        if (!title || !date || !startTime || !endTime) {
            showAlert('Please fill in all required fields', 'error');
            return;
        }

        // Prepare data
        const data = {
            title: title,
            date: date,
            start_time: startTime,
            end_time: endTime,
            description: description,
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
                showAlert('Event updated successfully!', 'success');
                closeEditModal();
                // Reload the page to show the updated event
                window.location.reload();
            } else {
                showAlert(data.message || 'Error updating event', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error updating event', 'error');
        });
    }

    function archiveEvent(eventId) {
        if (confirm('Are you sure you want to archive this event?')) {
            fetch(`/events/${eventId}/archive`, {
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
                    showAlert('Event archived successfully!', 'success');
                    window.location.reload();
                } else {
                    showAlert(data.message || 'Error archiving event', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error archiving event', 'error');
            });
        }
    }

    // Attendance Functions
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
                showAlert('Attendance saved successfully!', 'success');
                closeAttendanceModal();
            } else {
                showAlert(data.message || 'Error saving attendance', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error saving attendance', 'error');
        });
    }

    // Alert System
    function showAlert(message, type = 'info') {
        const toastrType = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        }[type] || 'info';

        toastr[toastrType](message, '', {
            positionClass: 'toast-top-right',
            timeOut: 3000
        });
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
</script>

@endsection