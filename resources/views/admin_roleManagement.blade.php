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
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
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
    
    .role-admin { background-color: #ef4444; color: white; }
    .role-editor { background-color: #f59e0b; color: white; }
    .role-viewer { background-color: #10b981; color: white; }
    
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
    
    .toast-success { background-color: #10b981; }
    .toast-error { background-color: #ef4444; }
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

        {{-- Add New Role --}}
        <div class="mb-8 p-6 border border-gray-200 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add New Role</h3>
                <button type="button" id="toggle-form" class="text-blue-600 hover:text-blue-800 text-sm">
                    <span id="toggle-text">Hide Form</span>
                </button>
            </div>
            
            <form id="add-role-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="name" required
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                        <span class="text-red-500 text-xs hidden" id="name-error"></span>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" required
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                        <span class="text-red-500 text-xs hidden" id="email-error"></span>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="password" required
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                        <span class="text-red-500 text-xs hidden" id="password-error"></span>
                    </div>

                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="confirmPassword" id="confirmPassword" required
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                        <span class="text-red-500 text-xs hidden" id="confirm-password-error"></span>
                    </div>

                    <div class="md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role"
                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors">
                            <option value="">Select a role</option>
                            <option value="Admin">Admin - Full system access</option>
                            <option value="Editor">Editor - Content management</option>
                            <option value="Viewer">Viewer - Read-only access</option>
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
                    <button class="filter-btn px-3 py-2 text-xs font-medium border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500" data-role="Admin">
                        Admin
                    </button>
                    <button class="filter-btn px-3 py-2 text-xs font-medium border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500" data-role="Editor">
                        Editor
                    </button>
                    <button class="filter-btn px-3 py-2 text-xs font-medium border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500" data-role="Viewer">
                        Viewer
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="roles-tbody" class="bg-white divide-y divide-gray-200">
                        {{-- Dynamic content will be inserted here by JavaScript --}}
                    </tbody>
                </table>
                
                {{-- No results message --}}
                <div id="no-results" class="hidden text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m-2 0l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No users found matching your criteria.</p>
                    <button id="clear-filters" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">Clear all filters</button>
                </div>
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

<script>
// Sample data - replace with actual data from Laravel
let users = [
    { id: 1, name: 'John Doe', email: 'john.doe@example.com', role: 'Admin', dateAdded: '2023-05-15', status: 'Active' },
    { id: 2, name: 'Jane Smith', email: 'jane.smith@example.com', role: 'Editor', dateAdded: '2023-06-20', status: 'Active' },
    { id: 3, name: 'Mike Johnson', email: 'mike.johnson@example.com', role: 'Viewer', dateAdded: '2023-07-10', status: 'Inactive' }
];

let filteredUsers = [...users];
let currentFilter = 'all';
let currentSort = 'name-asc';

// DOM Elements
const searchInput = document.getElementById('search-input');
const filterButtons = document.querySelectorAll('.filter-btn');
const sortSelect = document.getElementById('sort-select');
const rolesTableBody = document.getElementById('roles-tbody');
const noResults = document.getElementById('no-results');
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
        // Simulate adding user
        const formData = new FormData(this);
        const newUser = {
            id: users.length + 1,
            name: formData.get('name'),
            email: formData.get('email'),
            role: formData.get('role'),
            dateAdded: new Date().toISOString().split('T')[0],
            status: 'Active'
        };
        
        users.push(newUser);
        applyFilters();
        updateStats();
        showToast('User added successfully!', 'success');
        this.reset();
        clearValidationErrors();
    }
});

// Clear filters
document.getElementById('clear-filters').addEventListener('click', function() {
    searchInput.value = '';
    currentFilter = 'all';
    currentSort = 'name-asc';
    sortSelect.value = 'name-asc';
    
    // Reset filter buttons
    filterButtons.forEach(b => b.classList.remove('filter-active'));
    filterButtons[0].classList.add('filter-active');
    
    applyFilters();
});

// Apply filters and search
function applyFilters() {
    let filtered = [...users];
    
    // Apply search filter
    const searchTerm = searchInput.value.toLowerCase();
    if (searchTerm) {
        filtered = filtered.filter(user => 
            user.name.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm)
        );
    }
    
    // Apply role filter
    if (currentFilter !== 'all') {
        filtered = filtered.filter(user => user.role === currentFilter);
    }
    
    // Apply sorting
    filtered.sort((a, b) => {
        switch (currentSort) {
            case 'name-asc':
                return a.name.localeCompare(b.name);
            case 'name-desc':
                return b.name.localeCompare(a.name);
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
        rolesTableBody.innerHTML = '';
        noResults.classList.remove('hidden');
        showingCount.textContent = '0';
    } else {
        noResults.classList.add('hidden');
        showingCount.textContent = filteredUsers.length;
        
        rolesTableBody.innerHTML = filteredUsers.map(user => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium">
                                ${user.name.charAt(0)}
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">${user.name}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${user.email}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="role-badge role-${user.role.toLowerCase()}">${user.role}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${formatDate(user.dateAdded)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${user.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${user.status}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 transition-colors">
                            Edit
                        </button>
                        <button onclick="deleteUser(${user.id}, '${user.name}')" class="text-red-600 hover:text-red-900 transition-colors">
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    totalCount.textContent = users.length;
}

// Update statistics
function updateStats() {
    const totalUsers = users.length;
    const adminCount = users.filter(user => user.role === 'Admin').length;
    
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
    
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const role = document.getElementById('role').value;
    
    if (!name) {
        showFieldError('name', 'Name is required');
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
    } else if (password.length < 6) {
        showFieldError('password', 'Password must be at least 6 characters');
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

// Delete user functionality
function deleteUser(userId, userName) {
    document.getElementById('delete-user-name').textContent = userName;
    deleteModal.classList.remove('hidden');
    deleteModal.classList.add('flex');
    
    // Store the user ID for deletion
    deleteModal.dataset.userId = userId;
}

// Modal event listeners
document.getElementById('cancel-delete').addEventListener('click', function() {
    closeDeleteModal();
});

document.getElementById('confirm-delete').addEventListener('click', function() {
    const userId = parseInt(deleteModal.dataset.userId);
    users = users.filter(user => user.id !== userId);
    applyFilters();
    updateStats();
    closeDeleteModal();
    showToast('User deleted successfully!', 'success');
});

function closeDeleteModal() {
    deleteModal.classList.add('hidden');
    deleteModal.classList.remove('flex');
}

// Edit user (placeholder)
function editUser(userId) {
    showToast('Edit functionality would be implemented here', 'success');
}

// Toast notification
function showToast(message, type = 'success') {
    toast.textContent = message;
    toast.className = `toast toast-${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Close modal when clicking outside
deleteModal.addEventListener('click', function(e) {
    if (e.target === deleteModal) {
        closeDeleteModal();
    }
});
</script>
@endsection