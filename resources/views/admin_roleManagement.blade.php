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
                        @foreach($nonArchivedUsers as $user)
                        @if($user->id != Auth::id())
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
                                    <button onclick="archiveUser({{ $user->id }}, '{{ $user->first_name }} {{ $user->last_name }}')"
                                        class="text-red-600 hover:text-red-900 transition-colors">
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
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-md w-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit User Information</h3>
            <button id="close-edit-modal" class="text-gray-400 hover:text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="edit-user-form" class="space-y-4" autocomplete="off">
            @csrf
            <input type="hidden" id="edit-user-id" name="id">
            <div>
                <label for="edit-first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="first_name" id="edit-first-name" required
                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                <span class="text-red-500 text-xs hidden" id="edit-first-name-error"></span>
            </div>
            <div>
                <label for="edit-last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" id="edit-last-name" required
                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                <span class="text-red-500 text-xs hidden" id="edit-last-name-error"></span>
            </div>
            <div>
                <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="edit-email" required
                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                <span class="text-red-500 text-xs hidden" id="edit-email-error"></span>
            </div>
            <div>
                <label for="edit-role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="edit-role" required
                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                    <option value="admin">Admin - Full system access</option>
                    <option value="staff">Staff - Limited access</option>
                </select>
                <span class="text-red-500 text-xs hidden" id="edit-role-error"></span>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-edit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- archive modal -->
<div id="archive-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-md w-full">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Archive User</h3>
            </div>
        </div>
        <p class="text-sm text-gray-500 mb-4">Are you sure you want to archive <span id="archive-user-name" class="font-medium"></span>?</p>
        <div class="mb-4">
            <label for="archive-reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for archiving</label>
            <textarea id="archive-reason" rows="3" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors" placeholder="Enter reason..."></textarea>
        </div>
        <div class="flex space-x-3 justify-end">
            <button id="cancel-archive" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Cancel
            </button>
            <button id="confirm-archive" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Archive User
            </button>
        </div>
    </div>
</div>

@endsection
@section('scripts')

