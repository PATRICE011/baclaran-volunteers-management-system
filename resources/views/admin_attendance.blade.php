{{-- resources/views/attendance.blade.php --}}

@php
    // Static array of events—no database needed
    $events = [
        [
            'id' => 1,
            'title' => 'Sunday Service',
            'datetime' => '2023-07-16 09:00 AM',
            'volunteers' => 5,
        ],
        [
            'id' => 2,
            'title' => 'Youth Group Meeting',
            'datetime' => '2023-07-18 06:30 PM',
            'volunteers' => 5,
        ],
        [
            'id' => 3,
            'title' => 'Prayer Meeting',
            'datetime' => '2023-07-19 07:00 PM',
            'volunteers' => 5,
        ],
        [
            'id' => 4,
            'title' => 'Sunday Service',
            'datetime' => '2023-07-23 09:00 AM',
            'volunteers' => 5,
        ],
    ];

    // Static attendance details per event (no database)
    $attendanceDetails = [
        // Only event ID = 1 has detailed data in this example
        1 => [
            'present' => 3,
            'absent' => 2,
            'pending' => 0,
            'rows' => [
                ['name' => 'John Doe', 'role' => 'Usher', 'status' => 'Present'],
                ['name' => 'Jane Smith', 'role' => 'Worship Team', 'status' => 'Present'],
                ['name' => 'Mike Johnson', 'role' => 'Tech Team', 'status' => 'Absent'],
                ['name' => 'Sarah Williams', 'role' => 'Youth Leader', 'status' => 'Present'],
                ['name' => 'Tom Brown', 'role' => 'Assistant', 'status' => 'Absent'],
            ],
        ],
        // You may add additional keyed data for other event IDs here
    ];
@endphp

@extends('components.layout')

@section('title', 'Schedule')

