@extends('components.layout')
@section('title','Dashboard')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin_dashboard.css') }}">
@endsection
@section('content')

<body class="bg-gray-50">
  @php
  $current = request()->is('dashboard') ? 'active' : '';

  $metrics = [
  'totalVolunteers' => 124,
  'activeVolunteers' => 98,
  'upcomingEvents' => 12,
  'taskCompletionRate' => 78,
  'recentVolunteers' => [
  [ 'id' => 1, 'name' => "Sarah Johnson", 'role' => "Worship Team", 'avatar' => "SJ" ],
  [ 'id' => 2, 'name' => "Michael Chen", 'role' => "Children's Ministry", 'avatar' => "MC" ],
  [ 'id' => 3, 'name' => "David Wilson", 'role' => "Tech Team", 'avatar' => "DW" ],
  [ 'id' => 4, 'name' => "Rebecca Taylor", 'role' => "Hospitality", 'avatar' => "RT" ],
  ],
  'upcomingTasks' => [
  [ 'id' => 1, 'title' => "Sunday Service Setup", 'date' => "2023-06-18", 'assignee' => "David Wilson" ],
  [ 'id' => 2, 'title' => "Children's Program Preparation", 'date' => "2023-06-17", 'assignee' => "Michael Chen" ],
  [ 'id' => 3, 'title' => "Worship Team Rehearsal", 'date' => "2023-06-16", 'assignee' => "Sarah Johnson" ],
  ],
  ];
  @endphp
    <!-- Main Content -->
     @include('components.navs')
      <!-- Dashboard Content -->
      <main class="flex-1 overflow-auto p-4 sm:p-6">
        <div class="grid gap-6">
        <h1 class="text-2xl font-bold">Dashboard</h1>
          <!-- Metrics Cards -->
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Total Volunteers Card -->
            <div class="bg-white rounded shadow p-4">
              <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium">Total Volunteers</h3>
                <span>
                  <!-- Users Icon -->
                  <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h5v-2a4 4 0 00-3-3.87M9 20V10a4 4 0 00-8 0v10m12-10a4 4 0 018 0v10" />
                  </svg>
                </span>
              </div>
              <div class="text-2xl font-bold">
                {{ $metrics['totalVolunteers'] }}
              </div>
              <p class="text-xs text-gray-500">{{ $metrics['activeVolunteers'] }} currently active</p>
            </div>
            <!-- Upcoming Events Card -->
            <div class="bg-white rounded shadow p-4">
              <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium">Upcoming Events</h3>
                <span>
                  <!-- Calendar Icon -->
                  <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                  </svg>
                </span>
              </div>
              <div class="text-2xl font-bold">{{ $metrics['upcomingEvents'] }}</div>
              <p class="text-xs text-gray-500">Next 30 days</p>
            </div>
            <!-- Task Completion Card -->
            <div class="bg-white rounded shadow p-4">
              <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium">Task Completion</h3>
                <span>
                  <!-- Check Circle Icon -->
                  <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4" />
                  </svg>
                </span>
              </div>
              <div class="text-2xl font-bold">{{ $metrics['taskCompletionRate'] }}%</div>
              <div class="w-full bg-gray-300 rounded h-2 mt-2">
                <div class="bg-blue-500 h-2 rounded" style="width: {{ $metrics['taskCompletionRate'] }}%"></div>
              </div>
            </div>
            <!-- Ministry Distribution Card -->
            <div class="bg-white rounded shadow p-4">
              <div class="flex items-center justify-between pb-2">
                <h3 class="text-sm font-medium">Ministry Distribution</h3>
                <span>
                  <!-- Pie Chart Icon -->
                  <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 4.055A9 9 0 1020.945 13H11V4.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20.94 13a9 9 0 11-7.88-7.88" />
                  </svg>
                </span>
              </div>
              <div class="flex items-center justify-center h-16">
                <div class="flex gap-1">
                  <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                  <div class="h-2 w-2 rounded-full bg-green-500"></div>
                  <div class="h-2 w-2 rounded-full bg-yellow-500"></div>
                  <div class="h-2 w-2 rounded-full bg-red-500"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabs Section -->
          <div class="mt-2">
            <!-- Tailwind "pill" style tabs -->
            <div class="inline-flex items-center bg-gray-100 rounded-md p-1">
              <!-- Active tab button -->
              <button
                class="bg-white rounded-md text-gray-700 font-medium px-4 py-2 shadow focus:outline-none" data-target="overview">
                Overview
              </button>
              <!-- Inactive tab buttons -->
              <button
                class="rounded-md text-gray-600 px-4 py-2 hover:bg-white hover:text-gray-800 focus:outline-none" data-target="volunteers">
                Volunteers
              </button>
              <button
                class="rounded-md text-gray-600 px-4 py-2 hover:bg-white hover:text-gray-800 focus:outline-none" data-target="tasks">
                Tasks
              </button>
            </div>

            <!-- Tab Contents -->
            <div id="overview" class="mt-4">
              <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                <!-- Volunteer Activity (Mock Bar Chart) -->
                <div class="bg-white rounded shadow p-4 lg:col-span-4">
                  <div>
                    <h3 class="text-lg font-semibold">Volunteer Activity</h3>
                    <p class="text-sm text-gray-500">Volunteer participation over the last 30 days</p>
                  </div>
                  <div class="h-48 flex items-end gap-2 justify-between mt-4">
                    @for ($i = 0; $i < 7; $i++)
                      <div class="bg-blue-500 rounded-t w-full" style="height: {{ rand(20, 120) }}px;">
                  </div>
                  @endfor
                </div>
              </div>
              <!-- Quick Actions -->
              <div class="bg-white rounded shadow p-4 lg:col-span-3">
                <div>
                  <h3 class="text-lg font-semibold">Quick Actions</h3>
                  <p class="text-sm text-gray-500">Common tasks and actions</p>
                </div>
                <div class="space-y-2 mt-4">
                  <a href="#" class="flex justify-between items-center border px-3 py-2 rounded hover:bg-blue-500 hover:text-white">
                    <span class="flex items-center gap-2">
                      <!-- Users Icon -->
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h5v-2a4 4 0 00-3-3.87M9 20V10a4 4 0 00-8 0v10m12-10a4 4 0 018 0v10" />
                      </svg>
                      Add New Volunteer
                    </span>
                    <span>
                      <!-- Chevron Right Icon -->
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7" />
                      </svg>
                    </span>
                  </a>
                  <a href="#" class="flex justify-between items-center border px-3 py-2 rounded hover:bg-blue-500 hover:text-white">
                    <span class="flex items-center gap-2">
                      <!-- Calendar Icon -->
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                      </svg>
                      Schedule Event
                    </span>
                    <span>
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7" />
                      </svg>
                    </span>
                  </a>
                  <a href="#" class="flex justify-between items-center border px-3 py-2 rounded hover:bg-blue-500 hover:text-white">
                    <span class="flex items-center gap-2">
                      <!-- List Icon -->
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7" />
                      </svg>
                      Create Task
                    </span>
                    <span>
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7" />
                      </svg>
                    </span>
                  </a>
                  <a href="#" class="flex justify-between items-center border px-3 py-2 rounded hover:bg-blue-500 hover:text-white">
                    <span class="flex items-center gap-2">
                      <!-- Bar Chart Icon -->
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3v18h18" />
                      </svg>
                      View Reports
                    </span>
                    <span>
                      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7" />
                      </svg>
                    </span>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div id="volunteers" class="hidden mt-4">
            <div class="bg-white rounded shadow p-4">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold">Recent Volunteers</h3>
                  <p class="text-sm text-gray-500">Recently active volunteers</p>
                </div>
                <a href="#" class="py-1 px-2 border rounded hover:bg-blue-500 hover:text-white inline-flex items-center">
                  <!-- Plus Icon -->
                  <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4" />
                  </svg>
                  Add Volunteer
                </a>
              </div>
              <div class="mt-4 space-y-4">
                @foreach ($metrics['recentVolunteers'] as $volunteer)
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full overflow-hidden">
                      <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($volunteer['name']) }}"
                        alt="{{ $volunteer['name'] }}">
                    </div>
                    <div>
                      <p class="text-sm font-medium">{{ $volunteer['name'] }}</p>
                      <p class="text-xs text-gray-500">{{ $volunteer['role'] }}</p>
                    </div>
                  </div>
                  <a href="#" class="p-1 border rounded hover:bg-blue-500 hover:text-white">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                      viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5l7 7-7 7" />
                    </svg>
                  </a>
                </div>
                @endforeach
              </div>
            </div>
          </div>

          <div id="tasks" class="hidden mt-4">
            <div class="bg-white rounded shadow p-4">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold">Upcoming Tasks</h3>
                  <p class="text-sm text-gray-500">Tasks scheduled for the next 7 days</p>
                </div>
                <a href="#" class="py-1 px-2 border rounded hover:bg-blue-500 hover:text-white inline-flex items-center">
                  <!-- Plus Icon -->
                  <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4" />
                  </svg>
                  Add Task
                </a>
              </div>
              <div class="mt-4 space-y-4">
                @foreach ($metrics['upcomingTasks'] as $task)
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium">{{ $task['title'] }}</p>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <span>{{ $task['date'] }}</span>
                      <span>•</span>
                      <span>{{ $task['assignee'] }}</span>
                    </div>
                  </div>
                  <a href="#" class="py-1 px-2 border rounded hover:bg-blue-500 hover:text-white">
                    View
                  </a>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

    </div>
    </main>
  </div>
  </div>

@endsection
@section('scripts')
 <!-- Simple Tabs Script -->
 <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tabButtons = document.querySelectorAll('[data-target]');
      const tabContents = document.querySelectorAll('#overview, #volunteers, #tasks');

      tabButtons.forEach(button => {
        button.addEventListener('click', function() {
          // Remove active class from all tabs and hide contents
          tabButtons.forEach(btn => {
            btn.classList.remove('border-blue-500');
            btn.classList.add('border-transparent');
          });
          tabContents.forEach(content => content.classList.add('hidden'));

          // Activate the selected tab and content
          this.classList.remove('border-transparent');
          this.classList.add('border-blue-500');
          const target = this.getAttribute('data-target');
          document.getElementById(target).classList.remove('hidden');
        });
      });
    });
  </script>
@endsection