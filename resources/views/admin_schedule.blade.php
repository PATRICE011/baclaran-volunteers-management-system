@php
// Mock event data (using ISO date strings)
$events = [
[
'id' => 1,
'title' => 'Sunday Service',
'date' => '2023-07-16T00:00:00.000Z',
'startTime' => '9:00 AM',
'endTime' => '11:00 AM',
'location' => 'Main Sanctuary',
'description' => 'Regular Sunday worship service',
'volunteers' => [
['id' => 1, 'name' => 'John Doe', 'role' => 'Usher'],
['id' => 2, 'name' => 'Jane Smith', 'role' => 'Worship Team'],
['id' => 3, 'name' => 'Mike Johnson', 'role' => 'Tech Team'],
],
'color' => 'bg-blue-500'
],
[
'id' => 2,
'title' => 'Youth Group Meeting',
'date' => '2023-07-18T00:00:00.000Z',
'startTime' => '6:30 PM',
'endTime' => '8:30 PM',
'location' => 'Youth Room',
'description' => 'Weekly youth group gathering',
'volunteers' => [
['id' => 4, 'name' => 'Sarah Williams', 'role' => 'Youth Leader'],
['id' => 5, 'name' => 'Tom Brown', 'role' => 'Assistant'],
],
'color' => 'bg-green-500'
],
[
'id' => 3,
'title' => 'Prayer Meeting',
'date' => '2023-07-19T00:00:00.000Z',
'startTime' => '7:00 PM',
'endTime' => '8:00 PM',
'location' => 'Chapel',
'description' => 'Midweek prayer gathering',
'volunteers' => [
['id' => 6, 'name' => 'Robert Davis', 'role' => 'Prayer Leader']
],
'color' => 'bg-purple-500'
],
];

// Mock available volunteers data
$availableVolunteers = [
['id' => 1, 'name' => 'John Doe', 'skills' => ['Usher', 'Greeter'], 'availability' => 'Weekends'],
['id' => 2, 'name' => 'Jane Smith', 'skills' => ['Worship', 'Music'], 'availability' => 'Evenings'],
['id' => 3, 'name' => 'Mike Johnson', 'skills' => ['Tech', 'Sound'], 'availability' => 'Weekends'],
['id' => 4, 'name' => 'Sarah Williams', 'skills' => ['Youth', 'Teaching'], 'availability' => 'Weekends, Evenings'],
['id' => 5, 'name' => 'Tom Brown', 'skills' => ['Children', 'Games'], 'availability' => 'Weekends'],
['id' => 6, 'name' => 'Robert Davis', 'skills' => ['Prayer', 'Counseling'], 'availability' => 'Evenings'],
['id' => 7, 'name' => 'Lisa Garcia', 'skills' => ['Hospitality', 'Cooking'], 'availability' => 'Weekends, Evenings'],
['id' => 8, 'name' => 'David Wilson', 'skills' => ['Administration', 'Organization'], 'availability' => 'Weekdays'],
];
@endphp

@extends('components.layout')
@section('title','Schedule')
@section('styles')
<style>
    /* Modal background overlay */
    .modal-bg {
        background: rgba(0, 0, 0, 0.5);
    }
</style>

@endsection
@section('content')