@section('styles')
    <style>
        .modal-bg {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
@endsection

@section('content')
    @include('components.navs')

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Attendance Tracking</title>

        {{-- Tailwind CSS via CDN --}}
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.2/dist/tailwind.min.css" rel="stylesheet">

        {{-- Alpine.js for reactive logic --}}
        <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>

        {{-- Lucide Icons --}}
        <script src="https://unpkg.com/lucide/dist/lucide.min.js"></script>

        <style>
            /* Prevent flicker before Alpine mounts */
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>

    <body class="bg-gray-100 min-h-screen">

        {{-- Alpine root --}}
        <main class="flex-1 overflow-auto p-4 sm:p-6" x-data="{
            // Modal for viewing a single event's attendance
            showModal: false,
            selectedEventId: null,
        
            // Modal for marking attendance form
            showMarkModal: false,
        
            // Alpine’s copy of the PHP `$events` array
            events: {{ json_encode($events) }},
        
            // Alpine’s copy of the PHP `$attendanceDetails` map
            attendanceDetails: {{ json_encode($attendanceDetails) }},
        
            // Compute the currently selected event’s data
            get selectedEvent() {
                return this.events.find(e => e.id === this.selectedEventId) || null;
            },
        
            // Compute the attendance details object for the currently selected ID
            get selectedAttendance() {
                return this.attendanceDetails[this.selectedEventId] || null;
            },
        
            // Volunteers list for the “Mark Attendance” popup
            markVolunteers: [
                { id: 1, name: 'John Doe', role: 'Usher', checked: false, status: 'Present' },
                { id: 2, name: 'Jane Smith', role: 'Worship Team', checked: false, status: 'Present' },
                { id: 3, name: 'Mike Johnson', role: 'Tech Team', checked: false, status: 'Present' },
                { id: 4, name: 'Sarah Williams', role: 'Youth Leader', checked: false, status: 'Present' },
                { id: 5, name: 'Tom Brown', role: 'Assistant', checked: false, status: 'Present' },
            ],
        }">
            {{-- Container --}}
            <div class="bg-white rounded-xl shadow w-full h-full flex flex-col">

                {{-- Header --}}
                <div class="flex justify-between items-center m-6">
                    <h1 class="text-2xl font-bold">Attendance Tracking</h1>

                    <div class="flex items-center space-x-4">
                        {{-- Date Picker (static) --}}
                        <input type="date"
                            class="flex h-9 rounded-md border border-gray-300 bg-white px-3 py-1 text-sm shadow-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 w-40"
                            value="2025-06-06" />

                        {{-- Mark Attendance button triggers showMarkModal --}}
                        <button type="button"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 text-white text-sm font-medium shadow hover:bg-indigo-700 h-9 px-4 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-50"
                            @click="showMarkModal = true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-plus h-4 w-4 mr-2">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            Mark Attendance
                        </button>
                    </div>
                </div>

                {{-- Search Bar --}}
                <div class="m-6 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <input type="text"
                        class="flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm shadow-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 pl-10"
                        placeholder="Search events..." />
                </div>

                {{-- Main Content: Two Columns --}}
                <div class="flex flex-1 gap-6">
                    {{-- Left Column: Event List --}}
                    <div class="w-1/3 m-6">
                        <div class="rounded-xl border bg-white shadow">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <h3 class="font-semibold leading-none tracking-tight">Events</h3>
                                <p class="text-sm text-gray-500 mt-1">Select an event to view attendance</p>
                            </div>

                            {{-- Scrollable list of events --}}
                            <div class="p-6 pt-0">
                                <div class="h-[500px] overflow-y-auto">
                                    <template x-for="eventItem in events" :key="eventItem.id">
                                        <div class="p-3 rounded-md cursor-pointer hover:bg-gray-50 flex justify-between items-center"
                                            @click="selectedEventId = eventItem.id; showModal = true">
                                            <div>
                                                <div class="font-medium" x-text="eventItem.title"></div>
                                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-calendar h-3 w-3 mr-1">
                                                        <path d="M8 2v4"></path>
                                                        <path d="M16 2v4"></path>
                                                        <rect width="18" height="18" x="3" y="4" rx="2">
                                                        </rect>
                                                        <path d="M3 10h18"></path>
                                                    </svg>
                                                    <span x-text="eventItem.datetime"></span>
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center">
                                                <span x-text="eventItem.volunteers + ' volunteers'"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-chevron-right h-4 w-4 ml-2">
                                                    <path d="M9 18l6-6-6-6"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Placeholder --}}
                    <div class="flex-1">
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <h3 class="text-lg font-medium">No Event Selected</h3>
                                <p class="text-gray-500">Select an event from the list to view attendance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- 1) Modal: View Event Attendance  --}}
            {{-- ============================= --}}
            <div x-show="showModal" x-cloak x-transition.opacity
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40">
                <div x-show="showModal" x-cloak x-transition @click.away="showModal = false"
                    class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4 overflow-hidden">
                    {{-- Modal Header --}}
                    <div class="flex justify-between items-center p-6 border-b">
                        <div>
                            <h3 class="font-semibold text-lg leading-none tracking-tight">
                                <template x-if="selectedEvent">
                                    <span x-text="selectedEvent.title + ' Attendance'"></span>
                                </template>
                                <template x-if="!selectedEvent">
                                    Event Attendance
                                </template>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1" x-text="selectedEvent ? selectedEvent.datetime : ''">
                            </p>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600" @click="showModal = false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-x h-5 w-5">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-6 overflow-auto max-h-[80vh]">
                        <template x-if="selectedAttendance">
                            <div>
                                {{-- Summary Cards --}}
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    {{-- Present --}}
                                    <div class="rounded-xl border bg-white text-gray-900 shadow">
                                        <div class="p-6 pt-6">
                                            <div class="text-2xl font-bold" x-text="selectedAttendance.present"></div>
                                            <p class="text-xs text-gray-500">Present</p>
                                        </div>
                                    </div>

                                    {{-- Absent --}}
                                    <div class="rounded-xl border bg-white text-gray-900 shadow">
                                        <div class="p-6 pt-6">
                                            <div class="text-2xl font-bold" x-text="selectedAttendance.absent"></div>
                                            <p class="text-xs text-gray-500">Absent</p>
                                        </div>
                                    </div>

                                    {{-- Pending --}}
                                    <div class="rounded-xl border bg-white text-gray-900 shadow">
                                        <div class="p-6 pt-6">
                                            <div class="text-2xl font-bold" x-text="selectedAttendance.pending"></div>
                                            <p class="text-xs text-gray-500">Pending</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Attendance Table --}}
                                <div class="relative w-full overflow-auto">
                                    <table class="w-full text-sm">
                                        <thead class="[&_tr]:border-b">
                                            <tr class="border-b hover:bg-gray-50">
                                                <th class="h-10 px-2 text-left font-medium text-gray-500">Volunteer</th>
                                                <th class="h-10 px-2 text-left font-medium text-gray-500">Role</th>
                                                <th class="h-10 px-2 text-left font-medium text-gray-500">Status</th>
                                                <th class="h-10 px-2 text-right font-medium text-gray-500">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="[&_tr:last-child]:border-0">
                                            <template x-for="row in selectedAttendance.rows" :key="row.name">
                                                <tr class="border-b hover:bg-gray-50">
                                                    {{-- Volunteer Name & Avatar --}}
                                                    <td class="p-2 align-middle">
                                                        <div class="flex items-center">
                                                            <span
                                                                class="relative flex shrink-0 rounded-full h-8 w-8 overflow-hidden mr-2">
                                                                <img class="h-full w-full object-cover"
                                                                    :src="`https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(row.name)}`"
                                                                    :alt="row.name" />
                                                            </span>
                                                            <span x-text="row.name"></span>
                                                        </div>
                                                    </td>

                                                    {{-- Role --}}
                                                    <td class="p-2 align-middle">
                                                        <div
                                                            class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold text-gray-700">
                                                            <span x-text="row.role"></span>
                                                        </div>
                                                    </td>

                                                    {{-- Status Badge --}}
                                                    <td class="p-2 align-middle">
                                                        <template x-if="row.status === 'Present'">
                                                            <div
                                                                class="inline-flex items-center rounded-md bg-green-500 text-white px-2.5 py-0.5 text-xs font-semibold">
                                                                Present
                                                            </div>
                                                        </template>
                                                        <template x-if="row.status === 'Absent'">
                                                            <div
                                                                class="inline-flex items-center rounded-md bg-red-500 text-white px-2.5 py-0.5 text-xs font-semibold">
                                                                Absent
                                                            </div>
                                                        </template>
                                                        <template
                                                            x-if="row.status !== 'Present' && row.status !== 'Absent'">
                                                            <div
                                                                class="inline-flex items-center rounded-md bg-yellow-500 text-gray-900 px-2.5 py-0.5 text-xs font-semibold">
                                                                <span x-text="row.status"></span>
                                                            </div>
                                                        </template>
                                                    </td>

                                                    {{-- Actions --}}
                                                    <td class="p-2 align-middle text-right">
                                                        <div class="flex justify-end gap-2">
                                                            <button
                                                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white h-8 w-8 p-0 text-gray-500 hover:bg-gray-50 hover:text-gray-700"
                                                                title="Mark Present">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="lucide lucide-check h-4 w-4">
                                                                    <path d="M20 6 9 17l-5-5"></path>
                                                                </svg>
                                                            </button>
                                                            <button
                                                                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white h-8 w-8 p-0 text-gray-500 hover:bg-gray-50 hover:text-gray-700"
                                                                title="Mark Absent">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="lucide lucide-x h-4 w-4">
                                                                    <path d="M18 6 6 18"></path>
                                                                    <path d="m6 6 12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <template x-if="!selectedAttendance">
                            <div class="text-center text-gray-500">
                                No attendance data available for this event.
                            </div>
                        </template>
                    </div>
                </div>
            </div>


            {{-- ============================= --}}
            {{-- 2) Modal: “Mark Attendance” Popup Form  --}}
            {{-- ============================= --}}
            <div x-show="showMarkModal" x-cloak x-transition.opacity
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div x-show="showMarkModal" x-cloak x-transition @click.away="showMarkModal = false" role="dialog"
                    id="radix-:ra5:" aria-describedby="radix-:ra7:" aria-labelledby="radix-:ra6:" data-state="open"
                    class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border bg-white p-6 shadow-lg duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] sm:rounded-lg sm:max-w-[600px]"
                    tabindex="-1" style="pointer-events: auto;">
                    {{-- Modal Title & Description --}}
                    <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                        <h2 id="radix-:ra6:" class="text-lg font-semibold leading-none tracking-tight">
                            Mark Attendance
                        </h2>
                        <p id="radix-:ra7:" class="text-sm text-gray-500 mt-0.5">
                            Select an event and mark attendance for volunteers.
                        </p>
                    </div>

                    {{-- Modal Form Content --}}
                    <div class="grid gap-4 py-4">
                        {{-- Event Selector --}}
                        <div class="grid grid-cols-4 items-center gap-4">
                            <label for="event" class="text-sm font-medium text-right">Event</label>
                            <button type="button" role="combobox" aria-controls="radix-:ra9:" aria-expanded="false"
                                aria-autocomplete="none"
                                class="flex h-9 w-full items-center justify-between rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm shadow-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 col-span-3">
                                <span style="pointer-events: none;">
                                    Sunday Service – 2023-07-16
                                </span>
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" aria-hidden="true">
                                    <path
                                        d="M4.93179 5.43179C4.75605 5.60753 4.75605 5.89245 4.93179 6.06819C5.10753 6.24392 5.39245 6.24392 5.56819 6.06819L7.49999 4.13638L9.43179 6.06819C9.60753 6.24392 9.89245 6.24392 10.0682 6.06819C10.2439 5.89245 10.2439 5.60753 10.0682 5.43179L7.81819 3.18179C7.73379 3.0974 7.61933 3.04999 7.49999 3.04999C7.38064 3.04999 7.26618 3.0974 7.18179 3.18179L4.93179 5.43179ZM10.0682 9.56819C10.2439 9.39245 10.2439 9.10753 10.0682 8.93179C9.89245 8.75606 9.60753 8.75606 9.43179 8.93179L7.49999 10.8636L5.56819 8.93179C5.39245 8.75606 5.10753 8.75606 4.93179 8.93179C4.75605 9.10753 4.75605 9.39245 4.93179 9.56819L7.18179 11.8182C7.35753 11.9939 7.64245 11.9939 7.81819 11.8182L10.0682 9.56819Z"
                                        fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        {{-- Date Selector --}}
                        <div class="grid grid-cols-4 items-center gap-4">
                            <label for="date" class="text-sm font-medium text-right">Date</label>
                            <input type="date" id="date"
                                class="flex h-9 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-sm shadow-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 col-span-3"
                                value="2025-06-06" />
                        </div>

                        {{-- Volunteers Checklist / Status Dropdowns --}}
                        <div class="col-span-4">
                            <h4 class="font-medium mb-2">Volunteers</h4>
                            <div class="space-y-2 max-h-[300px] overflow-y-auto">
                                <template x-for="vol in markVolunteers" :key="vol.id">
                                    <div class="flex items-center space-x-2">
                                        {{-- Checkbox --}}
                                        <button type="button" role="checkbox" :aria-checked="vol.checked.toString()"
                                            @click="vol.checked = !vol.checked"
                                            class="peer h-4 w-4 shrink-0 rounded-sm border border-indigo-500 shadow focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                            :class="{ 'bg-indigo-500 text-white': vol.checked }"></button>

                                        {{-- Label --}}
                                        <label :for="`volunteer-${vol.id}`"
                                            class="text-sm font-medium flex-1 peer-disabled:cursor-not-allowed peer-disabled:opacity-50"
                                            x-text="`${vol.name} (${vol.role})`"></label>

                                        {{-- Status Dropdown --}}
                                        <select
                                            class="h-9 rounded-md border border-gray-300 bg-white px-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-[120px]"
                                            x-model="vol.status">
                                            <option value="Present">Present</option>
                                            <option value="Absent">Absent</option>
                                            <option value="Excused">Excused</option>
                                        </select>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer: Cancel / Save --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                        <button type="button" @click="showMarkModal = false"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-gray-300 bg-white px-4 py-2 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-50">
                            Cancel
                        </button>

                        <button type="button"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 text-white text-sm font-medium shadow hover:bg-indigo-700 px-4 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-50">
                            Save Attendance
                        </button>
                    </div>

                    {{-- Close (X) Button at Top Right --}}
                    <button type="button" @click="showMarkModal = false"
                        class="absolute right-4 top-4 rounded-sm opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                            <path
                                d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z"
                                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Close</span>
                    </button>
                </div>
            </div>

        </main>

        {{-- Initialize Lucide icons --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.lucide) {
                    window.lucide.replace();
                }
            });
        </script>
    </body>

    </html>