<script>
    const storageBaseUrl = "{{ asset('storage') }}";
    let users = @json($users);
    // Edit modal elements
    const editModal = document.getElementById('edit-modal');
    const closeEditModalBtn = document.getElementById('close-edit-modal');
    const cancelEditBtn = document.getElementById('cancel-edit');
    const editUserForm = document.getElementById('edit-user-form');
    const editUserId = document.getElementById('edit-user-id');
    const editFirstName = document.getElementById('edit-first-name');
    const editLastName = document.getElementById('edit-last-name');
    const editEmail = document.getElementById('edit-email');
    const editRole = document.getElementById('edit-role');

    // Error elements for edit form
    const editFirstNameError = document.getElementById('edit-first-name-error');
    const editLastNameError = document.getElementById('edit-last-name-error');
    const editEmailError = document.getElementById('edit-email-error');
    const editRoleError = document.getElementById('edit-role-error');

    // Open edit modal
    function editUser(userId) {
        const user = users.find(u => u.id === userId);
        if (user) {
            editUserId.value = user.id;
            editFirstName.value = user.first_name;
            editLastName.value = user.last_name;
            editEmail.value = user.email;
            editRole.value = user.role;

            // Clear previous validation errors
            clearEditValidationErrors();

            // Show modal
            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
        }
    }

    // Close edit modal
    function closeEditModal() {
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
    }

    // Clear edit validation errors
    function clearEditValidationErrors() {
        const errorElements = [
            editFirstNameError,
            editLastNameError,
            editEmailError,
            editRoleError
        ];

        const inputElements = [
            editFirstName,
            editLastName,
            editEmail,
            editRole
        ];

        errorElements.forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });

        inputElements.forEach(el => {
            el.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        });
    }

    // Validate edit form
    function validateEditForm() {
        clearEditValidationErrors();
        let isValid = true;

        const firstName = editFirstName.value.trim();
        const lastName = editLastName.value.trim();
        const email = editEmail.value.trim();
        const role = editRole.value;
        const userId = parseInt(editUserId.value);

        if (!firstName) {
            showEditFieldError('edit-first-name', 'First name is required');
            isValid = false;
        }

        if (!lastName) {
            showEditFieldError('edit-last-name', 'Last name is required');
            isValid = false;
        }

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

        return isValid;
    }

    function showEditFieldError(fieldName, message) {
        const errorElement = document.getElementById(fieldName + '-error');
        const inputElement = document.getElementById(fieldName);

        if (errorElement && inputElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            inputElement.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        }
    }

    // Event listeners for closing the edit modal
    closeEditModalBtn.addEventListener('click', closeEditModal);
    cancelEditBtn.addEventListener('click', closeEditModal);
    editModal.addEventListener('click', function(e) {
        if (e.target === editModal) {
            closeEditModal();
        }
    });

    // Edit form submission
    editUserForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateEditForm()) return;

        const userId = editUserId.value;
        const formData = {
            first_name: editFirstName.value.trim(),
            last_name: editLastName.value.trim(),
            email: editEmail.value.trim(),
            role: editRole.value
        };

        fetch(`/settings/role/${userId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                        users[userIndex].first_name = formData.first_name;
                        users[userIndex].last_name = formData.last_name;
                        users[userIndex].email = formData.email;
                        users[userIndex].role = formData.role;
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
            });
    });

    function archiveUser(userId, userName) {
        document.getElementById('archive-user-name').textContent = userName;
        const archiveModal = document.getElementById('archive-modal');
        archiveModal.classList.remove('hidden');
        archiveModal.classList.add('flex');
        archiveModal.dataset.userId = userId;
        // Store the user ID and name for later removal
        archiveModal.dataset.userName = userName;
    }

    // Close Archive Modal
    document.getElementById('cancel-archive').addEventListener('click', function() {
        document.getElementById('archive-modal').classList.add('hidden');
        document.getElementById('archive-reason').value = ''; // Clear the reason
    });

    // Confirm Archive
    document.getElementById('confirm-archive').addEventListener('click', function() {
        const userId = document.getElementById('archive-modal').dataset.userId;
        const userName = document.getElementById('archive-modal').dataset.userName;
        const reason = document.getElementById('archive-reason').value;

        fetch(`/settings/role/${userId}/archive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove user from the users array
                    users = users.filter(user => user.id !== parseInt(userId));

                    // Re-apply filters and re-render
                    applyFilters();
                    updateStats();

                    showToast('User archived successfully!', 'success');
                } else {
                    showToast(data.message || 'Failed to archive user', 'error');
                }
                document.getElementById('archive-modal').classList.add('hidden');
            })
            .catch(error => {
                showToast('An error occurred. Please try again.', 'error');
                document.getElementById('archive-modal').classList.add('hidden');
            });
    });
    let filteredUsers = [...users];
    let currentFilter = 'all';
    let currentSort = 'name-asc';

    // DOM Elements
    const searchInput = document.getElementById('search-input');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const sortSelect = document.getElementById('sort-select');
    const rolesTableBody = document.getElementById('roles-tbody');
    const showingCount = document.getElementById('showing-count');
    const totalCount = document.getElementById('total-count');
    const addRoleForm = document.getElementById('add-role-form');
    const toggleFormBtn = document.getElementById('toggle-form');
    const clearFormBtn = document.getElementById('clear-form');
    const deleteModal = document.getElementById('delete-modal');
    const toast = document.getElementById('toast');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        renderTable();
        updateStats();

        // Set initial active filter
        filterButtons[0].classList.add('filter-active');
    });

    // Search functionality
    searchInput.addEventListener('input', function() {
        applyFilters();
    });

    // Filter buttons
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(b => b.classList.remove('filter-active'));
            // Add active class to clicked button
            this.classList.add('filter-active');

            currentFilter = this.dataset.role;
            applyFilters();
        });
    });

    // Sort functionality
    sortSelect.addEventListener('change', function() {
        currentSort = this.value;
        applyFilters();
    });

    // Form toggle
    toggleFormBtn.addEventListener('click', function() {
        const form = addRoleForm;
        const toggleText = document.getElementById('toggle-text');

        if (form.style.display === 'none') {
            form.style.display = 'block';
            toggleText.textContent = 'Hide Form';
        } else {
            form.style.display = 'none';
            toggleText.textContent = 'Show Form';
        }
    });

    // Clear form
    clearFormBtn.addEventListener('click', function() {
        addRoleForm.reset();
        clearValidationErrors();
    });

    // Form submission
    addRoleForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (validateForm()) {
            const formData = new FormData(this);

            fetch('{{ route("roles.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        first_name: formData.get('first_name'),
                        last_name: formData.get('last_name'),
                        email: formData.get('email'),
                        password: formData.get('password'),
                        role: formData.get('role')
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
                        // Add new user to local array
                        const newUser = {
                            id: data.user.id,
                            first_name: data.user.first_name,
                            last_name: data.user.last_name,
                            email: data.user.email,
                            role: data.user.role,
                            dateAdded: data.user.created_at
                        };

                        users.push(newUser);
                        applyFilters();
                        updateStats();
                        showToast('User added successfully!', 'success');
                        this.reset();
                    } else {
                        showToast(data.message || 'An error occurred', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'An error occurred. Please try again.', 'error');
                });
        }
    });
    // Apply filters and search
    function applyFilters() {
        let filtered = [...users];

        // Apply search filter
        const searchTerm = searchInput.value.toLowerCase();
        if (searchTerm) {
            filtered = filtered.filter(user =>
                (user.first_name + ' ' + user.last_name).toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm)
            );
        }

        // Apply role filter
        if (currentFilter !== 'all') {
            filtered = filtered.filter(user => user.role === currentFilter);
        }

        // Apply sorting
        filtered.sort((a, b) => {
            const nameA = (a.first_name + ' ' + a.last_name).toLowerCase();
            const nameB = (b.first_name + ' ' + b.last_name).toLowerCase();

            switch (currentSort) {
                case 'name-asc':
                    return nameA.localeCompare(nameB);
                case 'name-desc':
                    return nameB.localeCompare(nameA);
                case 'date-asc':
                    return new Date(a.dateAdded) - new Date(b.dateAdded);
                case 'date-desc':
                    return new Date(b.dateAdded) - new Date(a.dateAdded);
                case 'role-asc':
                    return a.role.localeCompare(b.role);
                default:
                    return 0;
            }
        });

        filteredUsers = filtered;
        renderTable();
    }

    // Render table
    function renderTable() {
        if (filteredUsers.length === 0) {
            rolesTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No users found</td></tr>';
            showingCount.textContent = '0';
        } else {
            showingCount.textContent = filteredUsers.length;

            rolesTableBody.innerHTML = filteredUsers.map(user => `
            <tr class="hover:bg-gray-50 transition-colors" data-user-id="${user.id}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium overflow-hidden">
                                ${user.profile_picture ? 
                                    `<img src="${storageBaseUrl}/${user.profile_picture}" alt="${user.first_name} ${user.last_name}" class="w-full h-full object-cover">` 
                                    : 
                                    `<img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(user.first_name + ' ' + user.last_name)}" alt="${user.first_name} ${user.last_name}" class="w-full h-full object-cover">`
                                }
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">${user.first_name} ${user.last_name}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${user.email}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="role-badge role-${user.role}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${formatDate(user.dateAdded)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 transition-colors">
                            Edit
                        </button>
                        <button onclick="archiveUser(${user.id}, '${user.first_name} ${user.last_name}')"
                            class="text-red-600 hover:text-red-900 transition-colors">
                            Archive
                        </button>
                    </div>
                </td>
            </tr>
            `).join('');
        }

        totalCount.textContent = users.length;
    }

    function updateStats() {
        const currentUserId = {{ Auth::id() }};
        const totalUsers = users.filter(user => user.id !== currentUserId).length;
        const adminCount = users.filter(user => user.role === 'admin' && user.id !== currentUserId).length;

        document.getElementById('total-users').textContent = totalUsers;
        document.getElementById('admin-count').textContent = adminCount;
    }
    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Form validation
    function validateForm() {
        clearValidationErrors();
        let isValid = true;

        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const role = document.getElementById('role').value;

        if (!firstName) {
            showFieldError('first_name', 'First name is required');
            isValid = false;
        }

        if (!lastName) {
            showFieldError('last_name', 'Last name is required');
            isValid = false;
        }

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

        return isValid;
    }

    function showFieldError(fieldName, message) {
        const errorElement = document.getElementById(fieldName + '-error');
        const inputElement = document.getElementById(fieldName === 'confirm-password' ? 'confirmPassword' : fieldName);

        if (errorElement && inputElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            inputElement.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        }
    }

    function clearValidationErrors() {
        const errorElements = document.querySelectorAll('[id$="-error"]');
        const inputElements = document.querySelectorAll('input, select');

        errorElements.forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });

        inputElements.forEach(el => {
            el.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        });
    }

    // Toast notification
    function showToast(message, type = 'success') {
        toast.textContent = message;
        toast.className = `toast toast-${type} show`;

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
    // Toggle password visibility functions
    function togglePasswordVisibility(inputId, toggleButtonId) {
        const input = document.getElementById(inputId);
        const toggleButton = document.getElementById(toggleButtonId);
        const icon = toggleButton.querySelector("svg");

        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>`;
        } else {
            input.type = "password";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88">`;
        }
    }

    // Attach event listeners for password visibility toggles
    document.getElementById("toggleCurrentPassword")?.addEventListener("click", function() {
        togglePasswordVisibility(
            "password",
            "toggleCurrentPassword"
        );
    });
    document.getElementById("toggleConfirmPassword")?.addEventListener("click", function() {
        togglePasswordVisibility(
            "confirmPassword",
            "toggleConfirmPassword"
        );
    });
</script>
@endsection