@extends('components.layout')
@section('title','Task and Assignment Monitoring')
@section('styles')
@endsection
@section('content')
@include('components.navs')
<div class="md:ml-64">
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Task & Assignment Monitoring</h1>
                        <p class="mt-2 text-gray-600">Manage and track tasks across your organization</p>
                    </div>
                    <button onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Task
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                            <p class="text-3xl font-bold text-gray-900">24</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">To Do</p>
                            <p class="text-3xl font-bold text-orange-600">8</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">In Progress</p>
                            <p class="text-3xl font-bold text-yellow-600">10</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-3xl font-bold text-green-600">6</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-200">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" placeholder="Search tasks..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="To Do">To Do</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Volunteers</option>
                            <option value="1">John Smith</option>
                            <option value="2">Sarah Johnson</option>
                            <option value="3">Mike Wilson</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Ministries</option>
                            <option value="youth">Youth Ministry</option>
                            <option value="worship">Worship Ministry</option>
                            <option value="outreach">Outreach Ministry</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tasks Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Task Card 1 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Youth Event Planning</h3>
                            <p class="text-gray-600 text-sm">Organize and coordinate the upcoming youth retreat including venue booking, activities, and logistics.</p>
                        </div>
                        <div class="ml-4">
                            <button class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            In Progress
                        </span>
                        <span class="text-sm text-gray-500">Due: Dec 15, 2024</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex -space-x-2">
                                <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=John+Smith&background=3b82f6&color=fff" alt="John Smith">
                                <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=ef4444&color=fff" alt="Sarah Johnson">
                            </div>
                            <span class="ml-2 text-sm text-gray-600">Youth Ministry</span>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editTask(1)" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteTask(1)" class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Task Card 2 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Website Content Update</h3>
                            <p class="text-gray-600 text-sm">Update church website with new sermon series information and upcoming events.</p>
                        </div>
                        <div class="ml-4">
                            <button class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            To Do
                        </span>
                        <span class="text-sm text-gray-500">Due: Dec 20, 2024</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex -space-x-2">
                                <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Mike+Wilson&background=10b981&color=fff" alt="Mike Wilson">
                            </div>
                            <span class="ml-2 text-sm text-gray-600">Media Ministry</span>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editTask(2)" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteTask(2)" class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Task Card 3 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Community Outreach Program</h3>
                            <p class="text-gray-600 text-sm">Coordinate food drive for local homeless shelter and organize volunteer schedule.</p>
                        </div>
                        <div class="ml-4">
                            <button class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Completed
                        </span>
                        <span class="text-sm text-gray-500">Due: Dec 10, 2024</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex -space-x-2">
                                <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Lisa+Davis&background=8b5cf6&color=fff" alt="Lisa Davis">
                                <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Tom+Brown&background=f59e0b&color=fff" alt="Tom Brown">
                                <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=Amy+White&background=ec4899&color=fff" alt="Amy White">
                            </div>
                            <span class="ml-2 text-sm text-gray-600">Outreach Ministry</span>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editTask(3)" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteTask(3)" class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Task Modal -->
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 id="modalTitle" class="text-2xl font-bold text-gray-900">Add New Task</h2>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="taskForm">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Task Title</label>
                                <input type="text" id="taskTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter task title">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="taskDescription" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter task description"></textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                                    <input type="date" id="taskDueDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select id="taskStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="To Do">To Do</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Type</label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="assignmentType" value="volunteers" class="form-radio text-blue-600" checked>
                                        <span class="ml-2">Assign to Volunteers</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="assignmentType" value="ministry" class="form-radio text-blue-600">
                                        <span class="ml-2">Assign to Ministry</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div id="volunteerSection">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Volunteers</label>
                                <div class="border border-gray-300 rounded-lg p-3 max-h-40 overflow-y-auto">
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" class="form-checkbox text-blue-600" value="1">
                                            <span class="ml-2 text-sm">John Smith</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" class="form-checkbox text-blue-600" value="2">
                                            <span class="ml-2 text-sm">Sarah Johnson</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" class="form-checkbox text-blue-600" value="3">
                                            <span class="ml-2 text-sm">Mike Wilson</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" class="form-checkbox text-blue-600" value="4">
                                            <span class="ml-2 text-sm">Lisa Davis</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" class="form-checkbox text-blue-600" value="5">
                                            <span class="ml-2 text-sm">Tom Brown</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" class="form-checkbox text-blue-600" value="6">
                                            <span class="ml-2 text-sm">Amy White</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="ministrySection" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Ministry</label>
                                <select id="ministrySelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select a ministry</option>
                                    <option value="youth">Youth Ministry</option>
                                    <option value="worship">Worship Ministry</option>
                                    <option value="outreach">Outreach Ministry</option>
                                    <option value="media">Media Ministry</option>
                                    <option value="children">Children's Ministry</option>
                                    <option value="prayer">Prayer Ministry</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                            <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                Save Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modal Functions
    function openAddModal() {
        document.getElementById('taskModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Add New Task';
        document.getElementById('taskForm').reset();
    }

    function closeModal() {
        document.getElementById('taskModal').classList.add('hidden');
    }

    function editTask(taskId) {
        // Here you would fetch task data and populate the form
        document.getElementById('taskModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Edit Task';
        // Populate form with task data
    }

    function deleteTask(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            // Handle task deletion
            console.log('Deleting task:', taskId);
        }
    }

    // Assignment type toggle
    document.addEventListener('DOMContentLoaded', function() {
        const assignmentTypeRadios = document.querySelectorAll('input[name="assignmentType"]');
        const volunteerSection = document.getElementById('volunteerSection');
        const ministrySection = document.getElementById('ministrySection');

        assignmentTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'volunteers') {
                    volunteerSection.classList.remove('hidden');
                    ministrySection.classList.add('hidden');
                } else {
                    volunteerSection.classList.add('hidden');
                    ministrySection.classList.remove('hidden');
                }
            });
        });

        // Form submission
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('title', document.getElementById('taskTitle').value);
            formData.append('description', document.getElementById('taskDescription').value);
            formData.append('due_date', document.getElementById('taskDueDate').value);
            formData.append('status', document.getElementById('taskStatus').value);
            
            const assignmentType = document.querySelector('input[name="assignmentType"]:checked').value;
            
            if (assignmentType === 'volunteers') {
                const selectedVolunteers = [];
                document.querySelectorAll('#volunteerSection input[type="checkbox"]:checked').forEach(checkbox => {
                    selectedVolunteers.push(checkbox.value);
                });
                formData.append('volunteers', JSON.stringify(selectedVolunteers));
            } else {
                formData.append('ministry', document.getElementById('ministrySelect').value);
            }
            
            // Here you would send the data to your Laravel backend
            console.log('Form data:', Object.fromEntries(formData));
            
            // Example AJAX call (uncomment and modify as needed):
            /*
            fetch('/tasks', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                closeModal();
                // Reload the page or update the UI
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
            });
            */
            
            // For demo purposes, just close the modal
            closeModal();
            alert('Task saved successfully!');
        });

        // Close modal when clicking outside
        document.getElementById('taskModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    });
</script>
@endsection