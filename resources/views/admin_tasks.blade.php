@extends('components.layout')
@section('title', 'Task and Assignment Monitoring')
@section('styles')
    <style>
        #taskModal {
            backdrop-filter: blur(2px);
        }

        #taskModal .bg-white {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Form input focus states */
        input:focus,
        textarea:focus,
        select:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Smooth transitions */
        #taskModal>div {
            animation: modalSlideIn 0.3s ease-out;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .tooltip {
            position: relative;
        }

        .tooltip:hover::after {
            content: attr(title);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1f2937;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 0.25rem;
        }

        .tooltip:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 4px solid transparent;
            border-top-color: #1f2937;
            z-index: 10;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>
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
                            <h1 class="text-3xl font-bold text-gray-900">Task Management</h1>
                            <p class="mt-2 text-gray-600">Manage and track tasks across your organization</p>
                        </div>
                        <button onclick="openAddModal()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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
                                <p class="text-3xl font-bold text-gray-900">{{ $totalTasks }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">To Do</p>
                                <p class="text-3xl font-bold text-orange-600">{{ $todoTasks }}</p>
                            </div>
                            <div class="p-3 bg-orange-100 rounded-full">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">In Progress</p>
                                <p class="text-3xl font-bold text-yellow-600">{{ $inProgressTasks }}</p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Completed</p>
                                <p class="text-3xl font-bold text-green-600">{{ $completedTasks }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->

                <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-200">
                    <form id="searchForm" method="GET" action="{{ route('tasks.index') }}">
                        <div class="flex flex-col lg:flex-row gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                        placeholder="Search tasks by title or description..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <select name="status" id="statusFilter"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="To Do" {{ request('status') == 'To Do' ? 'selected' : '' }}>To Do</option>
                                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In
                                        Progress</option>
                                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>
                                        Completed</option>
                                </select>
                                @if(request('search') || request('status'))
                                    <a href="{{ route('tasks.index') }}"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                        Clear Filters
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Tasks Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($tasks as $task)
                        <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 task-card relative overflow-hidden"
                            data-task-id="{{ $task->id }}">

                            <!-- Status color accent -->
                            @php
                                $statusAccents = [
                                    'To Do' => 'bg-gradient-to-r from-orange-400 to-orange-500',
                                    'In Progress' => 'bg-gradient-to-r from-blue-400 to-blue-500',
                                    'Completed' => 'bg-gradient-to-r from-green-400 to-green-500'
                                ];
                                $statusAccent = $statusAccents[$task->status] ?? 'bg-gradient-to-r from-gray-400 to-gray-500';
                            @endphp
                            <div class="absolute top-0 left-0 right-0 h-1 {{ $statusAccent }}"></div>

                            <!-- Header Section -->
                            <div class="mb-4">
                                <h3
                                    class="text-xl font-bold text-gray-900 mb-3 task-title group-hover:text-blue-600 transition-colors duration-200">
                                    {{ $task->title }}
                                </h3>
                                <p class="text-gray-600 text-sm leading-relaxed line-clamp-2">{{ $task->description }}</p>
                            </div>

                            <!-- Ministry Badge -->
                            @if($task->ministry)
                                <div class="mb-4">
                                    <div
                                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100">
                                        <svg class="w-4 h-4 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2L4.5 9v11h3v-8h5v8h3V9L12 2zm0 1.5L17.5 9v10.5H14V11H10v8.5H6.5V9L12 3.5z" />
                                        </svg>
                                        <span
                                            class="text-xs font-medium text-indigo-700">{{ $task->ministry->ministry_name }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Status and Due Date Section -->
                            <div class="flex items-center justify-between mb-6">
                                @php
                                    $statusStyles = [
                                        'To Do' => [
                                            'bg' => 'bg-gradient-to-r from-orange-50 to-orange-100',
                                            'text' => 'text-orange-700',
                                            'border' => 'border-orange-200',
                                            'icon' => '<circle cx="12" cy="12" r="3"/>'
                                        ],
                                        'In Progress' => [
                                            'bg' => 'bg-gradient-to-r from-blue-50 to-blue-100',
                                            'text' => 'text-blue-700',
                                            'border' => 'border-blue-200',
                                            'icon' => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>'
                                        ],
                                        'Completed' => [
                                            'bg' => 'bg-gradient-to-r from-green-50 to-green-100',
                                            'text' => 'text-green-700',
                                            'border' => 'border-green-200',
                                            'icon' => '<polyline points="20,6 9,17 4,12"/>'
                                        ]
                                    ];
                                    $statusStyle = $statusStyles[$task->status] ?? [
                                        'bg' => 'bg-gradient-to-r from-gray-50 to-gray-100',
                                        'text' => 'text-gray-700',
                                        'border' => 'border-gray-200',
                                        'icon' => '<circle cx="12" cy="12" r="3"/>'
                                    ];
                                @endphp

                                <div class="flex items-center space-x-2">
                                    <div
                                        class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold border {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} {{ $statusStyle['border'] }}">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $statusStyle['icon'] !!}
                                        </svg>
                                        {{ $task->status }}
                                    </div>
                                </div>

                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    @if($task->due_date)
                                        @php
                                            $dueDate = \Carbon\Carbon::parse($task->due_date);
                                            $isOverdue = $dueDate->isPast() && $task->status !== 'Completed';
                                            $isDueSoon = $dueDate->isToday() || $dueDate->isTomorrow();
                                        @endphp
                                        <span
                                            class="@if($isOverdue) text-red-600 font-medium @elseif($isDueSoon) text-amber-600 font-medium @endif">
                                            {{ $dueDate->format('M d, Y') }}
                                            @if($isOverdue)
                                                <span class="text-xs">(Overdue)</span>
                                            @elseif($isDueSoon)
                                                <span class="text-xs">(Due Soon)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400">No due date</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer Section -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center text-xs text-gray-400">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Created {{ $task->created_at->format('M d, Y') }}
                                    </div>
                                </div>

                                <div
                                    class="flex items-center space-x-1 task-actions opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button onclick="editTask({{ $task->id }})"
                                        class="p-2 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all duration-200 tooltip"
                                        title="Edit Task">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <button onclick="deleteTask({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200 tooltip"
                                        title="Delete Task">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Progress indicator for overdue/due soon tasks -->
                            @if(isset($isOverdue) && $isOverdue)
                                <div class="absolute top-4 right-4">
                                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                </div>
                            @elseif(isset($isDueSoon) && $isDueSoon)
                                <div class="absolute top-4 right-4">
                                    <div class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($tasks->hasPages())
                    <div class="mt-8">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- add/edit modal -->
        <div id="taskModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Task</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="taskForm" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" id="taskId" name="id">
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="space-y-4">
                        <!-- Task Title -->
                        <div>
                            <label for="taskTitle" class="block text-sm font-medium text-gray-700 mb-2">Task Title *</label>
                            <input type="text" id="taskTitle" name="title" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Enter task title">
                        </div>

                        <!-- Task Description -->
                        <div>
                            <label for="taskDescription"
                                class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="taskDescription" name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                                placeholder="Enter task description (optional)"></textarea>
                        </div>
                        <!-- assign to ministry -->
                        <div>
                            <label for="taskMinistry" class="block text-sm font-medium text-gray-700 mb-2">Assign to
                                Ministry</label>
                            <select id="taskMinistry" name="ministry_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
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
                        </div>

                        <!-- Due Date and Status in a row -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Due Date -->
                            <div>
                                <label for="taskDueDate" class="block text-sm font-medium text-gray-700 mb-2">Due
                                    Date</label>
                                <input type="date" id="taskDueDate" name="due_date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="taskStatus" class="block text-sm font-medium text-gray-700 mb-2">Status
                                    *</label>
                                <select id="taskStatus" name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="To Do">To Do</option>
                                    <!-- <option value="In Progress">In Progress</option> -->
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Save Task
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Deletion</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete this task? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button onclick="closeDeleteModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                                Delete Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Task Modal -->
    <div id="archiveTaskModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex items-center mb-4 p-6 border-b">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Archive Task</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to archive "<span id="archiveTaskTitle"
                        class="font-medium"></span>"?</p>
                <div class="mb-4">
                    <label for="archiveReason" class="block text-sm font-medium text-gray-700 mb-1">Reason for
                        archiving</label>
                    <textarea id="archiveReason" rows="3"
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors"
                        placeholder="Enter reason..."></textarea>
                </div>
                <div class="flex space-x-3 justify-end">
                    <button onclick="closeArchiveModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button onclick="confirmArchiveTask()"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Archive Task
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <!-- <div id="toast" class="fixed top-4 right-4 z-50 hidden">
                                                        <div class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center">
                                                            <span id="toast-message"></span>
                                                            <button onclick="hideToast()" class="ml-4">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div> -->
@endsection

<!-- In admin_tasks.blade.php -->
@section('scripts')
    <script>
        // DOM Elements
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const taskForm = document.getElementById('taskForm');
        let taskToArchive = null;

        // Modal Functions
        function openAddModal() {
            document.getElementById('taskModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Add New Task';
            document.getElementById('taskForm').reset();
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('taskForm').action = "{{ route('tasks.store') }}";
        }

        function closeModal() {
            document.getElementById('taskModal').classList.add('hidden');
        }

        function closeArchiveModal() {
            document.getElementById('archiveTaskModal').classList.add('hidden');
            document.getElementById('archiveReason').value = '';
            taskToArchive = null;
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Task Functions
        function editTask(taskId) {
            fetch(`/tasks/${taskId}/edit`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(task => {
                    document.getElementById('taskModal').classList.remove('hidden');
                    document.getElementById('modalTitle').textContent = 'Edit Task';
                    document.getElementById('taskId').value = task.id;
                    document.getElementById('taskTitle').value = task.title;
                    document.getElementById('taskDescription').value = task.description;
                    document.getElementById('taskMinistry').value = task.ministry_id || '';

                    // Format date correctly for input[type=date]
                    const dueDate = task.due_date ? new Date(task.due_date) : null;
                    document.getElementById('taskDueDate').value = dueDate ?
                        dueDate.toISOString().split('T')[0] : '';

                    document.getElementById('taskStatus').value = task.status;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('taskForm').action = `/tasks/${task.id}`;
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Failed to load task data');
                });
        }

        function deleteTask(taskId, taskTitle) {
            taskToArchive = taskId;
            document.getElementById('archiveTaskTitle').textContent = taskTitle;
            document.getElementById('archiveTaskModal').classList.remove('hidden');
        }

        function confirmArchiveTask() {
            const reason = document.getElementById('archiveReason').value;

            fetch(`/tasks/${taskToArchive}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message || 'Task archived successfully');
                        document.querySelector(`[data-task-id="${taskToArchive}"]`).remove();
                    } else {
                        toastr.error(data.message || 'Failed to archive task');
                    }
                    closeArchiveModal();
                })
                .catch(error => {
                    toastr.error(error.message || 'An error occurred. Please try again.');
                    closeArchiveModal();
                });
        }

        // Handle Task Form Submission
        taskForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const method = this.querySelector('input[name="_method"]')?.value || 'POST';
            const url = this.action;

            // Convert FormData to JSON
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(jsonData)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        closeModal();
                        window.location.reload();
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        // Handle validation errors
                        Object.entries(error.errors).forEach(([field, messages]) => {
                            toastr.error(messages[0]);
                        });
                    } else {
                        toastr.error(error.message || 'An error occurred. Please try again.');
                    }
                });
        });

        // Search and Filter Functions
        function debounce(func, wait) {
            let timeout;
            return function () {
                const context = this,
                    args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function () {
            // Search input with debounce
            searchInput.addEventListener('input', debounce(function () {
                searchForm.submit();
            }, 500));

            // Status filter change
            statusFilter.addEventListener('change', function () {
                searchForm.submit();
            });

            // Modal close on outside click
            document.getElementById('taskModal').addEventListener('click', function (e) {
                if (e.target === this) closeModal();
            });

            document.getElementById('deleteModal').addEventListener('click', function (e) {
                if (e.target === this) closeDeleteModal();
            });

            // Escape key to close modals
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    if (!document.getElementById('taskModal').classList.contains('hidden')) {
                        closeModal();
                    }
                    if (!document.getElementById('deleteModal').classList.contains('hidden')) {
                        closeDeleteModal();
                    }
                    if (!document.getElementById('archiveTaskModal').classList.contains('hidden')) {
                        closeArchiveModal();
                    }
                }
            });
        });
    </script>
@endsection