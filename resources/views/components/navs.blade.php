<div x-data="{ mobileMenuOpen: false, userMenuOpen: false }"
    @keydown.window.escape="mobileMenuOpen = false; userMenuOpen = false">
    <!-- Mobile sidebar overlay -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/50 md:hidden" style="display: none;"
        @click="mobileMenuOpen = false">
    </div>

    <!-- Mobile sidebar -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-200 transform"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-200 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-white/95 from-slate-50 to-blue-50/30 border-r border-slate-200/50 backdrop-blur-xl md:hidden"
        style="display: none;">

        <!-- Mobile sidebar header with close button -->
        <div class="flex h-14 sm:h-16 items-center border-b border-slate-200/50 px-3 sm:px-6">
            <a href="{{ url('/dashboard') }}"
                class="flex items-center gap-2 sm:gap-3 hover:text-blue-600 transition-colors duration-200 min-w-0 flex-1">
                <!-- Logo Icon - Responsive sizing -->
                <div
                    class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
                    <i class="bx bx-church text-white text-base sm:text-lg"></i>
                </div>
                <!-- Text Content - Mobile optimized -->
                <div class="flex flex-col min-w-0 flex-1">
                    <span class="text-base sm:text-lg font-bold text-slate-800 truncate">Baclaran Church</span>
                    <span class="text-xs font-medium text-slate-500 -mt-1 truncate hidden xs:block sm:block">Management
                        System</span>
                </div>
            </a>
        </div>

        <!-- Mobile navigation menu -->
        <div class="flex-1 overflow-auto py-6">
            @php
                function navActive($path)
                {
                    return request()->is($path)
                        ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/20'
                        : 'text-slate-600 hover:bg-white/60 hover:text-slate-800 hover:shadow-sm';
                }
            @endphp
            <nav class="grid gap-1 px-4">
                <!-- Dashboard Link -->
                <a href="{{ url('/dashboard') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 text-slate-600 hover:bg-white/60 hover:text-slate-800 hover:shadow-sm {{ navActive('dashboard') }}">
                    <i class="bx bx-home text-lg"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Volunteers Link -->
                <a href="{{ url('/volunteers') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 text-slate-600 hover:bg-white/60 hover:text-slate-800 hover:shadow-sm {{ navActive('volunteers') }}">
                    <i class="bx bx-group text-lg"></i>
                    <span class="font-medium">Volunteers</span>
                </a>

                <!-- Ministries Link -->
                <a href="{{ url('/ministries') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 text-slate-600 hover:bg-white/60 hover:text-slate-800 hover:shadow-sm {{ navActive('ministries') }}">
                    <i class="bx bx-church text-lg"></i>
                    <span class="font-medium">Ministries</span>
                </a>

                <!-- Events Link -->
                <a href="{{ url('/events') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 text-slate-600 hover:bg-white/60 hover:text-slate-800 hover:shadow-sm {{ navActive('events') }}">
                    <i class="bx bx-calendar text-lg"></i>
                    <span class="font-medium">Events</span>
                </a>

                <!-- Tasks Link -->
                <a href="{{ url('/tasks') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 text-slate-600 hover:bg-white/60 hover:text-slate-800 hover:shadow-sm {{ navActive('tasks') }}">
                    <i class="bx bx-list-check text-lg"></i>
                    <span class="font-medium">Tasks</span>
                </a>
            </nav>
        </div>

        <!-- Mobile sidebar footer -->
        <div class="border-t border-slate-200/50 p-4">
            <div class="flex items-center gap-3 text-slate-500 text-sm">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span>System Online</span>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar Navigation -->
    <div
        class="hidden md:flex w-64 flex-col fixed inset-y-0 z-30 bg-white/95 from-slate-50 to-blue-50/30 border-r border-slate-200/50 backdrop-blur-xl">
        <!-- Fixed Desktop Sidebar Header -->
        <div class="flex h-16 items-center border-b border-slate-200/50 px-6">
            <a href="#" class="flex items-center gap-3 hover:text-blue-600 transition-colors duration-200">
                <!-- Logo Icon - Fixed positioning and sizing -->
                <div
                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
                    <i class="bx bx-church text-white text-lg"></i>
                </div>
                <!-- Text Content - Fixed layout -->
                <div class="flex flex-col min-w-0">
                    <span class="text-lg font-bold text-slate-800 truncate">Baclaran Church</span>
                    <span class="text-xs font-medium text-slate-500 -mt-1 truncate">Management System</span>
                </div>
            </a>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-auto py-6">
            <nav class="grid gap-1 px-4">
                @php
                    function dnavActive($path)
                    {
                        return request()->is($path)
                            ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/20'
                            : 'text-slate-600 hover:bg-white/60 hover:text-slate-800 hover:shadow-sm';
                    }
                @endphp
                <!-- Dashboard Link -->
                <a href="{{ url('/dashboard') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ dnavActive('dashboard') }}">
                    <i class="bx bx-home text-lg"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Volunteers Link -->
                <a href="{{ url('/volunteers') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ dnavActive('volunteers') }}">
                    <i class="bx bx-group text-lg"></i>
                    <span class="font-medium">Volunteers</span>
                </a>

                <!-- Ministries Link -->
                <a href="{{ url('/ministries') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ dnavActive('ministries') }}">
                    <i class="bx bx-church text-lg"></i>
                    <span class="font-medium">Ministries</span>
                </a>

                <!-- event Link -->
                <a href="{{ url('/events') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ dnavActive('events') }}">
                    <i class="bx bx-calendar text-lg"></i>
                    <span class="font-medium">Events</span>
                </a>
                <!-- Tasks Link -->
                <a href="{{ url('/tasks') }}"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200 {{ dnavActive('tasks') }}">
                    <i class="bx bx-list-check text-lg"></i>
                    <span class="font-medium">Tasks</span>
                </a>
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
    @php
        $avatarSeed = $user->email;
    @endphp
    <!-- Header - Now positioned to not overlap with sidebar -->
    <header
        class="sticky top-0 z-40 flex h-16 items-center gap-4 bg-white/95 backdrop-blur-xl border-b border-slate-200/50 px-6 shadow-sm md:ml-64">
        <!-- Mobile menu button -->
        <button @click="mobileMenuOpen = true"
            class="md:hidden p-2 rounded-md text-slate-600 hover:text-blue-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Simplified Breadcrumb - Shows only current page -->
        <!-- Dynamic Breadcrumb - Shows current page based on active navigation -->
        <div class="flex items-center gap-2 text-sm text-slate-600">
            @php
                $currentPath = request()->path();
                $pageTitle = 'Dashboard'; // Default value

                // Map paths to page titles
                if (request()->is('dashboard')) {
                    $pageTitle = 'Dashboard';
                } elseif (request()->is('volunteers')) {
                    $pageTitle = 'Volunteers';
                } elseif (request()->is('ministries')) {
                    $pageTitle = 'Ministries';
                } elseif (request()->is('events')) {
                    $pageTitle = 'Events';
                } elseif (request()->is('tasks')) {
                    $pageTitle = 'Tasks';
                } elseif (request()->is('settings')) {
                    $pageTitle = 'Settings';
                } elseif (request()->is('settings/role')) {
                    $pageTitle = 'Role Management';
                } elseif (request()->is('settings/archives')) {
                    $pageTitle = 'Archives';
                } else {
                    // For any other paths, use the first segment capitalized
                    $segments = explode('/', $currentPath);
                    $pageTitle = ucfirst($segments[0] ?? 'Dashboard');
                }
            @endphp

            <span class="font-medium text-slate-800">{{ $pageTitle }}</span>
        </div>

        <div class="flex flex-1 items-center justify-end">
            <div class="relative flex items-center gap-4">
                <!-- User Profile Button -->
                <button type="button"
                    class="group inline-flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                    @click="userMenuOpen = !userMenuOpen" aria-haspopup="true" :aria-expanded="userMenuOpen">

                    <!-- Avatar & User Info Container -->
                    <div class="flex items-center gap-3">
                        <!-- Avatar -->
                        <div class="relative">
                            <span
                                class="relative flex h-8 w-8 sm:h-9 sm:w-9 overflow-hidden rounded-full bg-gradient-to-br from-blue-400 to-blue-600 ring-1 ring-slate-200">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->email }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $avatarSeed }}"
                                        alt="{{ $user->email }}" class="h-full w-full object-cover">
                                @endif
                            </span>
                            <!-- Online Status Indicator -->
                            <div
                                class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border border-white rounded-full">
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex flex-col items-start">
                            <!-- Email - Smaller -->
                            <span class="text-xs sm:text-sm font-semibold text-slate-800">{{ $user->email }}</span>

                            <!-- Role - Tiny -->
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

                    </div>

                    <!-- Chevron Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-slate-400 group-hover:text-slate-600 transition-all duration-200"
                        :class="{ 'rotate-180': userMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                    </svg>
                </button>

                <!-- Clean Dropdown Panel -->
                <div x-show="userMenuOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="absolute right-0 mt-3 w-56 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50"
                    style="display: none; top: 100%;">

                    <!-- Menu Items -->
                    <div class="py-2">
                        <a href="{{ url('/settings') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors duration-200">
                            <i class="bx bx-cog text-slate-500"></i>
                            <span>Account Settings</span>
                        </a>

                        <a href="{{ url('/settings/role') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors duration-200">
                            <i class="bx bx-shield text-slate-500"></i>
                            <span>Role Management</span>
                        </a>

                        <a href="{{ url('/settings/archives') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors duration-200">
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
                            <button type="submit"
                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
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

        /* Ensure BoxIcons are properly loaded and displayed */
        .bx {
            font-family: boxicons !important;
            font-weight: normal;
            font-style: normal;
            line-height: 1;
            display: inline-block;
        }

        /* Fix for logo icon positioning */
        .bx-church {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
    </style>
</div>