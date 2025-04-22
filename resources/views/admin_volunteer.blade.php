<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Volunteer Directory</title>
  <!-- Tailwind CSS via CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <style>
    /* Modal background style */
    .modal-bg {
      background: rgba(0, 0, 0, 0.5);
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <!-- Page Wrapper -->
  <div class="flex h-screen">
    <!-- Sidebar Navigation -->
    <div class="hidden md:flex w-64 flex-col fixed inset-y-0 z-50 bg-white border-r">
      <div class="flex h-14 items-center border-b px-4">
        <h1 class="text-lg font-semibold">Baclaran Church VMS</h1>
      </div>

      <div class="flex-1 overflow-auto py-2">
        <nav class="grid gap-1 px-2">
          <a href="#" class="flex items-center gap-3 rounded-md bg-blue-500 px-3 py-2 text-white">
            <!-- Home Icon -->
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v8a2 2 0 01-2 2H3a2 2 0 01-2-2z" />
            </svg>
            <span>Dashboard</span>
          </a>

          <a href="#" class="flex items-center gap-3 rounded-md px-3 py-2 text-gray-600 hover:bg-blue-500 hover:text-white">
            <!-- Users Icon -->
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h5v-2a4 4 0 00-3-3.87M9 20V10a4 4 0 00-8 0v10m12-10a4 4 0 018 0v10" />
            </svg>
            <span>Volunteers</span>
          </a>

          <a href="#" class="flex items-center gap-3 rounded-md px-3 py-2 text-gray-600 hover:bg-blue-500 hover:text-white">
            <!-- Calendar Icon -->
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
            </svg>
            <span>Schedule</span>
          </a>

          <a href="#" class="flex items-center gap-3 rounded-md px-3 py-2 text-gray-600 hover:bg-blue-500 hover:text-white">
            <!-- List Icon -->
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5l7 7-7 7" />
            </svg>
            <span>Tasks</span>
          </a>
        </nav>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col flex-1 md:pl-64">
      <!-- Header -->
      <header class="sticky top-0 z-40 flex h-14 items-center gap-4 border-b bg-white px-4 sm:px-6">
        <div class="flex flex-1 items-center justify-between">
          <h2 class="text-lg font-semibold">Dashboard</h2>
          <div class="flex items-center gap-4">
            <button class="p-2 border rounded">
              <!-- Bell Icon -->
              <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </button>
            <div class="w-8 h-8 rounded-full overflow-hidden">
              <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=admin" alt="Admin">
            </div>
          </div>
        </div>
      </header>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-auto p-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Volunteer Directory</h1>
        <button id="addVolunteerBtn" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none">
          <!-- Plus icon -->
          <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Add Volunteer
        </button>
      </div>

      <!-- Search and Controls -->
      <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="relative flex-grow">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <!-- Search icon -->
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"></path>
            </svg>
          </div>
          <input id="searchInput" type="text" placeholder="Search volunteers by name, email, skills, or ministry..."
                 class="pl-10 pr-3 py-2 border rounded w-full focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex gap-2">
          <button id="gridViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Grid</button>
          <button id="listViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">List</button>
          <button id="filterBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 flex items-center">
            <!-- Filter icon -->
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707v4.586a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1v-4.586a1 1 0 0 0-.293-.707L1.293 6.707A1 1 0 0 1 1 6V4z"></path>
            </svg>
            Filter
          </button>
        </div>
      </div>

      <!-- Volunteer Data (Inline Mock Data) -->
      @php
        $volunteers = [
          [
            'id' => '1',
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'phone' => '(555) 123-4567',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=john',
            'skills' => ['Music', 'Teaching', 'Leadership'],
            'ministries' => ['Worship', 'Youth'],
            'availability' => ['Sunday Morning', 'Wednesday Evening'],
            'joinDate' => '2022-01-15',
            'status' => 'active',
          ],
          [
            'id' => '2',
            'name' => 'Sarah Johnson',
            'email' => 'sarah.j@example.com',
            'phone' => '(555) 987-6543',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=sarah',
            'skills' => ['Administration', 'Hospitality'],
            'ministries' => ['Children', 'Hospitality'],
            'availability' => ['Sunday Morning', 'Sunday Evening'],
            'joinDate' => '2021-08-22',
            'status' => 'active',
          ],
          [
            'id' => '3',
            'name' => 'Michael Chen',
            'email' => 'michael.c@example.com',
            'phone' => '(555) 456-7890',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=michael',
            'skills' => ['Technical', 'Media', 'Design'],
            'ministries' => ['Media', 'Tech Team'],
            'availability' => ['Sunday Morning', 'Saturday'],
            'joinDate' => '2022-03-10',
            'status' => 'active',
          ],
          [
            'id' => '4',
            'name' => 'Emily Rodriguez',
            'email' => 'emily.r@example.com',
            'phone' => '(555) 234-5678',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=emily',
            'skills' => ['Teaching', 'Counseling'],
            'ministries' => ['Women', 'Prayer'],
            'availability' => ['Tuesday Evening', 'Thursday Evening'],
            'joinDate' => '2021-11-05',
            'status' => 'inactive',
          ],
          [
            'id' => '5',
            'name' => 'David Wilson',
            'email' => 'david.w@example.com',
            'phone' => '(555) 876-5432',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=david',
            'skills' => ['Leadership', 'Finance', 'Teaching'],
            'ministries' => ['Men', 'Finance'],
            'availability' => ['Sunday Morning', 'Monday Evening'],
            'joinDate' => '2022-02-18',
            'status' => 'active',
          ],
          [
            'id' => '6',
            'name' => 'Lisa Thompson',
            'email' => 'lisa.t@example.com',
            'phone' => '(555) 345-6789',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=lisa',
            'skills' => ['Music', 'Hospitality'],
            'ministries' => ['Worship', 'Hospitality'],
            'availability' => ['Sunday Morning', 'Wednesday Evening'],
            'joinDate' => '2021-09-30',
            'status' => 'active',
          ],
        ];
      @endphp

      <!-- Grid View (default) -->
      <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($volunteers as $volunteer)
        <div class="bg-white shadow rounded overflow-hidden cursor-pointer volunteer-card"
             data-name="{{ strtolower($volunteer['name']) }}"
             data-email="{{ strtolower($volunteer['email']) }}"
             data-skills="{{ strtolower(implode(' ', $volunteer['skills'])) }}"
             data-ministries="{{ strtolower(implode(' ', $volunteer['ministries'])) }}"
             data-id="{{ $volunteer['id'] }}"
             onclick="openProfile('{{ $volunteer['id'] }}')">
          <div class="p-4 flex flex-col items-center">
            <img src="{{ $volunteer['avatar'] }}" alt="{{ $volunteer['name'] }}" class="w-20 h-20 rounded-full mb-4">
            <h3 class="font-semibold text-lg">{{ $volunteer['name'] }}</h3>
            <p class="text-sm text-gray-500 mb-2">{{ $volunteer['email'] }}</p>
            <div class="flex flex-wrap gap-1 justify-center mt-2">
              @foreach($volunteer['ministries'] as $ministry)
                <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                  {{ $ministry }}
                </span>
              @endforeach
            </div>
            <span class="mt-3 inline-block px-2 py-1 text-xs rounded {{ $volunteer['status'] === 'active' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
              {{ ucfirst($volunteer['status']) }}
            </span>
          </div>
        </div>
        @endforeach
      </div>

      <!-- List View (hidden by default) -->
      <div id="listView" class="hidden">
        <table class="min-w-full bg-white border">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-4 py-2 text-left">Name</th>
              <th class="px-4 py-2 text-left hidden md:table-cell">Email</th>
              <th class="px-4 py-2 text-left hidden lg:table-cell">Phone</th>
              <th class="px-4 py-2 text-left hidden lg:table-cell">Ministries</th>
              <th class="px-4 py-2 text-left hidden md:table-cell">Status</th>
              <th class="px-4 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($volunteers as $volunteer)
            <tr class="border-t hover:bg-gray-100 volunteer-row"
                data-name="{{ strtolower($volunteer['name']) }}"
                data-email="{{ strtolower($volunteer['email']) }}"
                data-skills="{{ strtolower(implode(' ', $volunteer['skills'])) }}"
                data-ministries="{{ strtolower(implode(' ', $volunteer['ministries'])) }}"
                data-id="{{ $volunteer['id'] }}">
              <td class="px-4 py-2 flex items-center gap-2 cursor-pointer" onclick="openProfile('{{ $volunteer['id'] }}')">
                <img src="{{ $volunteer['avatar'] }}" alt="{{ $volunteer['name'] }}" class="w-8 h-8 rounded-full">
                {{ $volunteer['name'] }}
              </td>
              <td class="px-4 py-2 hidden md:table-cell">{{ $volunteer['email'] }}</td>
              <td class="px-4 py-2 hidden lg:table-cell">{{ $volunteer['phone'] }}</td>
              <td class="px-4 py-2 hidden lg:table-cell">
                <div class="flex gap-1">
                  @foreach($volunteer['ministries'] as $ministry)
                    <span class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">{{ $ministry }}</span>
                  @endforeach
                </div>
              </td>
              <td class="px-4 py-2 hidden md:table-cell">
                <span class="px-2 py-1 text-xs rounded {{ $volunteer['status'] === 'active' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                  {{ ucfirst($volunteer['status']) }}
                </span>
              </td>
              <td class="px-4 py-2 text-right">
                <button class="text-blue-500 mr-2" onclick="openProfile('{{ $volunteer['id'] }}'); event.stopPropagation();">View</button>
                <button class="text-blue-500 mr-2">Edit</button>
                <button class="text-red-500">Delete</button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Pagination (static) -->
      <div class="flex justify-center space-x-2 mt-6">
        <button class="px-3 py-1 border rounded text-gray-500 hover:bg-gray-100">Previous</button>
        <button class="px-3 py-1 bg-blue-600 text-white rounded">1</button>
        <button class="px-3 py-1 border rounded text-gray-500 hover:bg-gray-100">2</button>
        <button class="px-3 py-1 border rounded text-gray-500 hover:bg-gray-100">3</button>
        <button class="px-3 py-1 border rounded text-gray-500 hover:bg-gray-100">Next</button>
      </div>
    </div>
  </div>

  <!-- Registration Modal -->
  <div id="registrationModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
    <div class="bg-white rounded-lg w-full max-w-lg p-6 relative">
      <button id="closeRegistration" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      <h2 class="text-xl font-bold mb-4">Volunteer Registration</h2>
      <!-- Registration Tabs -->
      <div id="registrationTabs" class="mb-4">
        <button class="reg-tab px-4 py-2 border-b-2 border-blue-600" data-tab="personal">Personal</button>
        <button class="reg-tab px-4 py-2 border-b-2 border-transparent" data-tab="skills">Skills</button>
        <button class="reg-tab px-4 py-2 border-b-2 border-transparent" data-tab="availability">Availability</button>
      </div>
      <div id="regTabContent">
        <div class="reg-content" id="tab-personal">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1" for="reg-name">Full Name</label>
            <input id="reg-name" type="text" placeholder="John Smith" class="w-full border rounded px-3 py-2">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1" for="reg-email">Email</label>
            <input id="reg-email" type="email" placeholder="john.smith@example.com" class="w-full border rounded px-3 py-2">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1" for="reg-phone">Phone Number</label>
            <input id="reg-phone" type="text" placeholder="(555) 123-4567" class="w-full border rounded px-3 py-2">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1" for="reg-address">Address</label>
            <textarea id="reg-address" placeholder="123 Main St, City, State, Zip" class="w-full border rounded px-3 py-2"></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1" for="reg-emergency">Emergency Contact</label>
            <input id="reg-emergency" type="text" placeholder="Name & Phone Number" class="w-full border rounded px-3 py-2">
          </div>
        </div>
        <div class="reg-content hidden" id="tab-skills">
          <div class="mb-4">
            <span class="block text-sm font-medium mb-1">Skills & Talents</span>
            <div class="grid grid-cols-2 gap-2">
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Music
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Teaching
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Leadership
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Administration
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Technical
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Hospitality
              </label>
            </div>
          </div>
          <div class="mb-4">
            <span class="block text-sm font-medium mb-1">Ministry Preferences</span>
            <div class="grid grid-cols-2 gap-2">
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Worship
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Children
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Youth
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Media
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Hospitality
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Prayer
              </label>
            </div>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1" for="reg-experience">Previous Experience</label>
            <textarea id="reg-experience" placeholder="Describe any relevant experience..." class="w-full border rounded px-3 py-2"></textarea>
          </div>
        </div>
        <div class="reg-content hidden" id="tab-availability">
          <div class="mb-4">
            <span class="block text-sm font-medium mb-1">Availability</span>
            <div class="grid grid-cols-2 gap-2">
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Sunday Morning
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Sunday Evening
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Monday Evening
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Tuesday Evening
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Wednesday Evening
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Thursday Evening
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Friday Evening
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2"> Saturday
              </label>
            </div>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1" for="reg-frequency">Preferred Frequency</label>
            <select id="reg-frequency" class="w-full border rounded px-3 py-2">
              <option value="">Select frequency</option>
              <option value="weekly">Weekly</option>
              <option value="biweekly">Bi-weekly</option>
              <option value="monthly">Monthly</option>
              <option value="asneeded">As needed</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1" for="reg-notes">Additional Notes</label>
            <textarea id="reg-notes" placeholder="Any additional information about your availability..." class="w-full border rounded px-3 py-2"></textarea>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-2">
        <button id="cancelRegistration" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Cancel</button>
        <button id="submitRegistration" class="px-4 py-2 bg-blue-600 text-white rounded">Register Volunteer</button>
      </div>
    </div>
  </div>

  <!-- Profile Modal -->
  <div id="profileModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
    <div class="bg-white rounded-lg w-full max-w-md p-6 relative">
      <button id="closeProfile" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      <div id="profileContent">
        <!-- Profile content populated via JS -->
      </div>
      <div class="flex justify-end gap-2 mt-4">
        <button id="editProfile" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 flex items-center">
          <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
          Edit Profile
        </button>
        <button id="scheduleVolunteer" class="px-4 py-2 bg-blue-600 text-white rounded flex items-center">
          Schedule Volunteer
        </button>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    // Toggle between grid and list views
    document.getElementById('gridViewBtn').addEventListener('click', function(){
      document.getElementById('gridView').style.display = 'grid';
      document.getElementById('listView').style.display = 'none';
    });
    document.getElementById('listViewBtn').addEventListener('click', function(){
      document.getElementById('gridView').style.display = 'none';
      document.getElementById('listView').style.display = 'block';
    });

    // Search filtering functionality for grid and list views
    document.getElementById('searchInput').addEventListener('input', function(){
      const query = this.value.toLowerCase();
      // Filter grid view cards
      document.querySelectorAll('.volunteer-card').forEach(card => {
        const name = card.getAttribute('data-name');
        const email = card.getAttribute('data-email');
        const skills = card.getAttribute('data-skills');
        const ministries = card.getAttribute('data-ministries');
        card.style.display = (name.includes(query) || email.includes(query) || skills.includes(query) || ministries.includes(query)) ? '' : 'none';
      });
      // Filter list view rows
      document.querySelectorAll('.volunteer-row').forEach(row => {
        const name = row.getAttribute('data-name');
        const email = row.getAttribute('data-email');
        const skills = row.getAttribute('data-skills');
        const ministries = row.getAttribute('data-ministries');
        row.style.display = (name.includes(query) || email.includes(query) || skills.includes(query) || ministries.includes(query)) ? '' : 'none';
      });
    });

    // Modal toggling for Registration
    document.getElementById('addVolunteerBtn').addEventListener('click', function(){
      document.getElementById('registrationModal').classList.remove('hidden');
      document.getElementById('registrationModal').classList.add('flex');
    });
    document.getElementById('closeRegistration').addEventListener('click', function(){
      document.getElementById('registrationModal').classList.add('hidden');
      document.getElementById('registrationModal').classList.remove('flex');
    });
    document.getElementById('cancelRegistration').addEventListener('click', function(){
      document.getElementById('registrationModal').classList.add('hidden');
      document.getElementById('registrationModal').classList.remove('flex');
    });
    document.getElementById('submitRegistration').addEventListener('click', function(){
      alert('Volunteer registered successfully!');
      document.getElementById('registrationModal').classList.add('hidden');
      document.getElementById('registrationModal').classList.remove('flex');
    });

    // Registration modal tabs
    document.querySelectorAll('.reg-tab').forEach(tab => {
      tab.addEventListener('click', function(){
        document.querySelectorAll('.reg-tab').forEach(t => {
          t.classList.remove('border-blue-600');
          t.classList.add('border-transparent');
        });
        this.classList.remove('border-transparent');
        this.classList.add('border-blue-600');
        const tabId = this.getAttribute('data-tab');
        document.querySelectorAll('.reg-content').forEach(content => {
          content.classList.add('hidden');
        });
        document.getElementById('tab-' + tabId).classList.remove('hidden');
      });
    });

    // Modal toggling for Profile
    function openProfile(id) {
      // Get volunteer data from inline PHP (passed as JSON)
      const volunteers = @json($volunteers);
      const volunteer = volunteers.find(v => v.id === id);
      if (volunteer) {
        // Populate the profile modal with volunteer data
        const profileHtml = `
          <div class="flex items-center gap-4">
            <img src="${volunteer.avatar}" alt="${volunteer.name}" class="w-16 h-16 rounded-full">
            <div>
              <h2 class="text-xl font-bold">${volunteer.name}</h2>
              <p class="text-sm text-gray-500">Joined on ${new Date(volunteer.joinDate).toLocaleDateString()}</p>
            </div>
          </div>
          <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm font-medium text-gray-500">Email</p>
              <p>${volunteer.email}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">Phone</p>
              <p>${volunteer.phone}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-500">Status</p>
              <p>${volunteer.status === 'active' ? 'Active' : 'Inactive'}</p>
            </div>
          </div>
          <div class="mt-4">
            <p class="text-sm font-medium text-gray-500">Ministries</p>
            <div class="flex flex-wrap gap-1">
              ${volunteer.ministries.map(m => `<span class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">${m}</span>`).join('')}
            </div>
          </div>
        `;
        document.getElementById('profileContent').innerHTML = profileHtml;
        document.getElementById('profileModal').classList.remove('hidden');
        document.getElementById('profileModal').classList.add('flex');
      }
    }
    document.getElementById('closeProfile').addEventListener('click', function(){
      document.getElementById('profileModal').classList.add('hidden');
      document.getElementById('profileModal').classList.remove('flex');
    });
    document.getElementById('scheduleVolunteer').addEventListener('click', function(){
      alert('Navigating to schedule volunteer page...');
      document.getElementById('profileModal').classList.add('hidden');
      document.getElementById('profileModal').classList.remove('flex');
    });
  </script>
</body>
</html>
