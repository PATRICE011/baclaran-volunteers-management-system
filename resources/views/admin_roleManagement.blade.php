@extends('components.layout')

@section('title', 'Role Management')

@section('styles')
    <style>
        /* Role Management Styles */
        .modal-bg {
            background: rgba(0, 0, 0, 0.5);
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .filter-active {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .role-admin {
            background-color: #ef4444;
            color: white;
        }

        .role-staff {
            background-color: #f59e0b;
            color: white;
        }

        .search-container {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            pointer-events: none;
        }

        .toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            padding: 1rem;
            border-radius: 0.5rem;
            color: white;
            font-weight: 500;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast-success {
            background-color: #10b981;
        }

        .toast-error {
            background-color: #ef4444;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
        }

        .form-input.error:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1);
        }

        .error-text {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }

        .error-text.show {
            display: block;
        }

        /* Button styles */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        /* Table styles */
        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Modal styles */
        .modal {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin: 1rem;
            max-width: 28rem;
            width: 100%;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
        }

        .modal-close {
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: #6b7280;
        }

        /* Stats cards */
        .stats-container {
            display: flex;
            space-x: 1rem;
        }

        .stat-card {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* Password toggle */
        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            padding: 0 0.75rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #6b7280;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #374151;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .modal-content {
                margin: 1rem;
            }

            .stats-container {
                flex-direction: column;
                space-y: 1rem;
            }

            .table th,
            .table td {
                padding: 0.75rem;
            }
        }
    </style>
@endsection

@section('content')
    @include('components.settings_nav')
    <main class="flex-1 overflow-auto p-4 sm:p-6 md:ml-64">
        <div class="bg-white rounded-lg shadow-lg p-6">
            {{-- Page heading with stats --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Role Management</h2>
                    <p class="text-sm text-gray-600 mt-1">Manage user roles and permissions</p>
                </div>
                <div class="mt-4 sm:mt-0 stats-container">
                    <div class="stat-card">
                        <div class="stat-number text-blue-600" id="total-users">{{ count($nonArchivedUsers) }}</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number text-red-600" id="admin-count">
                            {{ $nonArchivedUsers->where('role', 'admin')->count() }}</div>
                        <div class="stat-label">Admins</div>
                    </div>
                </div>
            </div>

            {{-- Add New User Form --}}
            <div class="mb-8 p-6 border border-gray-200 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Add New User</h3>
                    <button type="button" id="toggle-form" class="text-blue-600 hover:text-blue-800 text-sm">
                        <span id="toggle-text">Hide Form</span>
                    </button>
                </div>

                <form id="add-role-form" class="space-y-4" autocomplete="off">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Email Field --}}
                        <div class="md:col-span-2 form-group">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="email" autocomplete="off" required class="form-input">
                            <div class="error-text" id="email-error"></div>
                        </div>

                        {{-- Password Field --}}
                        <div class="form-group">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="password-container">
                                <input type="password" name="password" id="password" autocomplete="new-password" required
                                    class="form-input">
                                <button type="button" id="toggle-password" class="password-toggle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Must be at least 8 characters with one uppercase letter, one number, and one special
                                character
                            </p>
                            <div class="error-text" id="password-error"></div>
                        </div>

                        {{-- Confirm Password Field --}}
                        <div class="form-group">
                            <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm
                                Password</label>
                            <div class="password-container">
                                <input type="password" name="confirmPassword" id="confirm-password"
                                    autocomplete="new-password" required class="form-input">
                                <button type="button" id="toggle-confirm-password" class="password-toggle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            <div class="error-text" id="confirm-password-error"></div>
                        </div>

                        {{-- Role Field --}}
                        <div class="form-group">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" id="role" autocomplete="off" required class="form-input">
                                <option value="">Select a role</option>
                                <option value="admin">Admin - Full system access</option>
                                <option value="staff">Staff - Limited access</option>
                            </select>
                            <div class="error-text" id="role-error"></div>
                        </div>

                        {{-- Ministry Field --}}
                        <div class="form-group">
                            <label for="ministry" class="block text-sm font-medium text-gray-700 mb-1">Ministry</label>
                            <select name="ministry" id="ministry" autocomplete="off" required class="form-input">
                                <option value="">-- Select Ministry --</option>
                                @foreach ($ministries as $main)
                                    <optgroup label="{{ $main->ministry_name }}">
                                        @foreach ($main->children as $ministry)
                                            <option value="{{ $ministry->id }}">{{ $ministry->ministry_name }}</option>
                                            @if ($ministry->children->count())
                                                @foreach ($ministry->children as $sub)
                                                    <option value="{{ $sub->id }}">
                                                        &nbsp;&nbsp;&nbsp;→ {{ $sub->ministry_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <div class="error-text" id="ministry-error"></div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Add User
                        </button>
                        <button type="button" id="clear-form" class="btn btn-secondary">
                            Clear Form
                        </button>
                    </div>
                </form>
            </div>

            {{-- Filters and Search --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 sm:space-x-4">
                    {{-- Search --}}
                    <div class="search-container flex-1 max-w-md">
                        <div class="relative">
                            <svg class="search-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="search-input" placeholder="Search by email..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                    </div>

                    {{-- Role Filters --}}
                    <div class="flex space-x-2">
                        <button class="filter-btn btn btn-secondary filter-active" data-role="all">All Roles</button>
                        <button class="filter-btn btn btn-secondary" data-role="admin">Admin</button>
                        <button class="filter-btn btn btn-secondary" data-role="staff">Staff</button>
                    </div>

                    {{-- Sort Options --}}
                    <div>
                        <select id="sort-select"
                            class="text-xs border border-gray-300 rounded-md bg-white px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                            <option value="email-asc">Email (A-Z)</option>
                            <option value="email-desc">Email (Z-A)</option>
                            <option value="date-desc">Newest First</option>
                            <option value="date-asc">Oldest First</option>
                            <option value="role-asc">Role (A-Z)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Current Users Table --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Current Users</h3>
                    <div class="text-sm text-gray-500">
                        Showing <span id="showing-count">{{ count($nonArchivedUsers) }}</span> of <span
                            id="total-count">{{ count($nonArchivedUsers) }}</span> users
                    </div>
                </div>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Ministry</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="roles-tbody">
                            @foreach($nonArchivedUsers as $user)
                                @if($user->id != Auth::id())
                                    <tr data-user-id="{{ $user->id }}">
                                        <td>
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium overflow-hidden">
                                                        @if($user->profile_picture)
                                                            <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                                                alt="{{ $user->email }}"
                                                                class="w-full h-full object-cover rounded-full">
                                                        @else
                                                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($user->email) }}"
                                                                alt="{{ $user->email }}"
                                                                class="w-full h-full object-cover rounded-full">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->email }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                                        </td>
                                        <td class="text-sm text-gray-500">
                                            {{ $user->ministry ? $user->ministry->ministry_name : 'Not Assigned' }}
                                        </td>
                                        <td class="text-sm text-gray-500">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <div class="flex space-x-2">
                                                <button onclick="editUser({{ $user->id }})"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors text-sm">
                                                    Edit
                                                </button>
                                                <button onclick="archiveUser({{ $user->id }}, '{{ $user->email }}')"
                                                    class="text-red-600 hover:text-red-900 transition-colors text-sm">
                                                    Archive
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Toast notifications --}}
        <div id="toast" class="toast"></div>
    </main>

    {{-- Edit Role Modal --}}
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit User Information</h3>
                <button id="close-edit-modal" class="modal-close">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <form id="edit-user-form" class="space-y-4" autocomplete="off">
                @csrf
                <input type="hidden" id="edit-user-id" name="id">
                <div class="form-group">
                    <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="edit-email" required class="form-input">
                    <div class="error-text" id="edit-email-error"></div>
                </div>
                <div class="form-group">
                    <label for="edit-role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="edit-role" required class="form-input">
                        <option value="admin">Admin - Full system access</option>
                        <option value="staff">Staff - Limited access</option>
                    </select>
                    <div class="error-text" id="edit-role-error"></div>
                </div>
                <div class="form-group">
                    <label for="edit-ministry" class="block text-sm font-medium text-gray-700 mb-1">Ministry</label>
                    <select name="ministry" id="edit-ministry" required class="form-input">
                        <option value="">-- Select Ministry --</option>
                        @foreach ($ministries as $main)
                            <optgroup label="{{ $main->ministry_name }}">
                                @foreach ($main->children as $ministry)
                                    <option value="{{ $ministry->id }}">{{ $ministry->ministry_name }}</option>
                                    @if ($ministry->children->count())
                                        @foreach ($ministry->children as $sub)
                                            <option value="{{ $sub->id }}">
                                                &nbsp;&nbsp;&nbsp;→ {{ $sub->ministry_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <div class="error-text" id="edit-ministry-error"></div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-edit" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Archive Modal --}}
    <div id="archive-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <h3 class="modal-title">Archive User</h3>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to archive <span id="archive-user-name" class="font-medium"></span>?
            </p>
            <div class="form-group">
                <label for="archive-reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for
                    archiving</label>
                <textarea id="archive-reason" rows="3" class="form-input" placeholder="Enter reason..."></textarea>
            </div>
            <div class="flex space-x-3 justify-end">
                <button id="cancel-archive" class="btn btn-secondary">Cancel</button>
                <button id="confirm-archive" class="btn btn-primary">Archive User</button>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Role Management JavaScript - Inline Version
        (function () {
            'use strict';

            // Configuration
            const config = {
                users: @json($users),
                storageBaseUrl: "{{ asset('storage') }}",
                csrfToken: "{{ csrf_token() }}",
                currentUserId: {{Auth::id()}},
                routes: {
                    store: "{{ route('roles.store') }}",
                    update: "/settings/role/",
                    archive: "/settings/role/"
                }
            };

            // Global variables
            let users = config.users || [];
            let filteredUsers = [...users];
            let currentFilter = 'all';
            let currentSort = 'email-asc';

            // DOM Elements
            const elements = {
                // Forms
                addRoleForm: document.getElementById('add-role-form'),
                editUserForm: document.getElementById('edit-user-form'),

                // Form inputs
                email: document.getElementById('email'),
                password: document.getElementById('password'),
                confirmPassword: document.getElementById('confirm-password'),
                role: document.getElementById('role'),
                ministry: document.getElementById('ministry'),

                // Edit form inputs
                editUserId: document.getElementById('edit-user-id'),
                editEmail: document.getElementById('edit-email'),
                editRole: document.getElementById('edit-role'),
                editMinistry: document.getElementById('edit-ministry'),

                // Buttons
                toggleFormBtn: document.getElementById('toggle-form'),
                clearFormBtn: document.getElementById('clear-form'),
                closeEditModalBtn: document.getElementById('close-edit-modal'),
                cancelEditBtn: document.getElementById('cancel-edit'),
                cancelArchiveBtn: document.getElementById('cancel-archive'),
                confirmArchiveBtn: document.getElementById('confirm-archive'),

                // Search and filters
                searchInput: document.getElementById('search-input'),
                filterButtons: document.querySelectorAll('.filter-btn'),
                sortSelect: document.getElementById('sort-select'),

                // Table and stats
                rolesTableBody: document.getElementById('roles-tbody'),
                showingCount: document.getElementById('showing-count'),
                totalCount: document.getElementById('total-count'),
                totalUsers: document.getElementById('total-users'),
                adminCount: document.getElementById('admin-count'),

                // Modals
                editModal: document.getElementById('edit-modal'),
                archiveModal: document.getElementById('archive-modal'),
                archiveUserName: document.getElementById('archive-user-name'),
                archiveReason: document.getElementById('archive-reason'),

                // Toast
                toast: document.getElementById('toast'),

                // Password toggles
                togglePassword: document.getElementById('toggle-password'),
                toggleConfirmPassword: document.getElementById('toggle-confirm-password'),

                // Toggle text
                toggleText: document.getElementById('toggle-text')
            };

            // Initialize
            function init() {
                bindEventListeners();
                renderTable();
                updateStats();

                // Set initial filter active state
                if (elements.filterButtons.length > 0) {
                    elements.filterButtons[0].classList.add('filter-active');
                }
            }

            function bindEventListeners() {
                // Form submissions
                if (elements.addRoleForm) {
                    elements.addRoleForm.addEventListener('submit', handleAddUser);
                }

                if (elements.editUserForm) {
                    elements.editUserForm.addEventListener('submit', handleEditUser);
                }

                // Search and filters
                if (elements.searchInput) {
                    elements.searchInput.addEventListener('input', applyFilters);
                }

                if (elements.sortSelect) {
                    elements.sortSelect.addEventListener('change', function () {
                        currentSort = this.value;
                        applyFilters();
                    });
                }

                // Filter buttons
                elements.filterButtons.forEach(btn => {
                    btn.addEventListener('click', function () {
                        elements.filterButtons.forEach(b => b.classList.remove('filter-active'));
                        this.classList.add('filter-active');
                        currentFilter = this.dataset.role;
                        applyFilters();
                    });
                });

                // Form controls
                if (elements.toggleFormBtn) {
                    elements.toggleFormBtn.addEventListener('click', toggleForm);
                }

                if (elements.clearFormBtn) {
                    elements.clearFormBtn.addEventListener('click', clearForm);
                }

                // Modal controls
                if (elements.closeEditModalBtn) {
                    elements.closeEditModalBtn.addEventListener('click', closeEditModal);
                }

                if (elements.cancelEditBtn) {
                    elements.cancelEditBtn.addEventListener('click', closeEditModal);
                }

                if (elements.cancelArchiveBtn) {
                    elements.cancelArchiveBtn.addEventListener('click', closeArchiveModal);
                }

                if (elements.confirmArchiveBtn) {
                    elements.confirmArchiveBtn.addEventListener('click', handleArchiveUser);
                }

                // Password visibility toggles
                if (elements.togglePassword) {
                    elements.togglePassword.addEventListener('click', function () {
                        togglePasswordVisibility('password', 'toggle-password');
                    });
                }

                if (elements.toggleConfirmPassword) {
                    elements.toggleConfirmPassword.addEventListener('click', function () {
                        togglePasswordVisibility('confirm-password', 'toggle-confirm-password');
                    });
                }

                // Modal backdrop clicks
                if (elements.editModal) {
                    elements.editModal.addEventListener('click', function (e) {
                        if (e.target === elements.editModal) {
                            closeEditModal();
                        }
                    });
                }

                if (elements.archiveModal) {
                    elements.archiveModal.addEventListener('click', function (e) {
                        if (e.target === elements.archiveModal) {
                            closeArchiveModal();
                        }
                    });
                }
            }

            function handleAddUser(e) {
                e.preventDefault();

                if (!validateForm()) return;

                const formData = {
                    email: elements.email.value.trim(),
                    password: elements.password.value,
                    role: elements.role.value,
                    ministry_id: elements.ministry.value
                };

                showLoadingState(e.target);

                fetch(config.routes.store, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Server error');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const newUser = {
                                id: data.user.id,
                                email: data.user.email,
                                role: data.user.role,
                                ministry_id: data.user.ministry_id,
                                ministry_name: data.user.ministry_name || null,
                                profile_picture: data.user.profile_picture || null,
                                dateAdded: data.user.created_at
                            };

                            users.push(newUser);
                            applyFilters();
                            updateStats();
                            showToast('User added successfully!', 'success');
                            elements.addRoleForm.reset();
                            clearValidationErrors();
                        } else {
                            showToast(data.message || 'An error occurred', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(error.message || 'An error occurred. Please try again.', 'error');
                    })
                    .finally(() => {
                        hideLoadingState(e.target);
                    });
            }

            function handleEditUser(e) {
                e.preventDefault();

                if (!validateEditForm()) return;

                const userId = elements.editUserId.value;
                const formData = {
                    email: elements.editEmail.value.trim(),
                    role: elements.editRole.value,
                    ministry_id: elements.editMinistry.value
                };

                showLoadingState(e.target);

                fetch(`${config.routes.update}${userId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Server error');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update the user in the local array
                            const userIndex = users.findIndex(u => u.id === parseInt(userId));
                            if (userIndex !== -1) {
                                users[userIndex].email = formData.email;
                                users[userIndex].role = formData.role;
                                users[userIndex].ministry_id = formData.ministry_id;
                                users[userIndex].ministry_name = data.user?.ministry_name || null;
                            }
                            applyFilters();
                            closeEditModal();
                            showToast('User updated successfully!', 'success');
                        } else {
                            showToast(data.message || 'Failed to update user', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(error.message || 'An error occurred. Please try again.', 'error');
                    })
                    .finally(() => {
                        hideLoadingState(e.target);
                    });
            }

            function handleArchiveUser() {
                const userId = elements.archiveModal.dataset.userId;
                const reason = elements.archiveReason.value;

                showLoadingState(elements.confirmArchiveBtn);

                fetch(`${config.routes.archive}${userId}/archive`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reason: reason
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Server error');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            users = users.filter(user => user.id !== parseInt(userId));
                            applyFilters();
                            updateStats();
                            showToast('User archived successfully!', 'success');
                        } else {
                            showToast(data.message || 'Failed to archive user', 'error');
                        }
                        closeArchiveModal();
                    })
                    .catch(error => {
                        showToast(error.message || 'An error occurred. Please try again.', 'error');
                        closeArchiveModal();
                    })
                    .finally(() => {
                        hideLoadingState(elements.confirmArchiveBtn);
                    });
            }

            // Global functions for onclick handlers
            window.editUser = function (userId) {
                const user = users.find(u => u.id === userId);
                if (user && elements.editModal) {
                    elements.editUserId.value = user.id;
                    elements.editEmail.value = user.email;
                    elements.editRole.value = user.role;
                    elements.editMinistry.value = user.ministry_id || '';

                    clearEditValidationErrors();
                    elements.editModal.classList.add('show');
                }
            };

            window.archiveUser = function (userId, userEmail) {
                if (elements.archiveModal && elements.archiveUserName) {
                    elements.archiveUserName.textContent = userEmail;
                    elements.archiveModal.dataset.userId = userId;
                    elements.archiveModal.dataset.userEmail = userEmail;
                    elements.archiveModal.classList.add('show');
                }
            };

            function closeEditModal() {
                if (elements.editModal) {
                    elements.editModal.classList.remove('show');
                    clearEditValidationErrors();
                }
            }

            function closeArchiveModal() {
                if (elements.archiveModal && elements.archiveReason) {
                    elements.archiveModal.classList.remove('show');
                    elements.archiveReason.value = '';
                }
            }

            function toggleForm() {
                if (elements.addRoleForm && elements.toggleText) {
                    const isHidden = elements.addRoleForm.style.display === 'none';
                    elements.addRoleForm.style.display = isHidden ? 'block' : 'none';
                    elements.toggleText.textContent = isHidden ? 'Hide Form' : 'Show Form';
                }
            }

            function clearForm() {
                if (elements.addRoleForm) {
                    elements.addRoleForm.reset();
                    clearValidationErrors();
                }
            }

            function applyFilters() {
                let filtered = [...users];

                // Apply search filter
                if (elements.searchInput) {
                    const searchTerm = elements.searchInput.value.toLowerCase();
                    if (searchTerm) {
                        filtered = filtered.filter(user =>
                            user.email.toLowerCase().includes(searchTerm)
                        );
                    }
                }

                // Apply role filter
                if (currentFilter !== 'all') {
                    filtered = filtered.filter(user => user.role === currentFilter);
                }

                // Apply sorting
                filtered.sort((a, b) => {
                    switch (currentSort) {
                        case 'email-asc':
                            return a.email.localeCompare(b.email);
                        case 'email-desc':
                            return b.email.localeCompare(a.email);
                        case 'date-asc':
                            return new Date(a.dateAdded || a.created_at) - new Date(b.dateAdded || b.created_at);
                        case 'date-desc':
                            return new Date(b.dateAdded || b.created_at) - new Date(a.dateAdded || a.created_at);
                        case 'role-asc':
                            return a.role.localeCompare(b.role);
                        default:
                            return 0;
                    }
                });

                filteredUsers = filtered;
                renderTable();
            }

            function renderTable() {
                if (!elements.rolesTableBody) return;

                // Filter out current user
                const displayUsers = filteredUsers.filter(user => user.id !== config.currentUserId);

                if (displayUsers.length === 0) {
                    elements.rolesTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No users found</td></tr>';
                    if (elements.showingCount) elements.showingCount.textContent = '0';
                } else {
                    if (elements.showingCount) elements.showingCount.textContent = displayUsers.length;

                    elements.rolesTableBody.innerHTML = displayUsers.map(user => `
                    <tr data-user-id="${user.id}">
                        <td>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 mr-3">
                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium overflow-hidden">
                                        ${user.profile_picture ?
                            `<img src="${config.storageBaseUrl}/${user.profile_picture}" alt="${user.email}" class="w-full h-full object-cover rounded-full">`
                            :
                            `<img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(user.email)}" alt="${user.email}" class="w-full h-full object-cover rounded-full">`
                        }
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900">${user.email}</div>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge role-${user.role}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                        </td>
                        <td class="text-sm text-gray-500">
                            ${user.ministry_name || 'Not Assigned'}
                        </td>
                        <td class="text-sm text-gray-500">
                            ${formatDate(user.dateAdded || user.created_at)}
                        </td>
                        <td>
                            <div class="flex space-x-2">
                                <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 transition-colors text-sm">
                                    Edit
                                </button>
                                <button onclick="archiveUser(${user.id}, '${user.email}')" class="text-red-600 hover:text-red-900 transition-colors text-sm">
                                    Archive
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                }

                if (elements.totalCount) {
                    elements.totalCount.textContent = users.filter(user => user.id !== config.currentUserId).length;
                }
            }

            function updateStats() {
                const totalUsers = users.filter(user => user.id !== config.currentUserId).length;
                const adminCount = users.filter(user => user.role === 'admin' && user.id !== config.currentUserId).length;

                if (elements.totalUsers) elements.totalUsers.textContent = totalUsers;
                if (elements.adminCount) elements.adminCount.textContent = adminCount;
            }

            function formatDate(dateString) {
                if (!dateString) return 'N/A';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            function validateForm() {
                clearValidationErrors();
                let isValid = true;

                const email = elements.email.value.trim();
                const password = elements.password.value;
                const confirmPassword = elements.confirmPassword.value;
                const role = elements.role.value;
                const ministry = elements.ministry.value;

                if (!email) {
                    showFieldError('email', 'Email is required');
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    showFieldError('email', 'Please enter a valid email address');
                    isValid = false;
                } else if (users.some(user => user.email === email)) {
                    showFieldError('email', 'Email address already exists');
                    isValid = false;
                }

                if (!password) {
                    showFieldError('password', 'Password is required');
                    isValid = false;
                } else if (password.length < 8) {
                    showFieldError('password', 'Password must be at least 8 characters');
                    isValid = false;
                } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9])/.test(password)) {
                    showFieldError('password', 'Password must contain at least one uppercase letter, one number, and one special character');
                    isValid = false;
                }

                if (!confirmPassword) {
                    showFieldError('confirm-password', 'Please confirm your password');
                    isValid = false;
                } else if (password !== confirmPassword) {
                    showFieldError('confirm-password', 'Passwords do not match');
                    isValid = false;
                }

                if (!role) {
                    showFieldError('role', 'Please select a role');
                    isValid = false;
                }

                if (!ministry) {
                    showFieldError('ministry', 'Please select a ministry');
                    isValid = false;
                }

                return isValid;
            }

            function validateEditForm() {
                clearEditValidationErrors();
                let isValid = true;

                const email = elements.editEmail.value.trim();
                const role = elements.editRole.value;
                const ministry = elements.editMinistry.value;
                const userId = parseInt(elements.editUserId.value);

                if (!email) {
                    showEditFieldError('edit-email', 'Email is required');
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    showEditFieldError('edit-email', 'Please enter a valid email address');
                    isValid = false;
                } else if (users.some(user => user.email === email && user.id !== userId)) {
                    showEditFieldError('edit-email', 'Email address already exists');
                    isValid = false;
                }

                if (!role) {
                    showEditFieldError('edit-role', 'Please select a role');
                    isValid = false;
                }

                if (!ministry) {
                    showEditFieldError('edit-ministry', 'Please select a ministry');
                    isValid = false;
                }

                return isValid;
            }

            function showFieldError(fieldName, message) {
                const errorElement = document.getElementById(fieldName + '-error');
                const inputElement = document.getElementById(fieldName);

                if (errorElement && inputElement) {
                    errorElement.textContent = message;
                    errorElement.classList.add('show');
                    inputElement.classList.add('error');
                }
            }

            function showEditFieldError(fieldName, message) {
                const errorElement = document.getElementById(fieldName + '-error');
                const inputElement = document.getElementById(fieldName);

                if (errorElement && inputElement) {
                    errorElement.textContent = message;
                    errorElement.classList.add('show');
                    inputElement.classList.add('error');
                }
            }

            function clearValidationErrors() {
                const errorElements = document.querySelectorAll('.error-text');
                const inputElements = document.querySelectorAll('.form-input');

                errorElements.forEach(el => {
                    el.classList.remove('show');
                    el.textContent = '';
                });

                inputElements.forEach(el => {
                    el.classList.remove('error');
                });
            }

            function clearEditValidationErrors() {
                const errorElements = ['edit-email-error', 'edit-role-error', 'edit-ministry-error'];
                const inputElements = ['edit-email', 'edit-role', 'edit-ministry'];

                errorElements.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.classList.remove('show');
                        el.textContent = '';
                    }
                });

                inputElements.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.classList.remove('error');
                    }
                });
            }

            function togglePasswordVisibility(inputId, buttonId) {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);

                if (!input || !button) return;

                const icon = button.querySelector('svg');

                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>`;
                    }
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>`;
                    }
                }
            }

            function showToast(message, type = 'success') {
                if (elements.toast) {
                    elements.toast.textContent = message;
                    elements.toast.className = `toast toast-${type} show`;

                    setTimeout(() => {
                        elements.toast.classList.remove('show');
                    }, 3000);
                }
            }

            function showLoadingState(element) {
                if (element) {
                    element.disabled = true;
                    element.style.opacity = '0.6';
                    element.style.cursor = 'not-allowed';
                }
            }

            function hideLoadingState(element) {
                if (element) {
                    element.disabled = false;
                    element.style.opacity = '1';
                    element.style.cursor = 'pointer';
                }
            }

            // Initialize when DOM is loaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

        })();
    </script>
@endsection