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
                        <p class="text-2xl font-bold text-gray-900">12</p>
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
                        <p class="text-2xl font-bold text-gray-900">8</p>
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
                        <p class="text-2xl font-bold text-gray-900">5</p>
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
                            <input type="text" placeholder="Search events..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>All Events</option>
                            <option>Upcoming</option>
                            <option>Past</option>
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
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Sample Event 1 -->
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Sunday Worship Service</div>
                                        <div class="text-sm text-gray-500">Weekly worship and fellowship</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">July 6, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10:00 AM - 12:00 PM</td>
                            <td class="px-6 py-4">
                                <div class="flex -space-x-2">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/32.jpg" alt="John Doe">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Jane Smith">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/62.jpg" alt="Robert Johnson">
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">+5</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Upcoming</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="openEditModal(1)" class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="openAttendanceModal(1)" class="text-purple-600 hover:text-purple-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="archiveEvent(1)" class="text-orange-600 hover:text-orange-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 6l-4-4-4 4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Sample Event 2 -->
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Bible Study Group</div>
                                        <div class="text-sm text-gray-500">Weekly bible study and discussion</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">July 9, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">7:00 PM - 9:00 PM</td>
                            <td class="px-6 py-4">
                                <div class="flex -space-x-2">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/women/68.jpg" alt="Sarah Williams">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/41.jpg" alt="Michael Brown">
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">+3</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="openEditModal(2)" class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="openAttendanceModal(2)" class="text-purple-600 hover:text-purple-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="archiveEvent(2)" class="text-orange-600 hover:text-orange-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 6l-4-4-4 4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Sample Event 3 -->
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Community Outreach</div>
                                        <div class="text-sm text-gray-500">Feeding program for the community</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">July 12, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2:00 PM - 6:00 PM</td>
                            <td class="px-6 py-4">
                                <div class="flex -space-x-2">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/women/26.jpg" alt="Emily Davis">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/22.jpg" alt="David Wilson">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/women/32.jpg" alt="Lisa Taylor">
                                    <img class="w-8 h-8 rounded-full border-2 border-white" src="https://randomuser.me/api/portraits/men/75.jpg" alt="James Moore">
                                    <div class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">+7</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Planning</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="openEditModal(3)" class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="openAttendanceModal(3)" class="text-purple-600 hover:text-purple-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="archiveEvent(3)" class="text-orange-600 hover:text-orange-900 p-1 rounded transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 6l-4-4-4 4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                                <option value="Worship">Worship</option>
                                <option value="Hospitality">Hospitality</option>
                                <option value="Ushering">Ushering</option>
                                <option value="Children">Children</option>
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
<div id="editModal" >
    <!-- just like an add modal but field with current data to edit -->
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
<!-- Alert Messages -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-4"></div>

</div>
<script>
// Modal Functions
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('addEventForm').reset();
}

function openEditModal(eventId) {
    // Sample data for demonstration
    const sampleEvents = {
        1: {
            title: 'Sunday Worship Service',
            date: '2025-07-06',
            startTime: '10:00',
            endTime: '12:00',
            description: 'Weekly worship and fellowship',
            attendees: [1, 2, 3] // IDs of attendees
        },
        2: {
            title: 'Bible Study Group',
            date: '2025-07-09',
            startTime: '19:00',
            endTime: '21:00',
            description: 'Weekly bible study and discussion',
            attendees: [2, 4]
        },
        3: {
            title: 'Community Outreach',
            date: '2025-07-12',
            startTime: '14:00',
            endTime: '18:00',
            description: 'Feeding program for the community',
            attendees: [1, 3, 4]
        }
    };

    const event = sampleEvents[eventId];
    if (event) {
        document.getElementById('editTitle').value = event.title;
        document.getElementById('editDate').value = event.date;
        document.getElementById('editStartTime').value = event.startTime;
        document.getElementById('editEndTime').value = event.endTime;
        document.getElementById('editDescription').value = event.description;
        
        // Check the attendees checkboxes
        const checkboxes = document.querySelectorAll('#editEventForm input[name="attendees[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = event.attendees.includes(parseInt(checkbox.value));
        });
    }

    document.getElementById('editModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
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

function saveAttendance() {
    // Here you would save attendance status to your backend
    showAlert('Attendance saved successfully!', 'success');
    closeAttendanceModal();
}

// Event Actions
function addEvent() {
    const title = document.getElementById('addTitle').value;
    const date = document.getElementById('addDate').value;
    const startTime = document.getElementById('addStartTime').value;
    const endTime = document.getElementById('addEndTime').value;
    const description = document.getElementById('addDescription').value;

    // Get selected attendees
    const attendeeCheckboxes = document.querySelectorAll('#addEventForm input[name="attendees[]"]:checked');
    const attendees = Array.from(attendeeCheckboxes).map(cb => cb.value);

    if (!title || !date || !startTime || !endTime || attendees.length === 0) {
        showAlert('Please fill in all required fields and select at least one attendee', 'error');
        return;
    }

    // Here you would typically send the data to your Laravel backend
    // For demo purposes, we'll just show a success message
    showAlert('Event added successfully with ' + attendees.length + ' attendees!', 'success');
    closeAddModal();
}
function filterAttendees() {
    const searchTerm = document.getElementById('attendeeSearch').value.toLowerCase();
    const ministryFilter = document.getElementById('ministryFilter').value;
    const attendeeItems = document.querySelectorAll('.attendee-item');
    let visibleCount = 0;
    
    attendeeItems.forEach(item => {
        const name = item.getAttribute('data-name').toLowerCase();
        const ministry = item.getAttribute('data-ministry');
        
        const matchesSearch = name.includes(searchTerm);
        const matchesMinistry = !ministryFilter || ministry === ministryFilter;
        
        if (matchesSearch && matchesMinistry) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
    }
}

function updateAttendeeCount() {
    const checkedBoxes = document.querySelectorAll('input[name="attendees[]"]:checked');
    const count = checkedBoxes.length;
    document.getElementById('attendeeCount').textContent = `${count} selected`;
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
        // Here you would typically send the request to your Laravel backend
        // For demo purposes, we'll just show a success message
        showAlert('Event archived successfully!', 'warning');
    }
}

// Alert System
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();
    
    const alertColors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };
    
    const alertIcons = {
        success: `<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>`,
        error: `<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
               </svg>`,
        warning: `<svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                 </svg>`,
        info: `<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>`
    };
    
    const alertElement = document.createElement('div');
    alertElement.id = alertId;
    alertElement.className = `max-w-sm w-full ${alertColors[type]} border rounded-lg p-4 shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
    alertElement.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${alertIcons[type]}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button onclick="removeAlert('${alertId}')" class="inline-flex rounded-md p-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    alertContainer.appendChild(alertElement);
    
    // Animate in
    setTimeout(() => {
        alertElement.classList.remove('translate-x-full', 'opacity-0');
        alertElement.classList.add('translate-x-0', 'opacity-100');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        removeAlert(alertId);
    }, 5000);
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


// Update attendance summary
function updateAttendanceSummary() {
    const selects = document.querySelectorAll('.attendance-select');
    const counts = { present: 0, absent: 0, };
    
    selects.forEach(select => {
        if (select.value && counts.hasOwnProperty(select.value)) {
            counts[select.value]++;
        }
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
    updateAttendanceSummary();
});
</script>

@endsection