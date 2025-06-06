{{-- resources/views/tasks.blade.php --}}

@php
    // Static array of tasks—no database involved
    $tasks = [
        [
            'title' => 'Set up sound system',
            'description' => 'Prepare and test the sound system before Sunday service',
            'status' => 'todo',
            'category' => 'Worship',
            'date' => '2025-06-06',
            'initials' => 'JD',
            'ministry' => 'Worship',
        ],
        [
            'title' => "Prepare children's lesson",
            'description' => 'Create lesson plan and activities for Sunday school',
            'status' => 'todo',
            'category' => 'Children',
            'date' => '2025-06-06',
            'initials' => 'LB',
            'ministry' => 'Children',
        ],
        [
            'title' => 'Prepare communion elements',
            'description' => 'Purchase and prepare communion bread and juice',
            'status' => 'inprogress',
            'category' => 'Hospitality',
            'date' => '2025-06-06',
            'initials' => 'SS',
            'ministry' => 'Hospitality',
        ],
        [
            'title' => 'Coordinate greeters',
            'description' => 'Assign positions and provide instructions to greeting team',
            'status' => 'inprogress',
            'category' => 'Hospitality',
            'date' => '2025-06-06',
            'initials' => 'DW',
            'ministry' => 'Hospitality',
        ],
        [
            'title' => 'Update church website',
            'description' => "Add upcoming events and update pastor's message",
            'status' => 'completed',
            'category' => 'Media',
            'date' => '2025-06-06',
            'initials' => 'MJ',
            'ministry' => 'Media',
        ],
    ];
@endphp

@extends('components.layout')

@section('title', 'Ministries')

