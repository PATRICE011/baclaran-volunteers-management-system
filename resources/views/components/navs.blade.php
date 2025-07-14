<!-- Modern Sidebar Navigation - Cohesive with Dashboard -->
<div class="hidden md:flex w-64 flex-col fixed inset-y-0 z-50 bg-gradient-to-b from-slate-50 to-blue-50/30 border-r border-slate-200/50 backdrop-blur-xl">
    <!-- Sidebar Header -->
    <div class="flex h-16 items-center border-b border-slate-200/50 px-6">
        <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 text-xl font-bold text-slate-800 hover:text-blue-600 transition-colors duration-200">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                <i class="bx bx-church text-white text-lg"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold text-slate-800">Baclaran Church</span>
                <span class="text-sm font-medium text-slate-600 -mt-1">Management System</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 overflow-auto py-6">
        <nav class="grid gap-1 px-4">
            @php
            function navActive($path)
            {
            return request()->is($path)
            ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/20'
            : 'text-slate-600 hover:bg-white/60 hover:text-slate-800 hover:shadow-sm';
            }
            @endphp

            <!-- Dashboard Link -->
            <a href="{{ url('/dashboard') }}" class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ navActive('dashboard') }}">
                <i class="bx bx-home text-lg"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Volunteers Link -->
            <a href="{{ url('/volunteers') }}" class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ navActive('volunteers') }}">
                <i class="bx bx-group text-lg"></i>
                <span class="font-medium">Volunteers</span>
            </a>

            <!-- Ministries Link -->
            <a href="{{ url('/ministries') }}" class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ navActive('ministries') }}">
                <i class="bx bx-church text-lg"></i>
                <span class="font-medium">Ministries</span>
            </a>

            <!-- Tasks Link -->
            <a href="{{ url('/events') }}" class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ navActive('events') }}">
                <i class="bx bx-calendar text-lg"></i>
                <span class="font-medium">Events</span>
            </a>
            <a href="{{ url('/tasks') }}" class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ navActive('tasks') }}">
                <i class="bx bx-list-check text-lg"></i>
                <span class="font-medium">Tasks</span>
            </a>

            <!-- Schedule Link -->
            <!-- <a href="{{ url('/schedule') }}" class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ navActive('schedule') }}">
                <i class="bx bx-calendar text-lg"></i>
                <span class="font-medium">Schedule</span>
            </a> -->

            <!-- Attendance Link -->
            <!-- <a href="{{ url('/attendance') }}" class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ navActive('attendance') }}">
                <i class="bx bx-check-circle text-lg"></i>
                <span class="font-medium">Track Attendance</span>
            </a> -->
        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="border-t border-slate-200/50 p-4">
        <div class="flex items-center gap-3 text-slate-500 text-sm">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span>System Online</span>
        </div>
    </div>
</div>

<!-- Modern Header - Consistent with Dashboard -->
<header class="sticky top-0 z-40 flex h-16 items-center gap-4 bg-white/95 backdrop-blur-xl border-b border-slate-200/50 px-6 shadow-sm" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-slate-600">
        <a href="{{ url('/dashboard') }}" class="hover:text-blue-600 transition-colors duration-200">Dashboard</a>
        <i class="bx bx-chevron-right text-slate-400"></i>
        <span class="font-medium text-slate-800">Settings</span>
    </div>

    <div class="flex flex-1 items-center justify-end">
        <div class="relative flex items-center gap-4">
            @php
            // Set the avatar seed using the user's first and last name
            $avatarSeed = strtolower($user->first_name . ' ' . $user->last_name);
            @endphp

            <!-- User Profile Button -->
            <button type="button" class="group inline-flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all duration-200" @click="userMenuOpen = !userMenuOpen" aria-haspopup="true" :aria-expanded="userMenuOpen">

                <!-- Avatar -->
                <div class="relative">
                    <span class="relative flex h-8 w-8 overflow-hidden rounded-full bg-gradient-to-br from-blue-400 to-blue-600 ring-1 ring-slate-200">
                        @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->first_name }}" class="h-full w-full object-cover">
                        @else
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $avatarSeed }}" alt="{{ $user->first_name }}" class="h-full w-full object-cover">
                        @endif
                    </span>
                    <!-- Online Status Indicator -->
                    <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border border-white rounded-full"></div>
                </div>

                <!-- User Info -->
                <div class="flex flex-col items-start">
                    <span class="text-sm font-semibold text-slate-800">{{ $user->first_name }} {{ $user->last_name }}</span>
                    <span class="text-xs text-slate-500">
                        @if($user->role == 'admin')
                        Administrator
                        @elseif($user->role == 'staff')
                        Authorized Member
                        @else
                        {{ $user->role }}
                        @endif
                    </span>
                </div>

                <!-- Chevron Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 group-hover:text-slate-600 transition-all duration-200" :class="{ 'rotate-180': userMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                </svg>
            </button>

            <!-- Clean Dropdown Panel -->
            <div x-show="userMenuOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute right-0 mt-3 w-56 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50"
                style="display: none; top: 100%;">

                <!-- Menu Items -->
                <div class="py-2">
                    <a href="{{ url('/settings') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors duration-200">
                        <i class="bx bx-cog text-slate-500"></i>
                        <span>Account Settings</span>
                    </a>

                    @if($user->isAdmin())
                    <a href="{{ url('/settings/role') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors duration-200">
                        <i class="bx bx-shield text-slate-500"></i>
                        <span>Role Management</span>
                    </a>
                    @endif

                    <a href="{{ url('/settings/archives') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors duration-200">
                        <i class="bx bx-archive text-slate-500"></i>
                        <span>Archives</span>
                    </a>
                </div>

                <!-- Divider -->
                <div class="border-t border-slate-200"></div>

                <!-- Sign Out -->
                <div class="py-2">
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                            <i class="bx bx-log-out text-red-500"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Custom CSS for seamless integration -->
<style>
    /* Ensure the main content area has proper background */
    .main-content {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    /* Custom scrollbar that matches the design */
    .sidebar-nav::-webkit-scrollbar {
        width: 3px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: rgba(148, 163, 184, 0.1);
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 2px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.5);
    }
</style>