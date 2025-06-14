<!-- Sidebar Navigation -->
<div class="hidden md:flex w-64 flex-col fixed inset-y-0 z-50 bg-white border-r">
    <!-- Sidebar Header -->
    <div class="flex h-14 items-center border-b px-4">
        <a href="{{ url('/dashboard') }}" class="text-lg font-semibold text-gray-700 hover:text-blue-500">
            Baclaran Church VMS
        </a>
    </div>


    <div class="flex-1 overflow-auto py-2">
        <nav class="grid gap-1 px-2">
            @php
            function navActive($path)
            {
            return request()->is($path) ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-500 hover:text-white';
            }
            @endphp

            <!-- Dashboard Link -->
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('dashboard') }}">
                <!-- Dashboard Icon (Boxicon) -->
                <i class="bx bx-home h-5 w-5"></i>
                <span>Dashboard</span>
            </a>

            <!-- Volunteers Link -->
            <a href="{{ url('/volunteers') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('volunteers') }}">
                <!-- Users Icon (Boxicon) -->
                <i class="bx bx-group h-5 w-5"></i>
                <span>Volunteers</span>
            </a>

            <!-- Ministries Link -->
            <a href="{{ url('/ministries') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('ministries') }}">
                <!-- Ministry Icon (Boxicon) -->
                <i class="bx bx-church h-5 w-5"></i>
                <span>Ministries</span>
            </a>

            <!-- Schedule Link -->
            <a href="{{ url('/schedule') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('schedule') }}">
                <!-- Calendar Icon (Boxicon) -->
                <i class="bx bx-calendar h-5 w-5"></i>
                <span>Schedule</span>
            </a>

            <!-- Attendance Link -->
            <a href="{{ url('/attendance') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('attendance') }}">
                <!-- Attendance Icon (Boxicon) -->
                <i class="bx bx-check-circle h-5 w-5"></i>
                <span>Attendance</span>
            </a>

            <!-- Tasks Link -->
            <a href="{{ url('/tasks') }}" class="flex items-center gap-3 rounded-md px-3 py-2 {{ navActive('tasks') }}">
                <!-- Tasks Icon (Boxicon) -->
                <i class="bx bx-list-check h-5 w-5"></i>
                <span>Tasks</span>
            </a>
        </nav>
    </div>
</div>

<!-- Header -->
<!-- Header -->
<header class="sticky top-0 z-40 flex h-14 items-center gap-4 border-b bg-white px-4 sm:px-6" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
    <div class="flex flex-1 items-center justify-end">
        <div class="relative flex items-center gap-4">
            {{-- Avatar + Name + Chevron --}}
            @php
                // Set the avatar seed using the user's first and last name
                $avatarSeed = strtolower($user->first_name . ' ' . $user->last_name);
            @endphp
            <button type="button" class="inline-flex items-center gap-2 px-2 py-1 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400" @click="userMenuOpen = !userMenuOpen" aria-haspopup="true" :aria-expanded="userMenuOpen">
                <span class="relative flex h-8 w-8 overflow-hidden rounded-full bg-gray-200">
                    @if($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->first_name }}" class="h-full w-full object-cover">
                    @else
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $avatarSeed }}" alt="{{ $user->first_name }}" class="h-full w-full object-cover">
                    @endif
                </span>
                <span>{{ $user->first_name }} {{ $user->last_name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                </svg>
            </button>

            {{-- Dropdown Panel --}}
            <div x-show="userMenuOpen" x-transition.origin.top.right class="absolute right-0 mt-60 w-48 bg-white border border-gray-200 rounded-md shadow-lg overflow-hidden z-50" style="display: none;">
                <div class="py-1 divide-y divide-gray-100">
                    <a href="{{ url('/settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
                    <a href="{{ url('/role') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Role Management</a>
                    <a href="{{ url('/archives') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Archives</a>
                </div>
                <div class="py-1">
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Sign Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
