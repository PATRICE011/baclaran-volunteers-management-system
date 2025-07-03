@extends('components.layout')

@section('title', 'Schedule')

@section('styles')
<style>
    .modal-bg {
        background: rgba(0, 0, 0, 0.5);
    }

    .status-present {
        @apply bg-green-100 text-green-800;
    }

    .status-absent {
        @apply bg-red-100 text-red-800;
    }

    .status-pending {
        @apply bg-yellow-100 text-yellow-800;
    }
</style>
@endsection

@section('content')
@include('components.navs')

<div class="flex-1 flex flex-col overflow-auto p-6 space-y-6 md:ml-64">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Schedule Calendar</h1>
        <div class="flex items-center space-x-4">
            <div id="viewTabs" class="flex space-x-2 bg-gray-100 rounded-md p-1">
                <button data-view="month" class="px-4 py-2 bg-white rounded-md text-gray-700 shadow">Month</button>
                <button data-view="week" class="px-4 py-2 rounded-md text-gray-600 hover:bg-white">Week</button>
                <button data-view="day" class="px-4 py-2 rounded-md text-gray-600 hover:bg-white">Day</button>
            </div>
            <button id="newEventBtn"
                class="flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Event
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1011 17a6 6 0 006-6z" />
            </svg>
        </div>
        <input id="searchInput" type="text" placeholder="Search events..."
            class="pl-10 pr-3 py-2 border rounded w-full focus:border-blue-500" />
    </div>

    <!-- Calendar + Sidebar -->
    <div class="flex gap-6">
        <!-- Calendar Panel -->
        <div class="flex-1 border rounded-md p-4 space-y-4">
            <div class="flex justify-between items-center">
                <button id="prevMonth" class="px-2 py-1 text-gray-600 hover:bg-gray-200 rounded">&lt;</button>
                <h2 id="monthYearHeader" class="text-lg font-semibold"></h2>
                <button id="nextMonth" class="px-2 py-1 text-gray-600 hover:bg-gray-200 rounded">&gt;</button>
            </div>
            <div id="calendarView"></div>
        </div>

        <!-- Sidebar -->
        <div class="w-80 flex-shrink-0 space-y-6">
            <!-- Upcoming Events -->
            <div class="bg-white shadow rounded">
                <div class="px-4 py-3 border-b">
                    <h2 class="font-semibold text-lg">Upcoming Events</h2>
                    <p class="text-sm text-gray-500">Next 7 days</p>
                </div>
                <div class="p-4 max-h-72 overflow-auto space-y-2">
                    @foreach ($events as $ev)
                    <div class="flex items-start cursor-pointer hover:bg-gray-50 rounded-md p-2"
                        onclick="openViewEvent({{ $ev['id'] }})">
                        <div class="w-2 h-2 rounded-full mt-1.5 bg-{{ $ev['color'] }}-500"></div>
                        <div class="ml-2 flex-1">
                            <h4 class="font-medium text-sm">{{ $ev['title'] }}</h4>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                </svg>
                                <span>{{ date('M d, Y', strtotime($ev['date'])) }}</span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3" />
                                </svg>
                                <span>{{ date('g:i A', strtotime($ev['start_time'])) }} – {{ date('g:i A', strtotime($ev['end_time'])) }}</span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $ev['location'] }}</span>
                            </div>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <span>{{ count($ev['volunteers']) }} volunteers</span>
                            </div>
                        </div>
                        <button type="button" class="ml-2 focus:outline-none"
                            onclick="event.stopPropagation(); openViewEvent({{ $ev['id'] }});">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Available Volunteers -->
            <div class="bg-white shadow rounded">
                <div class="px-4 py-3 border-b">
                    <h2 class="font-semibold text-lg">Available Volunteers</h2>
                    <p class="text-sm text-gray-500">Active volunteers ready to serve</p>
                </div>
                <div class="p-4 max-h-72 overflow-auto space-y-4">
                    @foreach ($availableVolunteers as $vol)
                    {{-- Ensure volunteer has a status of Active --}}
                    @if($vol->detail->volunteer_status === 'Active')
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200">
                            {{-- Check if volunteer has a profile picture --}}
                            @if($vol->profile_picture)
                            <img src="{{ asset('storage/' . $vol->profile_picture) }}"
                                alt="{{ $vol->detail->full_name }}"
                                class="w-full h-full object-cover">
                            @else
                            {{-- If no profile picture, use DiceBear for default avatar --}}
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($vol->detail->full_name) }}"
                                alt="{{ $vol->detail->full_name }}"
                                class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="ml-4">
                            <p class="font-semibold">{{ $vol->detail->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $vol->detail->ministry->ministry_name }}</p>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>


            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div id="createEventModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative max-h-[80vh] overflow-y-auto">
            <button id="closeCreateEvent" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                ✕
            </button>
            <h2 class="text-xl font-bold mb-4">Create New Event</h2>
            <form id="createEventForm" class="grid gap-4 py-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Event Title</label>
                        <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Date</label>
                        <input type="date" name="date" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Start Time</label>
                        <input type="time" name="start_time" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">End Time</label>
                        <input type="time" name="end_time" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Location</label>
                    <input type="text" name="location" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Color</label>
                    <select name="color" class="w-full border rounded px-3 py-2">
                        <option value="blue">Blue</option>
                        <option value="green">Green</option>
                        <option value="purple">Purple</option>
                        <option value="red">Red</option>
                        <option value="yellow">Yellow</option>
                        <option value="indigo">Indigo</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancelCreateEvent"
                        class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Event Modal -->
    <div id="viewEventModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative max-h-[80vh] overflow-y-auto">
            <button id="closeViewEvent" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">✕</button>
            <div id="viewEventContent"></div>
            <div class="flex justify-between mt-4">
                <button id="deleteEventBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete Event</button>
                <div class="flex gap-2">
                    <button id="closeViewEventBtn"
                        class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Close</button>
                    <button id="editEventBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Edit Event</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // State
    let currentView = 'month',
        searchQuery = '',
        selectedDate = null,
        today = new Date(),
        currentYear = today.getFullYear(),
        currentMonth = today.getMonth();

    const events = @json($events),
        availableVolunteers = @json($availableVolunteers);

    // Refs
    const calendarView = document.getElementById('calendarView'),
        monthYearHeader = document.getElementById('monthYearHeader'),
        searchInput = document.getElementById('searchInput');

    // Tab clicks
    document.querySelectorAll('#viewTabs button').forEach(btn =>
        btn.addEventListener('click', () => {
            document.querySelectorAll('#viewTabs button').forEach(b => b.classList.remove('bg-white', 'shadow'));
            btn.classList.add('bg-white', 'shadow');
            currentView = btn.dataset.view;
            renderCalendar();
        })
    );

    // Search
    searchInput.addEventListener('input', e => {
        searchQuery = e.target.value.toLowerCase();
        renderCalendar();
    });

    // Prev/Next month
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar();
    });
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    });

    // Render dispatcher
    function renderCalendar() {
        if (currentView === 'month') renderMonthView();
        else if (currentView === 'week') renderWeekView();
        else renderDayView();
    }

    // Month view
    function renderMonthView() {
        const year = currentYear,
            month = currentMonth;
        const monthName = new Date(year, month).toLocaleString('default', {
            month: 'long'
        });
        monthYearHeader.textContent = `${monthName} ${year}`;

        const firstDay = new Date(year, month, 1).getDay(),
            daysInMonth = new Date(year, month + 1, 0).getDate();

        let html = '<div class="grid grid-cols-7 gap-1">';
        ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(d => {
            html += `<div class="p-2 text-center font-medium text-gray-600 text-sm">${d}</div>`;
        });
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="p-2 min-h-[80px] border rounded-md bg-gray-50"></div>';
        }
        for (let d = 1; d <= daysInMonth; d++) {
            const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            html +=
                `<div class="p-2 min-h-[80px] border rounded-md cursor-pointer hover:bg-gray-50 ${isToday?'bg-blue-50 border-blue-200':''}" onclick="selectDay(${year},${month},${d})">`;
            html += `<div class="font-medium text-sm ${isToday?'text-blue-600':'text-gray-900'}">${d}</div>`;
            const cellEvents = events.filter(ev => {
                const dt = new Date(ev.date);
                return dt.getFullYear() === year &&
                    dt.getMonth() === month &&
                    dt.getDate() === d &&
                    (ev.title.toLowerCase().includes(searchQuery) ||
                        ev.description.toLowerCase().includes(searchQuery) ||
                        ev.location.toLowerCase().includes(searchQuery));
            });
            cellEvents.forEach(ev => {
                html +=
                    `<div class="bg-${ev.color}-500 text-white p-1 rounded text-xs mt-1 truncate cursor-pointer hover:bg-${ev.color}-600" onclick="event.stopPropagation();openViewEvent(${ev.id});">${ev.title}</div>`;
            });
            html += '</div>';
        }
        html += '</div>';
        calendarView.innerHTML = html;
    }

    // Week view (simplified)
    function renderWeekView() {
        let html = '<div class="border rounded-md"><div class="grid grid-cols-8 border-b">';
        html += '<div class="p-2 border-r"></div>';
        ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(d => {
            html += `<div class="p-2 text-center font-medium border-r last:border-r-0">${d}</div>`;
        });
        html += '</div><div class="grid grid-cols-8">';
        for (let i = 0; i < 12; i++) {
            const hr = i + 8,
                disp = hr > 12 ? hr - 12 : hr,
                pm = hr >= 12 ? 'PM' : 'AM';
            html += `<div class="p-2 border-r text-xs text-center">${disp} ${pm}</div>`;
            for (let d = 0; d < 7; d++) {
                html += '<div class="p-1 border-r last:border-r-0 border-b min-h-[60px]"></div>';
            }
        }
        html += '</div></div>';
        calendarView.innerHTML = html;
    }

    // Day view (simplified)
    function renderDayView() {
        const date = selectedDate || new Date(currentYear, currentMonth, today.getDate());
        const header = date.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric'
        });
        let html =
            `<div class="border rounded-md"><div class="p-4 border-b font-medium text-center">${header}</div><div class="grid grid-cols-1">`;
        for (let i = 0; i < 12; i++) {
            const hr = i + 8,
                disp = hr > 12 ? hr - 12 : hr,
                pm = hr >= 12 ? 'PM' : 'AM';
            html +=
                `<div class="p-2 border-b flex"><div class="w-20 text-sm">${disp} ${pm}</div><div class="flex-1 min-h-[60px]"></div></div>`;
        }
        html += '</div></div>';
        calendarView.innerHTML = html;
    }

    function selectDay(y, m, d) {
        selectedDate = new Date(y, m, d);
        if (currentView === 'day') renderDayView();
    }

    // View Event Modal
    function openViewEvent(id) {
        let avatarUrl = v.profile_picture ||
            `https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(v.volunteer_detail.full_name)}`;


        let html = `<div class="py-4">
      <h2 class="text-xl font-bold mb-2">${ev.title}</h2>
      <p class="text-sm text-gray-600 mb-4">${ev.description || 'No description available'}</p>
      
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="flex items-center text-sm text-gray-600">
          <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/>
          </svg>
          <span>${new Date(ev.date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
        </div>
        <div class="flex items-center text-sm text-gray-600">
          <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3"/>
          </svg>
          <span>${new Date('1970-01-01T' + ev.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })} – ${new Date('1970-01-01T' + ev.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</span>
        </div>
      </div>
      
      <div class="flex items-center text-sm text-gray-600 mb-4">
        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>${ev.location || 'Location not specified'}</span>
      </div>
      
      <div class="mt-6">
        <h3 class="font-semibold text-lg mb-3 flex items-center">
          <svg class="h-5 w-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
          </svg>
          Assigned Volunteers (${ev.volunteers.length})
        </h3>
        <div class="space-y-3">`;

        ev.volunteers.forEach(v => {
            const volunteer = v.volunteer_detail;
            const statusClass = v.pivot.attendance_status.toLowerCase();
            const statusBadge = v.pivot.attendance_status;
            const checkedInTime = v.pivot.checked_in_at ? new Date(v.pivot.checked_in_at).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            }) : null;

            html += `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                        <img src="${avatarUrl}" alt="${v.volunteer_detail.full_name}" class="w-full h-full object-cover">
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-sm">${v.volunteer_detail.full_name}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">${v.volunteer_detail.line_group}</span>
                            ${v.volunteer_detail.ministry ? `<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">${v.volunteer_detail.ministry.name}</span>` : ''}
                        </div>
                        ${v.pivot.checked_in_at ? `<p class="text-xs text-gray-500 mt-1">Checked in at ${new Date(v.pivot.checked_in_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</p>` : ''}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded text-xs status-${v.pivot.attendance_status.toLowerCase()}">${v.pivot.attendance_status}</span>
                    <button onclick="toggleAttendance(${ev.id}, ${v.id})" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                        ${v.pivot.attendance_status === 'Present' ? 'Mark Absent' : 'Check In'}
                    </button>
                </div>
            </div>`;
        });

        html += `</div>
        <div class="mt-4">
          <button onclick="openAddVolunteerModal(${ev.id})" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
            + Add Volunteer
          </button>
        </div>
      </div>
    </div>`;

        document.getElementById('viewEventContent').innerHTML = html;
        document.getElementById('viewEventModal').classList.replace('hidden', 'flex');
    }

    // Toggle attendance function
    function toggleAttendance(eventId, volunteerId) {
        // This would typically make an AJAX call to update the database
        alert(`Attendance toggled for volunteer ${volunteerId} in event ${eventId} (demo)`);
    }

    // Add volunteer modal function
    function openAddVolunteerModal(eventId) {
        alert(`Open add volunteer modal for event ${eventId} (demo)`);
    }

    // Modal toggles
    ['closeViewEvent', 'closeViewEventBtn'].forEach(id =>
        document.getElementById(id).addEventListener('click', () => {
            document.getElementById('viewEventModal').classList.replace('flex', 'hidden');
        })
    );

    document.getElementById('deleteEventBtn').addEventListener('click', () => {
        if (confirm("Are you sure you want to delete this event? This action cannot be undone.")) {
            alert("Event deleted (demo)");
            document.getElementById('viewEventModal').classList.replace('flex', 'hidden');
        }
    });

    document.getElementById('editEventBtn').addEventListener('click', () => {
        alert("Edit event functionality (demo)");
        document.getElementById('viewEventModal').classList.replace('flex', 'hidden');
    });

    // Create Event Modal
    document.getElementById('newEventBtn').addEventListener('click', () => {
        document.getElementById('createEventModal').classList.replace('hidden', 'flex');
    });

    ['closeCreateEvent', 'cancelCreateEvent'].forEach(id =>
        document.getElementById(id).addEventListener('click', () => {
            document.getElementById('createEventModal').classList.replace('flex', 'hidden');
        })
    );

    document.getElementById('createEventForm').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const eventData = Object.fromEntries(formData.entries());
        console.log('Creating event:', eventData);
        alert("Event created successfully! (demo)");
        document.getElementById('createEventModal').classList.replace('flex', 'hidden');
        e.target.reset();
    });

    // Close modals when clicking outside
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-bg')) {
            document.querySelectorAll('.modal-bg').forEach(modal => {
                modal.classList.replace('flex', 'hidden');
            });
        }
    });

    // Initial render
    renderCalendar();
</script>
@endsection