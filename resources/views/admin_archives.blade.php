{{-- resources/views/archives.blade.php --}}
@extends('components.layout')

@section('title', 'Archives')

@section('styles')
<style>
    .modal-bg {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }

    .tab-indicator {
        transition: all 0.3s ease;
    }

    .search-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-success {
        background-color: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .btn-group {
        display: flex;
        gap: 0.5rem;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }

    .empty-state svg {
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1rem;
        color: #d1d5db;
    }
</style>
@endsection

@section('content')
@include('components.settings_nav')
<main class="flex-1 overflow-auto p-4 sm:p-6 md:ml-64" x-data="archivesManager({
    roles: {{ Js::from($roles ?? []) }},
    volunteers: {{ Js::from($volunteers) }},
    ministries: {{ Js::from($ministries) }},
    tasks: {{ Js::from($tasks) }},
    events: {{ Js::from($events) }}
})">
    <div class="bg-white rounded-lg shadow-lg p-6">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Archives</h2>
                <p class="text-sm text-gray-600">Manage archived roles, volunteers, ministries, tasks, and events</p>
            </div>

            {{-- Search and Filters --}}
            <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
                <div class="relative">
                    <input type="text"
                        x-model="searchQuery"
                        placeholder="Search archives..."
                        class="search-input w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <select x-model="selectedReason" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Reasons</option>
                    <option value="moved">Moved</option>
                    <option value="unavailable">Unavailable</option>
                    <option value="merged">Merged</option>
                    <option value="ended">Ended</option>
                    <option value="completed">Completed</option>
                    <option value="restructured">Restructured</option>
                </select>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-600">Roles</p>
                        <p class="text-2xl font-bold text-indigo-900" x-text="getTabCount('roles')">0</p>
                    </div>
                    <div class="p-3 bg-indigo-500 rounded-full">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.012-3a7.5 7.5 0 11-10.024 0M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Volunteers</p>
                        <p class="text-2xl font-bold text-blue-900" x-text="getTabCount('volunteers')">2</p>
                    </div>
                    <div class="p-3 bg-blue-500 rounded-full">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Ministries</p>
                        <p class="text-2xl font-bold text-green-900" x-text="getTabCount('ministries')">2</p>
                    </div>
                    <div class="p-3 bg-green-500 rounded-full">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600">Tasks</p>
                        <p class="text-2xl font-bold text-yellow-900" x-text="getTabCount('tasks')">0</p>
                    </div>
                    <div class="p-3 bg-yellow-500 rounded-full">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Events</p>
                        <p class="text-2xl font-bold text-purple-900" x-text="getTabCount('events')">0</p>
                    </div>
                    <div class="p-3 bg-purple-500 rounded-full">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b mb-6">
            <div class="flex space-x-1 overflow-x-auto">
                <button @click="tab = 'roles'"
                    :class="tab === 'roles' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.012-3a7.5 7.5 0 11-10.024 0M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Roles
                    </span>
                </button>
                <button @click="tab = 'volunteers'"
                    :class="tab === 'volunteers' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Volunteers
                    </span>
                </button>
                <button @click="tab = 'ministries'"
                    :class="tab === 'ministries' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Ministries
                    </span>
                </button>
                <button @click="tab = 'tasks'"
                    :class="tab === 'tasks' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Tasks
                    </span>
                </button>
                <button @click="tab = 'events'"
                    :class="tab === 'events' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Events
                    </span>
                </button>
            </div>
        </div>

        {{-- Bulk Actions --}}
        <div x-show="selectedItems.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 fade-in">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-blue-800">
                    <span x-text="selectedItems.length"></span> item(s) selected
                </span>
                <div class="btn-group">
                    <button @click="bulkRestore()" class="btn-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Restore Selected
                    </button>
                    <button @click="bulkDelete()" class="btn-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete Selected
                    </button>
                    <button @click="selectedItems = []" class="btn-sm bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>

        {{-- Roles Tab --}}
        <div x-show="tab === 'roles'" x-cloak>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse table-hover">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-200 p-3 text-left">
                                <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Role Name</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Permissions</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Archived Date</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Reason</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Archived By</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in getFilteredItems('roles')" :key="item.id">
                            <tr>
                                <td class="border border-gray-200 p-3">
                                    <input type="checkbox" :value="item.id" x-model="selectedItems">
                                </td>
                                <td class="border border-gray-200 p-3">
                                    <div class="flex items-center gap-3">
                                        {{-- Profile icon --}}
                                        <div>
                                            <p class="font-medium text-gray-900" x-text="item.name"></p>
                                            <p class="text-sm text-gray-500" x-text="item.description"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="border border-gray-200 p-3">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="permission in item.permissions">
                                            <span class="badge badge-info" x-text="permission"></span>
                                        </template>
                                    </div>
                                </td>
                                <td class="border border-gray-200 p-3" x-text="formatDate(item.archived_date)"></td>
                                <td class="border border-gray-200 p-3">
                                    <span class="badge badge-warning" x-text="item.reason"></span>
                                </td>
                                <td class="border border-gray-200 p-3" x-text="item.archived_by"></td>
                                <td class="border border-gray-200 p-3">
                                    <div class="flex items-center space-x-2">
                                        <button @click="restoreItem(item)" class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Restore
                                        </button>
                                        <button @click="confirmDelete(item)" class="inline-flex items-center px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="getFilteredItems('roles').length === 0">
                            <td colspan="7" class="border border-gray-200 p-8">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.012-3a7.5 7.5 0 11-10.024 0M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium mb-2">No archived roles found</h3>
                                    <p class="text-sm">No roles match your current search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Volunteers Tab --}}
        <div x-show="tab === 'volunteers'" x-cloak>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse table-hover">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-200 p-3 text-left">
                                <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Name</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Archived Date</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Reason</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Archived By</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in getFilteredItems('volunteers')" :key="item.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="border border-gray-200 p-3">
                                    <input type="checkbox" :value="item.id" x-model="selectedItems" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="border border-gray-200 p-3">
                                    <div class="flex items-center gap-3">
                                        <!-- <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                            <span x-text="item.name.charAt(0).toUpperCase()"></span>
                                        </div> -->
                                        <div>
                                            <p class="font-medium text-gray-900" x-text="item.name"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="item.email"></td>
                                <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="formatDate(item.archived_date)"></td>
                                <td class="border border-gray-200 p-3">
                                    <span class="badge badge-warning" x-text="item.reason"></span>
                                </td>
                                <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="item.archived_by"></td>
                                <td class="border border-gray-200 p-3">
                                    <div class="flex items-center space-x-2">
                                        <button @click="restoreItem(item)" class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Restore
                                        </button>
                                        <button @click="confirmDelete(item)" class="inline-flex items-center px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="getFilteredItems('volunteers').length === 0">
                            <td colspan="7" class="border border-gray-200 p-8">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium mb-2">No archived volunteers found</h3>
                                    <p class="text-sm">No volunteers match your current search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ministries Tab --}}
        <div x-show="tab === 'ministries'" x-cloak>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse table-hover">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-200 p-3 text-left">
                                <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Name</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Archived Date</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Reason</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Archived By</th>
                            <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in getFilteredItems('ministries')" :key="item.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="border border-gray-200 p-3">
                                    <input type="checkbox" :value="item.id" x-model="selectedItems" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="border border-gray-200 p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                            <span x-text="item.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900" x-text="item.name"></p>
                                            <p class="text-sm text-gray-500" x-text="item.description"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="formatDate(item.archived_date)"></td>
                                <td class="border border-gray-200 p-3">
                                    <span class="badge badge-info" x-text="item.reason"></span>
                                </td>
                                <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="item.archived_by"></td>
                                <td class="border border-gray-200 p-3">
                                    <div class="flex items-center space-x-2">
                                        <button @click="restoreItem(item)" class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Restore
                                        </button>
                                        <button @click="confirmDelete(item)" class="inline-flex items-center px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="getFilteredItems('ministries').length === 0">
                            <td colspan="6" class="border border-gray-200 p-8">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium mb-2">No archived ministries found</h3>
                                    <p class="text-sm">No ministries match your current search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tasks Tab --}}
        <div x-show="tab === 'tasks'" x-cloak>
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium mb-2">No archived tasks</h3>
                <p class="text-sm">Archived tasks will appear here once they are moved to the archive.</p>
            </div>
        </div>

        {{-- Events Tab --}}
        <div x-show="tab === 'events'" x-cloak>
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-medium mb-2">No archived events</h3>
                <p class="text-sm">Archived events will appear here once they are moved to the archive.</p>
            </div>
        </div>

    </div>

    {{-- Confirmation Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto modal-bg"
        @click.self="showModal = false; itemToDelete = null">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 fade-in" @click.stop>
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Confirm Delete</h3>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Are you sure you want to permanently delete this item? This action cannot be undone.
                </p>

                <div class="flex gap-3 justify-end">
                    <button @click="showModal = false; itemToDelete = null"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button @click="deleteItem()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notifications --}}
    <div x-show="showToast"
        x-cloak
        class="fixed top-4 right-4 z-50 fade-in">
        <div :class="toastType === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'"
            class="border rounded-lg p-4 shadow-lg max-w-sm">
            <div class="flex items-center gap-3">
                <div x-show="toastType === 'success'" class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div x-show="toastType === 'error'" class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p :class="toastType === 'success' ? 'text-green-800' : 'text-red-800'"
                        class="text-sm font-medium" x-text="toastMessage"></p>
                </div>
                <button @click="showToast = false"
                    :class="toastType === 'success' ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800'"
                    class="flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    {{-- Bulk Delete Confirmation Modal --}}
    <div x-show="showBulkDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto modal-bg"
        @click.self="showBulkDeleteModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 fade-in" @click.stop>
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Confirm Bulk Delete</h3>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Are you sure you want to permanently delete <span class="font-semibold" x-text="selectedItems.length"></span> selected items? This action cannot be undone.
                </p>

                <div class="flex gap-3 justify-end">
                    <button @click="showBulkDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button @click="confirmBulkDelete()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function archivesManager(initialData) {
        return {
            tab: 'roles',
            searchQuery: '',
            selectedReason: '',
            selectedItems: [],
            showModal: false,
            showBulkDeleteModal: false,
            itemToDelete: null,
            showToast: false,
            toastMessage: '',
            toastType: 'success',

            // Use the initial data passed from the server
            data: initialData,

            getTabCount(tabName) {
                return this.data[tabName].length;
            },

            getFilteredItems(tabName) {
                let items = this.data[tabName];

                // Filter by search query
                if (this.searchQuery) {
                    items = items.filter(item =>
                        (item.name && item.name.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                        (item.description && item.description.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                        (item.reason && item.reason.toLowerCase().includes(this.searchQuery.toLowerCase()))
                    );
                }

                // Filter by reason
                if (this.selectedReason) {
                    items = items.filter(item =>
                        item.reason && item.reason.toLowerCase().includes(this.selectedReason.toLowerCase())
                    );
                }

                return items;
            },

            formatDate(dateString) {
                if (!dateString) return 'N/A';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },

            toggleAll(event) {
                if (event.target.checked) {
                    this.selectedItems = this.getFilteredItems(this.tab).map(item => item.id);
                } else {
                    this.selectedItems = [];
                }
            },

            restoreItem(item) {
                let url;
                const itemId = item.id;

                if (this.tab === 'roles') {
                    url = `/settings/role/${itemId}/restore`;
                } else if (this.tab === 'volunteers') {
                    url = `/volunteers/${itemId}/restore`;
                } else {
                    return;
                }

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const tabData = this.data[this.tab];
                            const index = tabData.findIndex(i => i.id === itemId);
                            if (index > -1) {
                                tabData.splice(index, 1);
                            }
                            this.showToastMessage('Item restored successfully!', 'success');
                        } else {
                            this.showToastMessage(data.message || 'Error restoring item', 'error');
                        }
                    });
            },


            deleteItem() {
                if (!this.itemToDelete) return;

                let url;
                const itemId = this.itemToDelete.id;

                if (this.tab === 'roles') {
                    url = `/settings/role/${itemId}/force-delete`;
                } else if (this.tab === 'volunteers') {
                    url = `/volunteers/${itemId}/force-delete`;
                } else {
                    return;
                }

                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const tabData = this.data[this.tab];
                            const index = tabData.findIndex(item => item.id === itemId);
                            if (index > -1) {
                                tabData.splice(index, 1);
                            }
                            this.selectedItems = this.selectedItems.filter(id => id !== itemId);
                            this.showToastMessage('Item deleted permanently!', 'success');
                        } else {
                            this.showToastMessage(data.message || 'Error deleting item', 'error');
                        }
                        this.showModal = false;
                        this.itemToDelete = null;
                    });
            },
            bulkRestore() {
                if (this.selectedItems.length === 0) return;

                const endpoints = {
                    'roles': '/settings/role/bulk-restore',
                    'volunteers': '/volunteers/bulk-restore'
                };

                fetch(endpoints[this.tab], {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            ids: this.selectedItems
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const tabData = this.data[this.tab];
                            this.selectedItems.forEach(itemId => {
                                const index = tabData.findIndex(i => i.id === itemId);
                                if (index > -1) tabData.splice(index, 1);
                            });
                            this.showToastMessage(
                                `${data.restored_count} items restored successfully!`,
                                'success'
                            );
                        } else {
                            this.showToastMessage(
                                data.message || 'Error restoring items',
                                'error'
                            );
                        }
                        this.selectedItems = [];
                    });
            },

            bulkDelete() {
                this.showBulkDeleteModal = true;
            },

            confirmBulkDelete() {
                this.showBulkDeleteModal = false;
                if (this.selectedItems.length === 0) return;

                const endpoints = {
                    'roles': '/settings/role/bulk-force-delete',
                    'volunteers': '/volunteers/bulk-force-delete'
                };

                fetch(endpoints[this.tab], {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            ids: this.selectedItems
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const tabData = this.data[this.tab];
                            this.selectedItems.forEach(itemId => {
                                const index = tabData.findIndex(i => i.id === itemId);
                                if (index > -1) tabData.splice(index, 1);
                            });
                            this.showToastMessage(
                                `${data.deleted_count} items deleted permanently!`,
                                'success'
                            );
                        } else {
                            this.showToastMessage(
                                data.message || 'Error deleting items',
                                'error'
                            );
                        }
                        this.selectedItems = [];
                    });
            }
        }
    }
</script>
@endsection

@section('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection