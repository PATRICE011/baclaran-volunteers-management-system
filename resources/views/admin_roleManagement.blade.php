{{-- resources/views/roles.blade.php --}}
@extends('components.layout')

@section('title', 'Role Management')

@section('styles')
<style>
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
            <div class="mt-4 sm:mt-0 flex space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600" id="total-users">3</div>
                    <div class="text-xs text-gray-500">Total Users</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600" id="admin-count">1</div>
                    <div class="text-xs text-gray-500">Admins</div>
                </div>
            </div>
        </div>

        {{-- Add New Role Form --}}
        <div class="mb-8 p-6 border border-gray-200 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add New User</h3> {{-- Updated title --}}
                <button type="button" id="toggle-form" class="text-blue-600 hover:text-blue-800 text-sm">
                    <span id="toggle-text">Hide Form</span>
                </button>
            </div>

            <form id="add-role-form" class="space-y-4" autocomplete="off">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" id="first_name" autocomplete="off" required
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                        <span class="text-red-500 text-xs hidden" id="first_name-error"></span>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" id="last_name" autocomplete="off" required
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                        <span class="text-red-500 text-xs hidden" id="last_name-error"></span>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" autocomplete="off" required
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                        <span class="text-red-500 text-xs hidden" id="email-error"></span>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" autocomplete="new-password" required
                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                            <button type="button" id="toggleCurrentPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-sm">
                                <svg id="currentPasswordToggle" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                                </svg>
                            </button>
                        </div>
                        <!-- <p class="mt-1 text-xs text-gray-500">
                            Must be at least 8 characters with one uppercase letter, one number, and one special character
                        </p> -->
                        <span class="text-red-500 text-xs hidden" id="password-error"></span>
                    </div>

                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="confirmPassword" id="confirmPassword" autocomplete="new-password" required
                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                            <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-sm">
                                <svg id="currentPasswordToggle" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                                </svg>
                            </button>
                        </div>
                        <span class="text-red-500 text-xs hidden" id="confirm-password-error"></span>
                    </div>

                    <div class="md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role" autocomplete="off" required
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                            <option value="">Select a role</option>
                            <option value="admin">Admin - Full system access</option>
                            <option value="staff">Staff - Limited access</option>
                        </select>
                        <span class="text-red-500 text-xs hidden" id="role-error"></span>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Role
                    </button>
                    <button type="button" id="clear-form"
                        class="inline-flex items-center justify-center rounded-md bg-gray-200 px-6 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Clear Form
                    </button>
                </div>
            </form>

        </div>

        {{-- Filters and Search --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 sm:space-x-4">

                {{-- Search --}}
                <div class="search-container flex-1 max-w-md">
                    <div class="relative">
                        <svg class="search-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="search-input" placeholder="Search by name or email..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                    </div>
                </div>

                {{-- Role Filters --}}
                <div class="flex space-x-2">
                    <button class="filter-btn px-3 py-2 text-xs font-medium border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500" data-role="all">
                        All Roles
                    </button>
                    <button class="filter-btn px-3 py-2 text-xs font-medium border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500" data-role="admin">
                        Admin
                    </button>
                    <button class="filter-btn px-3 py-2 text-xs font-medium border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500" data-role="staff">
                        Staff
                    </button>
                </div>

                {{-- Sort Options --}}
                <div>
                    <select id="sort-select" class="text-xs border border-gray-300 rounded-md bg-white px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                        <option value="date-desc">Newest First</option>
                        <option value="date-asc">Oldest First</option>
                        <option value="role-asc">Role (A-Z)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Current Roles --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Current Roles</h3>
                <div class="text-sm text-gray-500">
                    Showing <span id="showing-count">3</span> of <span id="total-count">3</span> users
                </div>
            </div>

            <div class="overflow-x-auto bg-white rounded-lg border border-gray-200">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Added</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="roles-tbody" class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium">
                                            @if($user->profile_picture)
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                                alt="{{$user->full_name }}"
                                                class="w-full h-full object-cover">
                                            @else
                                            {{-- If no profile picture, use DiceBear for default avatar --}}
                                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($user->full_name) }}"
                                                alt="{{ $user->full_name }}"
                                                class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900 transition-colors">
                                        Edit
                                    </button>
                                    <button onclick="deleteUser({{ $user->id }}, '{{ $user->first_name }} {{ $user->last_name }}')" class="text-red-600 hover:text-red-900 transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Toast notifications --}}
    <div id="toast" class="toast"></div>

</main>

{{-- Confirmation Modal --}}
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-md w-full">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Confirm Deletion</h3>
            </div>
        </div>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span id="delete-user-name" class="font-medium"></span>? This action cannot be undone.</p>
        <div class="flex space-x-3 justify-end">
            <button id="cancel-delete" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Cancel
            </button>
            <button id="confirm-delete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                Delete User
            </button>
        </div>
    </div>
</div>


@endsection
@section('scripts')
<script src="{{ asset('assets/js/roles.js') }}"></script>
@endsection