@section('styles')
    <style>
        .modal-bg {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
@endsection

@section('content')
    @include('components.navs')

    {{-- Alpine root: manage tasks, dragging state, and “Create New Task” modal visibility --}}
    <main class="flex-1 overflow-auto p-4 sm:p-6" x-data="{
        tasks: {{ json_encode($tasks) }},
        draggedIndex: null,
        showCreateModal: false,
    
        // When a drag starts, store the index:
        onDragStart(idx) {
            this.draggedIndex = idx;
        },
    
        // On drop, move the dragged task to newStatus:
        onDrop(newStatus) {
            if (this.draggedIndex !== null) {
                this.tasks[this.draggedIndex].status = newStatus;
                this.draggedIndex = null;
            }
        },
    
        // Fields for the new task (in-memory only):
        newTitle: '',
        newDescription: '',
        newMinistry: '',
        newDueDate: '{{ now()->format('Y-m-d') }}',
        newStatus: 'todo',
        newCategory: '',
    
        // “Submit” in this demo simply closes the modal.
        createTask() {
            // In a real app you might push to this.tasks or send an AJAX request.
            // For now, we simply close the modal.
            this.showCreateModal = false;
        }
    }">
        <div class="bg-white rounded-lg shadow-sm h-full">
            {{-- ─────────────────────────────────────────────────────────────────── --}}
            {{--   Header Bar: Title / Search / Filters / Add Task Button           --}}
            {{-- ─────────────────────────────────────────────────────────────────── --}}
            <div class="flex justify-between items-center mb-6 px-4 py-2">
                <h1 class="text-2xl font-bold">Task Board</h1>
                <div class="flex space-x-4">
                    {{-- Search box (static placeholder) --}}
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-search absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                        <input type="text"
                            class="flex h-9 w-[250px] rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground pl-8 focus:outline-none focus:ring-1 focus:ring-ring"
                            placeholder="Search tasks..." />
                    </div>

                    {{-- Filter dropdown (now a working <select>) --}}
                    <div x-data="{ filterMinistry: '' }">
                        <label for="filterMinistry" class="sr-only">Filter by ministry</label>
                        <select id="filterMinistry" x-model="filterMinistry"
                            class="flex h-9 w-[180px] items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="">All Ministries</option>
                            <option value="Worship">Worship</option>
                            <option value="Hospitality">Hospitality</option>
                            <option value="Media">Media</option>
                            <option value="Children">Children</option>
                            <option value="Youth">Youth</option>
                        </select>
                    </div>

                    {{-- “Add Task” button: opens the Create New Task modal --}}
                    <button
                        class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90 focus:outline-none focus:ring-1 focus:ring-ring"
                        @click="showCreateModal = true" aria-haspopup="dialog" aria-expanded="false"
                        aria-controls="create-task-dialog">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4">
                            <path
                                d="M8 2.75C8 2.47386 7.77614 2.25 7.5 2.25C7.22386 2.25 7 2.47386 7 2.75V7H2.75C2.47386 7 2.25 7.22386 2.25 7.5C2.25 7.77614 2.47386 8 2.75 8H7V12.25C7 12.5261 7.22386 12.75 7.5 12.75C7.77614 12.75 8 12.5261 8 12.25V8H12.25C12.5261 8 12.75 7.77614 12.75 7.5C12.75 7.22386 12.5261 7 12.25 7H8V2.75Z"
                                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                        Add Task
                    </button>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────────────── --}}
            {{--   Three-column Kanban Board: “To Do” / “In Progress” / “Completed” --}}
            {{-- ─────────────────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-3 gap-4 px-4 h-[calc(100%-80px)]">
                {{-- To Do Column --}}
                <div class="rounded-lg bg-slate-100 p-4 h-full overflow-y-auto" @dragover.prevent @drop="onDrop('todo')">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-lg">To Do</h2>
                        <div
                            class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-slate-50">
                            <span x-text="tasks.filter(t => t.status === 'todo').length"></span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(task, idx) in tasks.filter(t => t.status === 'todo')" :key="idx">
                            <div class="rounded-xl border bg-card text-card-foreground shadow cursor-move" draggable="true"
                                @dragstart="onDragStart(idx)">
                                <div class="flex flex-col space-y-1.5 p-3 pb-0">
                                    <div class="flex justify-between items-start">
                                        <h3 class="font-semibold tracking-tight text-base" x-text="task.title"></h3>
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted-foreground">
                                            <path
                                                d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z"
                                                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-3 pt-2">
                                    <p class="text-sm text-muted-foreground mb-3" x-text="task.description"></p>
                                    <div class="flex justify-between items-center">
                                        <div class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-slate-50"
                                            x-text="task.category"></div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-muted-foreground" x-text="task.date"></span>
                                            <span class="relative flex h-6 w-6 shrink-0 overflow-hidden rounded-full">
                                                <span
                                                    class="flex h-full w-full items-center justify-center rounded-full bg-muted text-xs"
                                                    x-text="task.initials"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- In Progress Column --}}
                <div class="rounded-lg bg-blue-50 p-4 h-full overflow-y-auto" @dragover.prevent
                    @drop="onDrop('inprogress')">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-lg">In Progress</h2>
                        <div
                            class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-blue-100">
                            <span x-text="tasks.filter(t => t.status === 'inprogress').length"></span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(task, idx) in tasks.filter(t => t.status === 'inprogress')"
                            :key="idx">
                            <div class="rounded-xl border bg-card text-card-foreground shadow cursor-move" draggable="true"
                                @dragstart="onDragStart(idx)">
                                <div class="flex flex-col space-y-1.5 p-3 pb-0">
                                    <div class="flex justify-between items-start">
                                        <h3 class="font-semibold tracking-tight text-base" x-text="task.title"></h3>
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted-foreground">
                                            <path
                                                d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z"
                                                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-3 pt-2">
                                    <p class="text-sm text-muted-foreground mb-3" x-text="task.description"></p>
                                    <div class="flex justify-between items-center">
                                        <div class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-blue-100"
                                            x-text="task.category"></div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-muted-foreground" x-text="task.date"></span>
                                            <span class="relative flex h-6 w-6 shrink-0 overflow-hidden rounded-full">
                                                <span
                                                    class="flex h-full w-full items-center justify-center rounded-full bg-muted text-xs"
                                                    x-text="task.initials"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Completed Column --}}
                <div class="rounded-lg bg-green-50 p-4 h-full overflow-y-auto" @dragover.prevent
                    @drop="onDrop('completed')">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-lg">Completed</h2>
                        <div
                            class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-green-100">
                            <span x-text="tasks.filter(t => t.status === 'completed').length"></span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(task, idx) in tasks.filter(t => t.status === 'completed')"
                            :key="idx">
                            <div class="rounded-xl border bg-card text-card-foreground shadow cursor-move"
                                draggable="true" @dragstart="onDragStart(idx)">
                                <div class="flex flex-col space-y-1.5 p-3 pb-0">
                                    <div class="flex justify-between items-start">
                                        <h3 class="font-semibold tracking-tight text-base" x-text="task.title"></h3>
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted-foreground">
                                            <path
                                                d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z"
                                                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-3 pt-2">
                                    <p class="text-sm text-muted-foreground mb-3" x-text="task.description"></p>
                                    <div class="flex justify-between items-center">
                                        <div class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-green-100"
                                            x-text="task.category"></div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-muted-foreground" x-text="task.date"></span>
                                            <span class="relative flex h-6 w-6 shrink-0 overflow-hidden rounded-full">
                                                <span
                                                    class="flex h-full w-full items-center justify-center rounded-full bg-muted text-xs"
                                                    x-text="task.initials"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────────── --}}
        {{--   Modal: Create New Task (x-show controlled by showCreateModal)    --}}
        {{-- ─────────────────────────────────────────────────────────────────── --}}
        <div x-show="showCreateModal" x-cloak x-transition.opacity
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div x-show="showCreateModal" x-cloak x-transition @click.away="showCreateModal = false" role="dialog"
                aria-labelledby="create-task-title" aria-describedby="create-task-desc" id="create-task-dialog"
                class="relative w-full max-w-[500px] rounded-lg bg-white p-6 shadow-xl" tabindex="-1">
                {{-- Header --}}
                <div class="flex flex-col space-y-1.5 text-center sm:text-left mb-4">
                    <h2 id="create-task-title" class="text-lg font-semibold">
                        Create New Task
                    </h2>
                </div>

                {{-- Form Fields --}}
                <div class="grid gap-4 py-4">
                    {{-- Title --}}
                    <div class="grid gap-2">
                        <label for="title" class="text-sm font-medium">Title</label>
                        <input id="title" type="text" x-model="newTitle" placeholder="Task title"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring" />
                    </div>

                    {{-- Description --}}
                    <div class="grid gap-2">
                        <label for="description" class="text-sm font-medium">Description</label>
                        <textarea id="description" x-model="newDescription" placeholder="Task description"
                            class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"></textarea>
                    </div>

                    {{-- Ministry (now a real <select>) --}}
                    <div class="grid gap-2">
                        <label for="ministry" class="text-sm font-medium">Ministry</label>
                        <select id="ministry" x-model="newMinistry"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="" disabled selected>Select ministry</option>
                            <option value="Worship">Worship</option>
                            <option value="Hospitality">Hospitality</option>
                            <option value="Media">Media</option>
                            <option value="Children">Children</option>
                            <option value="Youth">Youth</option>
                        </select>
                    </div>

                    {{-- Due Date (native date picker) --}}
                    <div class="grid gap-2">
                        <label for="dueDate" class="text-sm font-medium">Due Date</label>
                        <input id="dueDate" type="date" x-model="newDueDate"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring" />
                    </div>

                    {{-- Status (dropdown) --}}
                    <div class="grid gap-2">
                        <label for="status" class="text-sm font-medium">Status</label>
                        <select id="status" x-model="newStatus"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                            <option value="todo">To Do</option>
                            <option value="inprogress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>

                {{-- Buttons: Cancel / Create Task --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                    <button
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:opacity-50 mt-2 sm:mt-0"
                        @click="showCreateModal = false" type="button">
                        Cancel
                    </button>
                    <button
                        class="inline-flex items-center justify-center rounded-md bg-primary text-primary-foreground px-4 py-2 text-sm font-medium shadow hover:bg-primary/90 focus:outline-none focus:ring-1 focus:ring-ring disabled:opacity-50 mb-2 sm:mb-0"
                        @click="createTask()" type="button">
                        Create Task
                    </button>
                </div>

                {{-- “X” (Close) Button --}}
                <button type="button"
                    class="absolute right-4 top-4 rounded-sm opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    @click="showCreateModal = false">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                        <path
                            d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z"
                            fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
            </div>
        </div>
    </main>

    {{-- Alpine.js CDN --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
