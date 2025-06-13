@php
    // Mock event data (ISO date strings)
    $current = request()->is('schedule') ? 'active' : '';
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
            'color' => 'bg-blue-500',
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
            'color' => 'bg-green-500',
        ],
        [
            'id' => 3,
            'title' => 'Prayer Meeting',
            'date' => '2023-07-19T00:00:00.000Z',
            'startTime' => '7:00 PM',
            'endTime' => '8:00 PM',
            'location' => 'Chapel',
            'description' => 'Midweek prayer gathering',
            'volunteers' => [['id' => 6, 'name' => 'Robert Davis', 'role' => 'Prayer Leader']],
            'color' => 'bg-purple-500',
        ],
    ];
    // Mock available volunteers
    $availableVolunteers = [
        ['id' => 1, 'name' => 'John Doe', 'skills' => ['Usher', 'Greeter'], 'availability' => 'Weekends'],
        ['id' => 2, 'name' => 'Jane Smith', 'skills' => ['Worship', 'Music'], 'availability' => 'Evenings'],
        ['id' => 3, 'name' => 'Mike Johnson', 'skills' => ['Tech', 'Sound'], 'availability' => 'Weekends'],
        [
            'id' => 4,
            'name' => 'Sarah Williams',
            'skills' => ['Youth', 'Teaching'],
            'availability' => 'Weekends, Evenings',
        ],
        ['id' => 5, 'name' => 'Tom Brown', 'skills' => ['Children', 'Games'], 'availability' => 'Weekends'],
        ['id' => 6, 'name' => 'Robert Davis', 'skills' => ['Prayer', 'Counseling'], 'availability' => 'Evenings'],
        [
            'id' => 7,
            'name' => 'Lisa Garcia',
            'skills' => ['Hospitality', 'Cooking'],
            'availability' => 'Weekends, Evenings',
        ],
        [
            'id' => 8,
            'name' => 'David Wilson',
            'skills' => ['Administration', 'Organization'],
            'availability' => 'Weekdays',
        ],
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
                                <div class="w-2 h-2 rounded-full mt-1.5 {{ $ev['color'] }}"></div>
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
                                        <span>{{ $ev['startTime'] }} – {{ $ev['endTime'] }}</span>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500 mt-1">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9l-5 5-5-5" />
                                        </svg>
                                        <span>{{ $ev['location'] }}</span>
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
                        <p class="text-sm text-gray-500">People ready to serve</p>
                    </div>
                    <div class="p-4 max-h-72 overflow-auto space-y-4">
                        @foreach ($availableVolunteers as $vol)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full overflow-hidden">
                                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($vol['name']) }}"
                                        alt="{{ $vol['name'] }}">
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium">{{ $vol['name'] }}</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach ($vol['skills'] as $skill)
                                            <span
                                                class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">{{ $skill }}</span>
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
            <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative max-h-[80vh] overflow-y-auto">
                <button id="closeCreateEvent" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                    ✕
                </button>
                <h2 class="text-xl font-bold mb-4">Create New Event</h2>
                <form id="createEventForm" class="grid gap-4 py-4">
                    <!-- your form fields here -->
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
                    <button id="deleteEventBtn" class="px-4 py-2 bg-red-600 text-white rounded">Delete Event</button>
                    <div class="flex gap-2">
                        <button id="closeViewEventBtn"
                            class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Close</button>
                        <button id="editEventBtn" class="px-4 py-2 bg-blue-600 text-white rounded">Edit Event</button>
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
                html += `<div class="p-2 text-center font-medium">${d}</div>`;
            });
            for (let i = 0; i < firstDay; i++) {
                html += '<div class="p-2 min-h-[80px] border rounded-md bg-gray-50"></div>';
            }
            for (let d = 1; d <= daysInMonth; d++) {
                const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
                html +=
                    `<div class="p-2 min-h-[80px] border rounded-md ${isToday?'bg-gray-200':''}" onclick="selectDay(${year},${month},${d})">`;
                html += `<div class="font-medium">${d}</div>`;
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
                        `<div class="${ev.color} text-white p-1 rounded text-xs mt-1 truncate cursor-pointer" onclick="event.stopPropagation();openViewEvent(${ev.id});">${ev.title}</div>`;
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
            const ev = events.find(x => x.id === id);
            if (!ev) return;
            let html = `<div class="py-4">
      <h2 class="text-xl font-bold mb-2">${ev.title}</h2>
      <p class="text-sm text-gray-500 mb-2">${ev.description}</p>
      <div class="flex items-center text-sm text-gray-500 mb-2">
        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/>
        </svg>
        <span>${new Date(ev.date).toLocaleDateString()}</span>
      </div>
      <div class="flex items-center text-sm text-gray-500 mb-2">
        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4l3 3"/>
        </svg>
        <span>${ev.startTime} – ${ev.endTime}</span>
      </div>
      <div class="flex items-center text-sm text-gray-500 mb-2">
        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 9l-5 5-5-5"/>
        </svg>
        <span>${ev.location}</span>
      </div>
      <div class="mt-4"><h3 class="font-medium text-sm mb-2">Assigned Volunteers</h3>`;
            ev.volunteers.forEach(v => {
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

            document.getElementById('viewEventContent').innerHTML = html;
            document.getElementById('viewEventModal').classList.replace('hidden', 'flex');
        }

        // Modal toggles
        ['closeViewEvent', 'closeViewEventBtn'].forEach(id =>
            document.getElementById(id).addEventListener('click', () => {
                document.getElementById('viewEventModal').classList.replace('flex', 'hidden');
            })
        );
        document.getElementById('deleteEventBtn').addEventListener('click', () => {
            if (confirm("Delete this event?")) {
                alert("Deleted (demo)");
                document.getElementById('viewEventModal').classList.replace('flex', 'hidden');
            }
        });
        document.getElementById('editEventBtn').addEventListener('click', () => {
            alert("Edit (demo)");
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
            alert("Created (demo)");
            document.getElementById('createEventModal').classList.replace('flex', 'hidden');
        });

        // Initial render
        renderCalendar();
    </script>
@endsection
