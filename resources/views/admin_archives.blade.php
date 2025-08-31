{{-- resources/views/admin_archives.blade.php --}}
@extends('components.layout')

@section('title', 'Archives')

@section('styles')
    <style>
        /* ==========================================================================
       ARCHIVES PAGE STYLES
       ========================================================================== */

        /* Base Utilities
       ========================================================================== */
        [x-cloak] {
            display: none !important;
        }

        /* Modal Styles
       ========================================================================== */
        .modal-bg {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        /* Animations
       ========================================================================== */
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

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Table Styles
       ========================================================================== */
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-loading {
            position: relative;
        }

        .table-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 10;
        }

        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 11;
            width: 2rem;
            height: 2rem;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Tab Navigation
       ========================================================================== */
        .tab-indicator {
            transition: all 0.3s ease;
        }

        /* Form Elements
       ========================================================================== */
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .per-page-select {
            padding: 0.25rem 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            background: white;
        }

        /* Improved checkbox styling for indeterminate state */
        input[type="checkbox"]:indeterminate {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M4 8h8'/%3e%3c/svg%3e");
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        /* Badge Components
       ========================================================================== */
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

        /* Button Groups
       ========================================================================== */
        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Empty State
       ========================================================================== */
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

        /* Pagination Styles
       ========================================================================== */

        /* Main pagination container */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
            padding: 1rem 0;
            border-top: 1px solid #e5e7eb;
        }

        .pagination-info {
            font-size: 0.875rem;
            color: #6b7280;
        }

        /* Pagination controls wrapper */
        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Individual pagination buttons */
        .pagination-btn {
            min-width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .pagination-btn:hover:not(:disabled):not(.active) {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        /* Pagination ellipsis */
        .pagination-ellipsis {
            padding: 0.5rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
        }

        /* Jump to page functionality */
        .pagination-jump {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: 1rem;
            padding-left: 1rem;
            border-left: 1px solid #e5e7eb;
        }

        .pagination-jump input {
            width: 4rem;
            padding: 0.25rem 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            text-align: center;
            transition: border-color 0.2s;
        }

        .pagination-jump input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .pagination-jump button {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .pagination-jump button:hover {
            background: #2563eb;
        }

        .pagination-jump button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        /* Responsive Design
       ========================================================================== */
        @media (max-width: 768px) {

            /* Responsive pagination */
            .pagination {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .pagination-controls {
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .pagination-jump {
                justify-content: center;
                margin-left: 0;
                padding-left: 0;
                border-left: none;
                border-top: 1px solid #e5e7eb;
                padding-top: 1rem;
            }

            .pagination-info {
                text-align: center;
            }

            /* Responsive button groups */
            .btn-group {
                flex-direction: column;
                gap: 0.25rem;
            }

            /* Responsive empty state */
            .empty-state {
                padding: 2rem 0.5rem;
            }

            .empty-state svg {
                width: 3rem;
                height: 3rem;
            }
        }

        @media (max-width: 480px) {
            .pagination-btn {
                min-width: 2rem;
                height: 2rem;
                font-size: 0.75rem;
            }

            .pagination-ellipsis {
                min-width: 2rem;
                height: 2rem;
            }

            .pagination-jump input {
                width: 3rem;
                font-size: 0.75rem;
            }

            .pagination-jump button {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
        }

        /* Print Styles
       ========================================================================== */
        @media print {

            .pagination,
            .btn-group,
            .search-input,
            .per-page-select {
                display: none !important;
            }

            .table-hover tbody tr:hover {
                background-color: transparent;
            }

            .modal-bg {
                display: none !important;
            }
        }

        /* High Contrast Mode Support
       ========================================================================== */
        @media (prefers-contrast: high) {
            .pagination-btn {
                border-width: 2px;
            }

            .badge {
                border: 1px solid currentColor;
            }

            .search-input:focus {
                border-width: 2px;
            }
        }

        /* Reduced Motion Support
       ========================================================================== */
        @media (prefers-reduced-motion: reduce) {

            .fade-in,
            .tab-indicator,
            .pagination-btn,
            .loading-spinner {
                animation: none;
                transition: none;
            }
        }
    </style>
@endsection

@section('content')
    @include('components.settings_nav')
    <main class="flex-1 overflow-auto p-4 sm:p-6 md:ml-64" x-data="archivesManager({
                                    roles: {{ Js::from($roles ?? []) }},
                                    volunteers: {{ Js::from($volunteers) }},
                                    tasks: {{ Js::from($tasks) }},
                                    events: {{ Js::from($events) }}
                                })" x-cloak>
        <div class="bg-white rounded-lg shadow-lg p-6">

            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Archives</h2>
                    <p class="text-sm text-gray-600">Manage archived roles, volunteers, ministries, tasks, and events</p>
                </div>

            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-600">Roles</p>
                            <p class="text-2xl font-bold text-indigo-900" x-text="getTabCount('roles')">0</p>
                        </div>
                        <div class="p-3 bg-indigo-500 rounded-full">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.012-3a7.5 7.5 0 11-10.024 0M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="border-b mb-6">
                <div class="flex space-x-1 overflow-x-auto">
                    <button @click="changeTab('roles')"
                        :class="tab === 'roles' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.012-3a7.5 7.5 0 11-10.024 0M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Roles
                        </span>
                    </button>
                    <button @click="changeTab('volunteers')"
                        :class="tab === 'volunteers' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                            Volunteers
                        </span>
                    </button>
                    <button @click="changeTab('tasks')"
                        :class="tab === 'tasks' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Tasks
                        </span>
                    </button>
                    <button @click="changeTab('events')"
                        :class="tab === 'events' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                        class="tab-indicator px-6 py-3 whitespace-nowrap transition-colors duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Events
                        </span>
                    </button>
                </div>
            </div>

            {{-- Per Page Selector --}}
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Show:</label>
                    <select x-model="perPage" @change="changePerPage()" class="per-page-select">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-600">per page</span>
                </div>

                <!-- Results summary -->
                <div class="text-sm text-gray-600" x-show="getTotalItems(tab) > 0">
                    <span x-text="getTotalItems(tab)"></span> total results
                    <template x-if="searchQuery || selectedReason">
                        <span class="ml-2 text-blue-600">
                            (filtered from <span x-text="getTabCount(tab)"></span>)
                        </span>
                    </template>
                </div>
            </div>

            {{-- Bulk Actions --}}
            <div x-show="selectedItems.length > 0" x-cloak
                class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 fade-in">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-800">
                        <span x-text="selectedItems.length"></span> item(s) selected
                    </span>
                    <div class="btn-group">
                        <button @click="bulkRestore()"
                            class="btn-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Restore Selected
                        </button>
                        <button @click="bulkDelete()"
                            class="btn-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete Selected
                        </button>
                        <button @click="selectedItems = []"
                            class="btn-sm bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
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
                                    <input type="checkbox" :checked="isAllSelected()" :indeterminate="isSomeSelected()"
                                        @change="toggleAll($event)"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Email
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Role
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Ministry</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Archived Date</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Reason
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Archived By</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in getPaginatedItems('roles')" :key="item.id">
                                <tr>
                                    <td class="border border-gray-200 p-3">
                                        <input type="checkbox" :value="item.id" x-model="selectedItems">
                                    </td>
                                    <td class="border border-gray-200 p-3">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <p class="font-medium text-gray-900" x-text="item.email"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="border border-gray-200 p-3">
                                        <span class="badge"
                                            :class="{'badge-success': item.role === 'Admin', 'badge-info': item.role === 'Staff'}">
                                            <span x-text="item.role"></span>
                                        </span>
                                    </td>
                                    <td class="border border-gray-200 p-3" x-text="item.ministry"></td>
                                    <td class="border border-gray-200 p-3" x-text="formatDate(item.archived_date)"></td>
                                    <td class="border border-gray-200 p-3">
                                        <span class="badge badge-warning" x-text="item.reason"></span>
                                    </td>
                                    <td class="border border-gray-200 p-3" x-text="item.archived_by"></td>
                                    <td class="border border-gray-200 p-3">
                                        <div class="flex items-center space-x-2">
                                            <button @click="restoreItem(item)"
                                                class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all">
                                                Restore
                                            </button>
                                            <button @click="confirmDelete(item)"
                                                class="inline-flex items-center px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="getPaginatedItems('roles').length === 0">
                                <td colspan="8" class="border border-gray-200 p-8">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.012-3a7.5 7.5 0 11-10.024 0M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                            </path>
                                        </svg>
                                        <h3 class="text-lg font-medium mb-2">No archived roles found</h3>
                                        <p class="text-sm">No roles match your current search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div x-html="renderPagination('roles')"></div>
            </div>

            {{-- Volunteers Tab --}}
            <div x-show="tab === 'volunteers'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse table-hover">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border border-gray-200 p-3 text-left">
                                    <input type="checkbox" @change="toggleAll($event)"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Name
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Email
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Archived Date</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Reason
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Archived By</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in getPaginatedItems('volunteers')" :key="item.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="border border-gray-200 p-3">
                                        <input type="checkbox" :value="item.id" x-model="selectedItems"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="border border-gray-200 p-3">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <p class="font-medium text-gray-900" x-text="item.name"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="item.email"></td>
                                    <td class="border border-gray-200 p-3 text-sm text-gray-700"
                                        x-text="formatDate(item.archived_date)"></td>
                                    <td class="border border-gray-200 p-3">
                                        <span class="badge badge-warning" x-text="item.reason"></span>
                                    </td>
                                    <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="item.archived_by">
                                    </td>
                                    <td class="border border-gray-200 p-3">
                                        <div class="flex items-center space-x-2">
                                            <button @click="restoreItem(item)"
                                                class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                                Restore
                                            </button>
                                            <button @click="confirmDelete(item)"
                                                class="inline-flex items-center px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="getPaginatedItems('volunteers').length === 0">
                                <td colspan="7" class="border border-gray-200 p-8">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                            </path>
                                        </svg>
                                        <h3 class="text-lg font-medium mb-2">No archived volunteers found</h3>
                                        <p class="text-sm">No volunteers match your current search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div x-html="renderPagination('volunteers')"></div>
            </div>

            {{-- Tasks Tab --}}
            <div x-show="tab === 'tasks'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse table-hover">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border border-gray-200 p-3 text-left">
                                    <input type="checkbox" @change="toggleAll($event)"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Task
                                    Title</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Status
                                </th>

                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Archived Date</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Reason
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Archived By</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in getPaginatedItems('tasks')" :key="item.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="border border-gray-200 p-3">
                                        <input type="checkbox" :value="item.id" x-model="selectedItems"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="border border-gray-200 p-3">
                                        <div class="flex items-center gap-3">

                                            <div>
                                                <p class="font-medium text-gray-900" x-text="item.title"></p>

                                            </div>
                                        </div>
                                    </td>
                                    <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="item.status"></td>

                                    <td class="border border-gray-200 p-3 text-sm text-gray-700"
                                        x-text="formatDate(item.archived_date)"></td>
                                    <td class="border border-gray-200 p-3">
                                        <span class="badge badge-warning" x-text="item.reason"></span>
                                    </td>
                                    <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="item.archived_by">
                                    </td>
                                    <td class="border border-gray-200 p-3">
                                        <div class="flex items-center space-x-2">
                                            <button @click="restoreItem(item)"
                                                class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                                Restore
                                            </button>
                                            <button @click="confirmDelete(item)"
                                                class="inline-flex items-center px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="getPaginatedItems('tasks').length === 0">
                                <td colspan="8" class="border border-gray-200 p-8">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        <h3 class="text-lg font-medium mb-2">No archived tasks found</h3>
                                        <p class="text-sm">No tasks match your current search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div x-html="renderPagination('tasks')"></div>
            </div>

            {{-- Events Tab --}}
            <div x-show="tab === 'events'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse table-hover">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border border-gray-200 p-3 text-left">
                                    <input type="checkbox" @change="toggleAll($event)"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Event
                                    Title</th>

                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Archived Date</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Reason
                                </th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">
                                    Archived By</th>
                                <th class="border border-gray-200 p-3 text-left text-sm font-semibold text-gray-900">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in getPaginatedItems('events')" :key="item.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="border border-gray-200 p-3">
                                        <input type="checkbox" :value="item.id" x-model="selectedItems"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="border border-gray-200 p-3">
                                        <div class="flex items-center gap-3">

                                            <div>
                                                <p class="font-medium text-gray-900" x-text="item.title"></p>

                                            </div>
                                        </div>
                                    </td>

                                    <td class="border border-gray-200 p-3 text-sm text-gray-700"
                                        x-text="formatDate(item.archived_date)"></td>
                                    <td class="border border-gray-200 p-3">
                                        <span class="badge badge-warning" x-text="item.reason"></span>
                                    </td>
                                    <td class="border border-gray-200 p-3 text-sm text-gray-700" x-text="item.archived_by">
                                    </td>
                                    <td class="border border-gray-200 p-3">
                                        <div class="flex items-center space-x-2">
                                            <button @click="restoreItem(item)"
                                                class="inline-flex items-center px-3 py-2 text-sm border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                                Restore
                                            </button>
                                            <button @click="confirmDelete(item)"
                                                class="inline-flex items-center px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="getPaginatedItems('events').length === 0">
                                <td colspan="7" class="border border-gray-200 p-8">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <h3 class="text-lg font-medium mb-2">No archived events found</h3>
                                        <p class="text-sm">No events match your current search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div x-html="renderPagination('events')"></div>
            </div>

        </div>

        {{-- Confirmation Modal --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto modal-bg"
            @click.self="showModal = false; itemToDelete = null" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 fade-in" @click.stop>
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
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
        <div x-show="showToast" x-cloak class="fixed top-4 right-4 z-50 fade-in" style="display: none;">
            <div :class="toastType === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'"
                class="border rounded-lg p-4 shadow-lg max-w-sm">
                <div class="flex items-center gap-3">
                    <div x-show="toastType === 'success'" class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div x-show="toastType === 'error'" class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p :class="toastType === 'success' ? 'text-green-800' : 'text-red-800'" class="text-sm font-medium"
                            x-text="toastMessage"></p>
                    </div>
                    <button @click="showToast = false"
                        :class="toastType === 'success' ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800'"
                        class="flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Bulk Delete Confirmation Modal --}}
        <div x-show="showBulkDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto modal-bg"
            @click.self="showBulkDeleteModal = false" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 fade-in" @click.stop>
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Confirm Bulk Delete</h3>
                    <p class="text-sm text-gray-600 text-center mb-6">
                        Are you sure you want to permanently delete <span class="font-semibold"
                            x-text="selectedItems.length"></span> selected items? This action cannot be undone.
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
@endsection

@section('scripts')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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

                // Pagination
                currentPage: 1,
                perPage: 10,
                jumpToPage: '', // For jump to page functionality

                // Use the initial data passed from the server
                data: initialData,

                getTabCount(tabName) {
                    return this.data[tabName].length;
                },

                getFilteredItems(tabName) {
                    let items = this.data[tabName];

                    // Filter by search query
                    if (this.searchQuery) {
                        items = items.filter(item => {
                            const searchableFields = [
                                item.name,
                                item.email,
                                item.title,
                                item.description,
                                item.reason,
                                item.role,
                                item.ministry,
                                item.archived_by
                            ];

                            return searchableFields.some(field =>
                                field && field.toString().toLowerCase().includes(this.searchQuery.toLowerCase())
                            );
                        });
                    }

                    // Filter by reason
                    if (this.selectedReason) {
                        items = items.filter(item =>
                            item.reason && item.reason.toLowerCase().includes(this.selectedReason.toLowerCase())
                        );
                    }

                    return items;
                },

                getPaginatedItems(tabName) {
                    const filteredItems = this.getFilteredItems(tabName);
                    const startIndex = (this.currentPage - 1) * this.perPage;
                    const endIndex = startIndex + this.perPage;
                    return filteredItems.slice(startIndex, endIndex);
                },

                getTotalPages(tabName) {
                    const filteredItems = this.getFilteredItems(tabName);
                    return Math.ceil(filteredItems.length / this.perPage) || 1;
                },

                getTotalItems(tabName) {
                    return this.getFilteredItems(tabName).length;
                },

                getStartItem(tabName) {
                    const totalItems = this.getTotalItems(tabName);
                    if (totalItems === 0) return 0;
                    return (this.currentPage - 1) * this.perPage + 1;
                },

                getEndItem(tabName) {
                    const totalItems = this.getTotalItems(tabName);
                    const endItem = this.currentPage * this.perPage;
                    return Math.min(endItem, totalItems);
                },

                goToPage(page, tabName) {
                    const totalPages = this.getTotalPages(tabName);
                    if (page >= 1 && page <= totalPages) {
                        this.currentPage = page;
                        this.selectedItems = [];
                        this.jumpToPage = '';
                    }
                },

                goToPreviousPage(tabName) {
                    if (this.currentPage > 1) {
                        this.goToPage(this.currentPage - 1, tabName);
                    }
                },

                goToNextPage(tabName) {
                    const totalPages = this.getTotalPages(tabName);
                    if (this.currentPage < totalPages) {
                        this.goToPage(this.currentPage + 1, tabName);
                    }
                },

                goToFirstPage(tabName) {
                    this.goToPage(1, tabName);
                },

                goToLastPage(tabName) {
                    const totalPages = this.getTotalPages(tabName);
                    this.goToPage(totalPages, tabName);
                },

                jumpToPageHandler(tabName) {
                    const page = parseInt(this.jumpToPage);
                    if (!isNaN(page) && page > 0) {
                        this.goToPage(page, tabName);
                    }
                },

                getPageNumbers(tabName) {
                    const totalPages = this.getTotalPages(tabName);
                    const pages = [];
                    const maxVisiblePages = 7; // Show more pages for better UX
                    const ellipsis = '...';

                    if (totalPages <= maxVisiblePages) {
                        // Show all pages if total is small
                        for (let i = 1; i <= totalPages; i++) {
                            pages.push(i);
                        }
                    } else {
                        // Always show first page
                        pages.push(1);

                        // Calculate range around current page
                        const start = Math.max(2, this.currentPage - 2);
                        const end = Math.min(totalPages - 1, this.currentPage + 2);

                        // Add ellipsis after first page if needed
                        if (start > 2) {
                            pages.push(ellipsis);
                        }

                        // Add pages around current page
                        for (let i = start; i <= end; i++) {
                            if (i !== 1 && i !== totalPages) {
                                pages.push(i);
                            }
                        }

                        // Add ellipsis before last page if needed
                        if (end < totalPages - 1) {
                            pages.push(ellipsis);
                        }

                        // Always show last page
                        if (totalPages > 1) {
                            pages.push(totalPages);
                        }
                    }

                    return pages;
                },

                renderPagination(tabName) {
                    const totalPages = this.getTotalPages(tabName);
                    const totalItems = this.getTotalItems(tabName);
                    const startItem = this.getStartItem(tabName);
                    const endItem = this.getEndItem(tabName);

                    if (totalItems === 0) {
                        return '<div class="pagination"><div class="pagination-info">No results found</div></div>';
                    }

                    if (totalPages <= 1) {
                        return `<div class="pagination"><div class="pagination-info">Showing ${totalItems} result${totalItems === 1 ? '' : 's'}</div></div>`;
                    }

                    const pageNumbers = this.getPageNumbers(tabName);

                    let paginationHtml = `
                                <div class="pagination">
                                    <div class="pagination-info">
                                        Showing ${startItem} to ${endItem} of ${totalItems} results
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="pagination-controls">
                                            <button class="pagination-btn" ${this.currentPage === 1 ? 'disabled' : ''} 
                                                    @click="goToFirstPage('${tabName}')" title="First page">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                                                </svg>
                                            </button>
                                            <button class="pagination-btn" ${this.currentPage === 1 ? 'disabled' : ''} 
                                                    @click="goToPreviousPage('${tabName}')" title="Previous page">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                            </button>
                            `;

                    pageNumbers.forEach(page => {
                        if (page === '...') {
                            paginationHtml += '<span class="pagination-ellipsis">...</span>';
                        } else {
                            paginationHtml += `
                                        <button class="pagination-btn ${this.currentPage === page ? 'active' : ''}" 
                                                @click="goToPage(${page}, '${tabName}')" title="Page ${page}">
                                            ${page}
                                        </button>
                                    `;
                        }
                    });

                    paginationHtml += `
                                            <button class="pagination-btn" ${this.currentPage === totalPages ? 'disabled' : ''} 
                                                    @click="goToNextPage('${tabName}')" title="Next page">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                            <button class="pagination-btn" ${this.currentPage === totalPages ? 'disabled' : ''} 
                                                    @click="goToLastPage('${tabName}')" title="Last page">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Jump to page -->
                                        <div class="pagination-jump">
                                            <span class="text-sm text-gray-600">Go to:</span>
                                            <input type="number" 
                                                   x-model="jumpToPage" 
                                                   @keyup.enter="jumpToPageHandler('${tabName}')"
                                                   min="1" 
                                                   max="${totalPages}"
                                                   placeholder="${this.currentPage}"
                                                   class="w-16 px-2 py-1 text-sm border border-gray-300 rounded text-center">
                                            <button @click="jumpToPageHandler('${tabName}')" 
                                                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                                Go
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;

                    return paginationHtml;
                },

                changeTab(newTab) {
                    this.tab = newTab;
                    this.currentPage = 1;
                    this.selectedItems = [];
                    this.jumpToPage = '';
                },

                resetPagination() {
                    this.currentPage = 1;
                    this.selectedItems = [];
                    this.jumpToPage = '';
                },

                // Handle per page change
                changePerPage() {
                    // Maintain relative position in results
                    const currentStartItem = (this.currentPage - 1) * this.perPage + 1;
                    const newPage = Math.ceil(currentStartItem / this.perPage);
                    this.currentPage = Math.max(1, newPage);
                    this.selectedItems = [];
                },

                formatDate(dateString) {
                    if (!dateString || dateString === 'N/A') return 'N/A';
                    try {
                        const date = new Date(dateString);
                        if (isNaN(date.getTime())) return 'N/A';
                        return date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    } catch (error) {
                        return 'N/A';
                    }
                },

                toggleAll(event) {
                    if (event.target.checked) {
                        this.selectedItems = this.getPaginatedItems(this.tab).map(item => item.id);
                    } else {
                        this.selectedItems = [];
                    }
                },

                isAllSelected() {
                    const currentPageItems = this.getPaginatedItems(this.tab);
                    return currentPageItems.length > 0 && currentPageItems.every(item => this.selectedItems.includes(item.id));
                },

                isSomeSelected() {
                    const currentPageItems = this.getPaginatedItems(this.tab);
                    return currentPageItems.some(item => this.selectedItems.includes(item.id)) && !this.isAllSelected();
                },

                showToastMessage(message, type) {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;

                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                },

                confirmDelete(item) {
                    this.itemToDelete = item;
                    this.showModal = true;
                },

                restoreItem(item) {
                    let url;
                    const itemId = item.id;

                    if (this.tab === 'roles') {
                        url = `/settings/role/${itemId}/restore`;
                    } else if (this.tab === 'volunteers') {
                        url = `/volunteers/${itemId}/restore`;
                    } else if (this.tab === 'ministries') {
                        url = `/ministries/${itemId}/restore`;
                    } else if (this.tab === 'events') {
                        url = `/events/${itemId}/restore`;
                    } else if (this.tab === 'tasks') {
                        url = `/tasks/${itemId}/restore`;
                    } else {
                        return;
                    }

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
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

                                // Adjust pagination if current page is empty
                                if (this.getPaginatedItems(this.tab).length === 0 && this.currentPage > 1) {
                                    this.currentPage = this.currentPage - 1;
                                }
                            } else {
                                this.showToastMessage(data.message || 'Error restoring item', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Restore error:', error);
                            this.showToastMessage('Error restoring item', 'error');
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
                    } else if (this.tab === 'ministries') {
                        url = `/ministries/${itemId}/force-delete`;
                    } else if (this.tab === 'events') {
                        url = `/events/${itemId}/force-delete`;
                    } else if (this.tab === 'tasks') {
                        url = `/tasks/${itemId}/force-delete`;
                    } else {
                        return;
                    }

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
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

                                // Adjust pagination if current page is empty
                                if (this.getPaginatedItems(this.tab).length === 0 && this.currentPage > 1) {
                                    this.currentPage = this.currentPage - 1;
                                }
                            } else {
                                this.showToastMessage(data.message || 'Error deleting item', 'error');
                            }
                            this.showModal = false;
                            this.itemToDelete = null;
                        })
                        .catch(error => {
                            console.error('Delete error:', error);
                            this.showToastMessage('Error deleting item', 'error');
                            this.showModal = false;
                            this.itemToDelete = null;
                        });
                },

                bulkRestore() {
                    if (this.selectedItems.length === 0) return;

                    const endpoints = {
                        'roles': '/settings/role/bulk-restore',
                        'volunteers': '/volunteers/bulk-restore',
                        'ministries': '/ministries/bulk-restore',
                        'events': '/events/bulk-restore',
                        'tasks': '/tasks/bulk-restore',
                    };

                    fetch(endpoints[this.tab], {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
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
                                    `${data.restored_count || this.selectedItems.length} items restored successfully!`,
                                    'success'
                                );

                                // Adjust pagination if current page is empty
                                if (this.getPaginatedItems(this.tab).length === 0 && this.currentPage > 1) {
                                    this.currentPage = this.currentPage - 1;
                                }
                            } else {
                                this.showToastMessage(data.message || 'Error restoring items', 'error');
                            }
                            this.selectedItems = [];
                        })
                        .catch(error => {
                            console.error('Bulk restore error:', error);
                            this.showToastMessage('Error restoring items', 'error');
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
                        'volunteers': '/volunteers/bulk-force-delete',
                        'ministries': '/ministries/bulk-force-delete',
                        'events': '/events/bulk-force-delete',
                        'tasks': '/tasks/bulk-force-delete',
                    };

                    fetch(endpoints[this.tab], {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
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
                                    `${data.deleted_count || this.selectedItems.length} items deleted permanently!`,
                                    'success'
                                );

                                // Adjust pagination if current page is empty
                                if (this.getPaginatedItems(this.tab).length === 0 && this.currentPage > 1) {
                                    this.currentPage = this.currentPage - 1;
                                }
                            } else {
                                this.showToastMessage(data.message || 'Error deleting items', 'error');
                            }
                            this.selectedItems = [];
                        })
                        .catch(error => {
                            console.error('Bulk delete error:', error);
                            this.showToastMessage('Error deleting items', 'error');
                            this.selectedItems = [];
                        });
                },

                // Initialize event listeners and watchers
                init() {
                    // Watch for changes that should reset pagination
                    this.$watch('searchQuery', () => {
                        this.resetPagination();
                    });

                    this.$watch('selectedReason', () => {
                        this.resetPagination();
                    });

                    this.$watch('perPage', () => {
                        this.changePerPage();
                    });

                    // Auto-hide toast after showing
                    this.$watch('showToast', (value) => {
                        if (value) {
                            setTimeout(() => {
                                this.showToast = false;
                            }, 5000);
                        }
                    });
                }
            }
        }
    </script>
@endsection