<!-- Page Wrapper: Sidebar + Main Content -->
@include('components.navs')
<!-- Main Content Area -->
<div class="flex-1 flex flex-col overflow-auto p-6">
    <!-- Header: Title & Controls -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Schedule Calendar</h1>
        <div class="flex items-center space-x-4">
            <!-- View Tabs -->
            <div id="viewTabs" class="flex space-x-2 bg-gray-100 rounded-md p-1">
                <button class="bg-white rounded-md text-gray-700 font-medium px-4 py-2 shadow focus:outline-none" data-view="month">
                    Month
                </button>

                <!-- Inactive tab buttons -->
                <button class="rounded-md text-gray-600 px-4 py-2 hover:bg-white hover:text-gray-800 focus:outline-none" data-view="week">
                    Week
                </button>

                <button class="rounded-md text-gray-600 px-4 py-2 hover:bg-white hover:text-gray-800 focus:outline-none" data-day="day">
                    Day
                </button>
            </div>
            <!-- New Event Button -->
            <button id="newEventBtn" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Event
            </button>
        </div>
    </div>

    <!-- Search Input -->
    <div class="mb-4 relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1011 17a6 6 0 006-6z"></path>
            </svg>
        </div>
        <input id="searchInput" type="text" placeholder="Search events by title, description, or location..."
            class="pl-10 pr-3 py-2 border rounded w-full focus:outline-none focus:border-blue-500">
    </div>

    <!-- Calendar View Container -->
    <div id="calendarView" class="flex-1"></div>

    <!-- Right Sidebar: Upcoming Events and Available Volunteers -->
    <div class="flex gap-6 mt-6">
        <div class="w-80">
            <!-- Upcoming Events Card -->
            <div class="bg-white shadow rounded mb-6">
                <div class="px-4 py-3 border-b">
                    <h2 class="font-semibold text-lg">Upcoming Events</h2>
                    <p class="text-sm text-gray-500">Next 7 days</p>
                </div>
                <div class="p-4 max-h-72 overflow-auto space-y-4">
                    @foreach($events as $event)
                    <div class="flex items-start">
                        <div class="w-2 h-2 rounded-full mt-1.5 {{ $event['color'] }}"></div>
                        <div class="ml-2 flex-1">
                            <h4 class="font-medium text-sm">{{ $event['title'] }}</h4>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                </svg>
                                <span>{{ date('M d, Y', strtotime($event['date'])) }}</span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3" />
                                </svg>
                                <span>{{ $event['startTime'] }} - {{ $event['endTime'] }}</span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9l-5 5-5-5" />
                                </svg>
                                <span>{{ $event['location'] }}</span>
                            </div>
                        </div>
                        <button class="ml-2" onclick="openViewEvent({{ $event['id'] }})">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- Available Volunteers Card -->
            <div class="bg-white shadow rounded">
                <div class="px-4 py-3 border-b">
                    <h2 class="font-semibold text-lg">Available Volunteers</h2>
                    <p class="text-sm text-gray-500">People ready to serve</p>
                </div>
                <div class="p-4 max-h-72 overflow-auto space-y-4">
                    @foreach($availableVolunteers as $vol)
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full overflow-hidden">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($vol['name']) }}" alt="{{ $vol['name'] }}">
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">{{ $vol['name'] }}</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($vol['skills'] as $skill)
                                <span class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div id="createEventModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative">
            <button id="closeCreateEvent" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <h2 class="text-xl font-bold mb-4">Create New Event</h2>
            <form id="createEventForm" class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-4">
                    <label for="event-title" class="text-right">Title</label>
                    <input id="event-title" type="text" placeholder="Event title" class="col-span-3 border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <label for="event-date" class="text-right">Date</label>
                    <input id="event-date" type="date" class="col-span-3 border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <label for="event-start" class="text-right">Start Time</label>
                    <input id="event-start" type="time" class="col-span-3 border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <label for="event-end" class="text-right">End Time</label>
                    <input id="event-end" type="time" class="col-span-3 border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <label for="event-location" class="text-right">Location</label>
                    <input id="event-location" type="text" placeholder="Event location" class="col-span-3 border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <label for="event-description" class="text-right">Description</label>
                    <textarea id="event-description" placeholder="Event description" class="col-span-3 border rounded px-3 py-2"></textarea>
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <label for="event-volunteers" class="text-right">Volunteers</label>
                    <div class="col-span-3">
                        <select id="event-volunteers" class="w-full border rounded px-3 py-2">
                            <option value="">Select volunteer</option>
                            @foreach($availableVolunteers as $vol)
                            <option value="{{ $vol['id'] }}">{{ $vol['name'] }} ({{ implode(', ', $vol['skills']) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-4 items-center gap-4">
                    <label for="event-color" class="text-right">Color</label>
                    <div class="col-span-3">
                        <select id="event-color" class="w-full border rounded px-3 py-2">
                            <option value="bg-blue-500">Blue</option>
                            <option value="bg-green-500">Green</option>
                            <option value="bg-red-500">Red</option>
                            <option value="bg-purple-500">Purple</option>
                            <option value="bg-yellow-500">Yellow</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancelCreateEvent" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Event Modal -->
    <div id="viewEventModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative">
            <button id="closeViewEvent" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div id="viewEventContent">
                <!-- Event details will be populated here -->
            </div>
            <div class="flex justify-between mt-4">
                <button id="deleteEventBtn" class="px-4 py-2 bg-red-600 text-white rounded">Delete Event</button>
                <div class="flex gap-2">
                    <button id="closeViewEventBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Close</button>
                    <button id="editEventBtn" class="px-4 py-2 bg-blue-600 text-white rounded">Edit Event</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('scripts')

<!-- JavaScript for Interactivity -->
<script>
    // State variables
    let currentView = "month";
    let searchQuery = "";
    let selectedEvent = null;
    let selectedDate = null;

    // Data passed from Blade
    const events = @json($events);
    const availableVolunteers = @json($availableVolunteers);

    // Handle view tab clicks
    document.querySelectorAll("#viewTabs button").forEach(button => {
        button.addEventListener("click", () => {
            currentView = button.getAttribute("data-view");
            renderCalendarView();
        });
    });

    // Search filtering
    document.getElementById("searchInput").addEventListener("input", function() {
        searchQuery = this.value.toLowerCase();
        renderCalendarView();
    });

    // Render the calendar view depending on currentView value
    function renderCalendarView() {
        const container = document.getElementById("calendarView");
        if (currentView === "month") {
            container.innerHTML = renderMonthView();
        } else if (currentView === "week") {
            container.innerHTML = renderWeekView();
        } else if (currentView === "day") {
            container.innerHTML = renderDayView();
        }
    }

    // Render a simple Month view (for demo purposes, using July 2023)
    function renderMonthView() {
        const year = 2023,
            month = 6,
            lastDay = 31;
        let html = '<div class="grid grid-cols-7 gap-1">';
        // Render day headers
        const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        days.forEach(day => {
            html += `<div class="p-2 text-center font-medium">${day}</div>`;
        });
        // Render cells (assume 35 cells)
        for (let i = 1; i <= 35; i++) {
            let dayNum = i;
            html += `<div class="p-2 min-h-[80px] border rounded-md ${dayNum === new Date().getDate() ? "bg-gray-200" : ""}" onclick="setSelectedDate(${year}, ${month}, ${dayNum})">`;
            if (dayNum <= lastDay) {
                html += `<div class="font-medium">${dayNum}</div>`;
                // Filter events for this day, matching search query if provided
                const cellEvents = events.filter(event => {
                    const d = new Date(event.date);
                    return d.getFullYear() === year && d.getMonth() === month && d.getDate() === dayNum &&
                        (event.title.toLowerCase().includes(searchQuery) ||
                            event.description.toLowerCase().includes(searchQuery) ||
                            event.location.toLowerCase().includes(searchQuery));
                });
                cellEvents.forEach(ev => {
                    html += `<div class="${ev.color} text-white p-1 rounded-md text-xs mt-1 cursor-pointer truncate" onclick="event.stopPropagation(); openViewEvent(${ev.id});">${ev.title}</div>`;
                });
            }
            html += `</div>`;
        }
        html += '</div>';
        return html;
    }

    // Render a basic Week view (simplified)
    function renderWeekView() {
        let html = '<div class="border rounded-md">';
        html += '<div class="grid grid-cols-8 border-b">';
        html += '<div class="p-2 border-r"></div>';
        const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        days.forEach(day => {
            html += `<div class="p-2 text-center font-medium border-r last:border-r-0">${day}</div>`;
        });
        html += '</div>';
        html += '<div class="grid grid-cols-8">';
        // Time slots from 8 AM to 7 PM (12 slots)
        for (let i = 0; i < 12; i++) {
            const hour = i + 8;
            let displayHour = hour > 12 ? hour - 12 : hour;
            let period = hour >= 12 ? "PM" : "AM";
            html += `<div class="p-2 border-r text-xs text-center">${displayHour} ${period}</div>`;
            // 7 days per slot
            for (let d = 0; d < 7; d++) {
                html += `<div class="p-1 border-r last:border-r-0 border-b min-h-[60px]"></div>`;
            }
        }
        html += '</div></div>';
        return html;
    }

    // Render a simplified Day view
    function renderDayView() {
        const date = selectedDate || new Date(2023, 6, 16);
        let html = `<div class="border rounded-md">
                    <div class="p-4 border-b font-medium text-center">
                      ${date.toLocaleDateString("en-US", { weekday: "long", month: "long", day: "numeric" })}
                    </div>`;
        html += '<div class="grid grid-cols-1">';
        for (let i = 0; i < 12; i++) {
            const hour = i + 8;
            let displayHour = hour > 12 ? hour - 12 : hour;
            let period = hour >= 12 ? "PM" : "AM";
            html += `<div class="p-2 border-b flex">
                   <div class="w-20 text-sm">${displayHour} ${period}</div>
                   <div class="flex-1 min-h-[60px]"></div>
                 </div>`;
        }
        html += '</div></div>';
        return html;
    }

    // Set the selected date (for day view)
    function setSelectedDate(year, month, day) {
        selectedDate = new Date(year, month, day);
        if (currentView === "day") renderCalendarView();
    }

    // Open the View Event Modal and populate with event details
    function openViewEvent(id) {
        const eventObj = events.find(e => e.id === id);
        if (!eventObj) return;
        selectedEvent = eventObj;
        let html = `<div class="py-4">
                    <h2 class="text-xl font-bold mb-2">${eventObj.title}</h2>
                    <p class="text-sm text-gray-500 mb-2">${eventObj.description}</p>
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                      <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                      </svg>
                      <span>${new Date(eventObj.date).toLocaleDateString()}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                      <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3" />
                      </svg>
                      <span>${eventObj.startTime} - ${eventObj.endTime}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                      <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9l-5 5-5-5" />
                      </svg>
                      <span>${eventObj.location}</span>
                    </div>
                    <div class="mt-4">
                      <h3 class="font-medium text-sm mb-2">Assigned Volunteers</h3>`;
        eventObj.volunteers.forEach(v => {
            html += `<div class="flex items-center mb-2">
                   <div class="w-6 h-6 rounded-full overflow-hidden">
                     <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(v.name)}" alt="${v.name}">
                   </div>
                   <div class="ml-2 text-sm">
                     <span>${v.name}</span>
                     <span class="ml-1 px-2 py-1 bg-gray-100 rounded text-xs">${v.role}</span>
                   </div>
                 </div>`;
        });
        html += `</div></div>`;
        document.getElementById("viewEventContent").innerHTML = html;
        document.getElementById("viewEventModal").classList.remove("hidden");
        document.getElementById("viewEventModal").classList.add("flex");
    }

    // Modal toggling for View Event Modal
    document.getElementById("closeViewEvent").addEventListener("click", () => {
        document.getElementById("viewEventModal").classList.add("hidden");
        document.getElementById("viewEventModal").classList.remove("flex");
    });
    document.getElementById("closeViewEventBtn").addEventListener("click", () => {
        document.getElementById("viewEventModal").classList.add("hidden");
        document.getElementById("viewEventModal").classList.remove("flex");
    });
    document.getElementById("editEventBtn").addEventListener("click", () => {
        alert("Edit event functionality would open here");
        document.getElementById("viewEventModal").classList.add("hidden");
        document.getElementById("viewEventModal").classList.remove("flex");
    });
    document.getElementById("deleteEventBtn").addEventListener("click", () => {
        if (confirm("Are you sure you want to delete this event? This action cannot be undone.")) {
            alert("Event deleted (demo only)");
            document.getElementById("viewEventModal").classList.add("hidden");
            document.getElementById("viewEventModal").classList.remove("flex");
        }
    });

    // Modal toggling for Create Event Modal
    document.getElementById("newEventBtn").addEventListener("click", () => {
        document.getElementById("createEventModal").classList.remove("hidden");
        document.getElementById("createEventModal").classList.add("flex");
    });
    document.getElementById("closeCreateEvent").addEventListener("click", () => {
        document.getElementById("createEventModal").classList.add("hidden");
        document.getElementById("createEventModal").classList.remove("flex");
    });
    document.getElementById("cancelCreateEvent").addEventListener("click", () => {
        document.getElementById("createEventModal").classList.add("hidden");
        document.getElementById("createEventModal").classList.remove("flex");
    });
    document.getElementById("createEventForm").addEventListener("submit", function(e) {
        e.preventDefault();
        alert("Event created successfully! (Demo only)");
        document.getElementById("createEventModal").classList.add("hidden");
        document.getElementById("createEventModal").classList.remove("flex");
    });

    // Initial render of calendar view
    renderCalendarView();
</script>

@endsection