@extends('components.layout')
@section('title', 'Dashboard')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin_dashboard.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@section('content')

@php
$current = request()->is('dashboard') ? 'active' : '';
@endphp

<!-- Include Navigation -->
@include('components.navs')

<!-- Main Content Container -->
<div class="md:ml-64">
    <!-- Dashboard Content -->
    <main class="p-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Volunteers Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Volunteers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $metrics['totalVolunteers'] }}</p>
                        <p class="text-xs text-green-600 mt-1 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            {{ $metrics['activeVolunteers'] }} currently active
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Upcoming Events</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $metrics['upcomingEvents'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Next 30 days</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Task Completion Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Task Completion</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $metrics['taskCompletionRate'] }}%</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $metrics['taskCompletionRate'] }}%"></div>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Active Ministries Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Ministries</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $metrics['activeMinistries'] }}</p>
                        <div class="flex items-center space-x-1 mt-2">
                            @foreach($metrics['ministryData'] as $ministry)
                                <div class="w-2 h-2 {{ $ministry['color'] }} rounded-full"></div>
                            @endforeach
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hands-praying text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6">
                    <button class="py-4 px-1 border-b-2 border-blue-600 text-blue-600 font-medium text-sm tab-button active" data-target="overview">
                        <i class="fas fa-chart-line mr-2"></i>Overview
                    </button>
                    <button class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm tab-button" data-target="volunteers">
                        <i class="fas fa-users mr-2"></i>Volunteers
                    </button>
                    <button class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm tab-button" data-target="tasks">
                        <i class="fas fa-tasks mr-2"></i>Tasks
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div id="overview" class="p-6 tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Volunteer Activity Chart -->
                    <div class="lg:col-span-2">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Volunteer Activity</h3>
                            <p class="text-sm text-gray-600">Volunteer participation over the last 30 days</p>
                        </div>
                        <div class="h-64">
                            <canvas id="volunteerChart"></canvas>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="lg:col-span-1">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                            <p class="text-sm text-gray-600">Common tasks and actions</p>
                        </div>
                        <div class="space-y-3">
                            <a href="{{ url('/volunteers') }}" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-colors group">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200">
                                        <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Add New Volunteer</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-blue-600"></i>
                            </a>

                            <a href="{{ url('/events') }}" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-colors group">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200">
                                        <i class="fas fa-calendar-plus text-green-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Schedule Event</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-green-600"></i>
                            </a>

                            <a href="{{ url('/tasks') }}" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 transition-colors group">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-200">
                                        <i class="fas fa-plus text-purple-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Create Task</span>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-purple-600"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ministry Distribution -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ministry Distribution</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($metrics['ministryData'] as $ministry)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="w-12 h-12 {{ $ministry['color'] }} rounded-full mx-auto mb-2 flex items-center justify-center">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <h4 class="font-medium text-gray-900">{{ $ministry['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $ministry['volunteers'] }} volunteers</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="volunteers" class="p-6 tab-content hidden">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Volunteers</h3>
                        <p class="text-sm text-gray-600">Recently active volunteers</p>
                    </div>
                    <a href="{{ url('/volunteers') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Volunteer
                    </a>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volunteer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($metrics['recentVolunteers'] as $volunteer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-sm font-medium text-blue-600">{{ $volunteer['avatar'] }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $volunteer['name'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $volunteer['role'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $volunteer['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($volunteer['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($volunteer['joined'])->format('M d, Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tasks" class="p-6 tab-content hidden">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Upcoming Tasks</h3>
                        <p class="text-sm text-gray-600">Tasks scheduled for the next 7 days</p>
                    </div>
                    <a href="{{ url('/tasks') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Task
                    </a>
                </div>

                <div class="space-y-4">
                    @foreach($metrics['upcomingTasks'] as $task)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <h4 class="text-sm font-medium text-gray-900 mr-3">{{ $task['title'] }}</h4>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $task['priority'] === 'high' ? 'bg-red-100 text-red-800' : 
                                           ($task['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($task['priority']) }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($task['date'])->format('M d, Y') }}</span>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-user mr-2"></i>
                                    <span>{{ $task['assignee'] }}</span>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $task['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($task['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ str_replace('_', ' ', ucfirst($task['status'])) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                
                // Remove active classes from all tabs
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-blue-600', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Activate clicked tab
                this.classList.remove('border-transparent', 'text-gray-500');
                this.classList.add('border-blue-600', 'text-blue-600');
                
                // Show target content
                document.getElementById(target).classList.remove('hidden');
            });
        });

        // Initialize Chart.js with real data if available
        const ctx = document.getElementById('volunteerChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Active Volunteers',
                    data: [85, 92, 78, 98], // Replace with real data if available
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection