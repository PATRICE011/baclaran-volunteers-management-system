@extends('components.layout')

@section('title', 'Volunteers')

@section('styles')
<style>
    .modal-bg {
        background: rgba(0, 0, 0, 0.5);
    }

    .text-base {
        white-space: nowrap;
        overflow-x: auto;
        max-width: 100%;
    }

    .profile-tab.active-tab {
        border-bottom-color: #3b82f6 !important;
        color: #2563eb !important;
    }

    .profile-tab:hover {
        border-bottom-color: #d1d5db;
        color: #374151;
    }

    .tab-content {
        transition: opacity 0.2s ease-in-out;
    }


    .editable-input {
        padding-right: 2.5rem;
    }

    select::-ms-expand {
        display: none;
    }

    select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    #field-email_address-display {
        scrollbar-width: thin;
    }

    #archive-modal {
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    #archive-modal.active {
        opacity: 1;
        pointer-events: all;
    }

    #archive-modal .modal-container {
        transform: translateY(-20px);
        transition: transform 0.3s ease;
    }

    #archive-modal.active .modal-container {
        transform: translateY(0);
    }
</style>
@endsection

@section('content')
@include('components.navs')

<div class="min-h-screen pt-16 md:pl-64 bg-gradient-to-b from-white to-slate-50">
    <div class="flex-1 flex flex-col overflow-auto p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Volunteer Directory</h1>
            <button id="addVolunteerBtn"
                class="flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Volunteer
            </button>
        </div>

        <!-- Search & Controls -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <!-- Search Input -->
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                </div>
                <input id="searchInput" type="text" placeholder="Search volunteers by name, email, or ministry..."
                    class="pl-10 pr-3 py-2 border rounded w-full focus:outline-none focus:border-blue-500 h-12">
            </div>

            <!-- View Buttons (Grid & List) -->
            <div class="flex gap-2">
                <button id="gridViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 bg-white h-12"
                    data-view="grid">
                    <svg class="inline mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Grid
                </button>
                <button id="listViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 bg-white h-12"
                    data-view="list">
                    <svg class="inline mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    List
                </button>
            </div>

            <!-- Ministry Filter -->
            <div class="relative flex-grow">
                <select id="ministryFilter" class="pl-10 pr-3 py-2 border rounded w-full h-12">
                    <option value="">-- Select Ministry --</option>
                    @foreach ($ministries as $main)
                    <optgroup label="{{ $main->ministry_name }}">
                        @foreach ($main->children as $ministry)
                        <option value="{{ $ministry->id }}">{{ $ministry->ministry_name }}</option>

                        {{-- Render sub-groups --}}
                        @if ($ministry->children->count())
                        @foreach ($ministry->children as $sub)
                        <option value="{{ $sub->id }}">
                            &nbsp;&nbsp;&nbsp;→ {{ $sub->ministry_name }}
                        </option>
                        @endforeach
                        @endif
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>

            </div>

            <!-- Status Filter -->
            <div class="relative flex-grow">
                <select id="statusFilter" class="pl-10 pr-3 py-2 border rounded w-full h-12">
                    <option value="">-- Select Status --</option>
                    @foreach ($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                    @endforeach
                </select>
            </div>

        </div>

        <!-- Loading Overlay -->
        <div id="viewLoadingOverlay" class="fixed inset-0 bg-black bg-opacity-25 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Loading view...</span>
            </div>
        </div>


        <!-- Grid View Container -->
        <div id="gridView" class="space-y-8" style="display: {{ request('view') === 'list' ? 'none' : 'grid' }};">
            {{-- This will be dynamically replaced by AJAX --}}
        </div>

        <!-- List View -->
        <div id="listView" class="hidden">
            {{-- This will be dynamically replaced by AJAX --}}
        </div>
    </div>

    <div id="registrationModal" class="fixed inset-0 hidden items-center justify-center modal-bg z-50">
        <div class="bg-white rounded-xl w-full max-w-2xl p-8 relative max-h-[90vh] overflow-y-auto shadow-lg min-h-[500px]">
            <!-- Close Button -->
            <button id="closeRegistration" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Title -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Volunteer Registration</h2>

            <!-- Tabs -->
            <div id="registrationTabs" class="mb-6 flex gap-3 border-b pb-2">
                <button class="reg-tab text-sm font-medium px-4 py-2 border-b-2 border-blue-600 text-blue-600"
                    data-tab="personal" data-step="1">Basic Info</button>
                <button class="reg-tab text-sm font-medium px-4 py-2 border-b-2 border-transparent text-gray-500 tab-locked"
                    data-tab="sheet" data-step="2">Volunteer's Info</button>
            </div>

            <div id="regTabContent">
                <!-- Basic Info -->
                <div class="reg-content" id="tab-personal">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Volunteer ID -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Volunteer ID</label>
                            <input name="volunteer_id" type="text" placeholder="VOL-XXXXXX"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>

                        <!-- Profile Picture Upload Section -->
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <img id="profilePreview"
                                        class="h-20 w-20 rounded-full object-cover border-2 border-gray-300"
                                        src="data:image/svg+xml,%3csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100' height='100' fill='%23f3f4f6'/%3e%3ctext x='50%25' y='50%25' font-size='14' text-anchor='middle' alignment-baseline='middle' fill='%23374151'%3eNo Image%3c/text%3e%3c/svg%3e"
                                        alt="Profile preview">
                                </div>
                                <div class="flex-1">
                                    <input name="profile_picture" type="file" accept="image/*"
                                        id="profilePictureInput" class="hidden">
                                    <label for="profilePictureInput"
                                        class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Choose Photo
                                    </label>
                                    <button type="button" id="removeProfilePicture"
                                        class="ml-2 inline-flex items-center px-3 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 hidden">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Remove
                                    </button>
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Full Name -->
                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <input type="text" name="last_name" placeholder="Last Name" required
                                    class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <input type="text" name="first_name" placeholder="First Name" required
                                    class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <input type="text" name="middle_initial" placeholder="M.I."
                                    class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                            </div>
                        </div>

                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nickname</label>
                            <input name="nickname" type="text" placeholder="e.g. Chaz"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input name="dob" type="date"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                            <div class="flex gap-4 mt-1">
                                <label class="inline-flex items-center text-sm">
                                    <input type="radio" name="sex" value="male" required
                                        class="form-radio text-blue-600">
                                    <span class="ml-2">Male</span>
                                </label>
                                <label class="inline-flex items-center text-sm">
                                    <input type="radio" name="sex" value="female"
                                        class="form-radio text-pink-600">
                                    <span class="ml-2">Female</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" placeholder="123 Main St, City, Province"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <input name="phone" type="tel" placeholder="+63 912 345 6789"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input name="email" type="email" placeholder="you@example.com"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>

                        <div class="col-span-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                            <input name="occupation" type="text" placeholder="e.g. Software Developer"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['Single', 'Married', 'Widow/er', 'Separated', 'Others'] as $status)
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="civil_status" value="{{ $status }}" required
                                    class="form-radio mr-2">
                                {{ ucfirst($status) }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sacrament/s Received</label>
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            @foreach (['baptism', 'first_communion', 'confirmation', 'marriage'] as $sacrament)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="sacraments[]" value="{{ $sacrament }}"
                                    class="form-checkbox mr-2">
                                {{ ucwords(str_replace('_', ' ', $sacrament)) }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Volunteer's Info -->
                <div class="reg-content hidden space-y-6" id="tab-sheet">
                    <!-- Ministry Select -->
                    <div>
                        <label for="reg-ministry" class="block text-sm font-medium text-gray-700 mb-1">Ministry</label>
                        <select id="reg-ministry" name="ministry_id" class="pl-10 pr-3 py-2 border rounded w-full h-12">
                            <option value="">-- Select Ministry --</option>
                            @foreach ($ministries as $main)
                            <optgroup label="{{ $main->ministry_name }}">
                                @foreach ($main->children as $ministry)
                                <option value="{{ $ministry->id }}">{{ $ministry->ministry_name }}</option>

                                {{-- Render sub-groups --}}
                                @if ($ministry->children->count())
                                @foreach ($ministry->children as $sub)
                                <option value="{{ $sub->id }}">
                                    &nbsp;&nbsp;&nbsp;→ {{ $sub->ministry_name }}
                                </option>
                                @endforeach
                                @endif
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Applied & Duration -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="reg-applied-date" class="block text-sm font-medium text-gray-700 mb-1">Month & Year Started</label>
                            <input id="reg-applied-date" name="applied_date" type="month"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label for="reg-regular-duration" class="block text-sm font-medium text-gray-700 mb-1">Years as Regular Volunteer</label>
                            <input id="reg-regular-duration" name="regular_duration" type="text"
                                placeholder="e.g. 1 yr 6 mos" class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    <!-- Formation Received -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Formation Received</label>
                        <div class="space-y-3 mt-2">
                            <!-- BOS -->
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="formations[]" value="BOS" class="formation-checkbox">
                                <span class="text-sm w-48">Basic Orientation Seminar (BOS)</span>
                                <select name="bos_year" class="formation-year border rounded px-2 py-1 text-sm" disabled>
                                    <option value="">Select Year</option>
                                    @for ($y = date('Y'); $y >= 1980; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Diocesan Basic Formation -->
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="formations[]" value="Diocesan Basic Formation" class="formation-checkbox">
                                <span class="text-sm w-48">Diocesan Basic Formation</span>
                                <select name="diocesan_year" class="formation-year border rounded px-2 py-1 text-sm" disabled>
                                    <option value="">Select Year</option>
                                    @for ($y = date('Y'); $y >= 1980; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Safeguarding Policy -->
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="formations[]" value="Safeguarding Policy" class="formation-checkbox">
                                <span class="text-sm w-48">Safeguarding Policy</span>
                                <select name="safeguarding_year" class="formation-year border rounded px-2 py-1 text-sm" disabled>
                                    <option value="">Select Year</option>
                                    @for ($y = date('Y'); $y >= 1980; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Other Formation -->
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="other_formation_check" class="formation-checkbox">
                                <input type="text" id="other_formation_input" placeholder="Other Formation" class="w-48 border rounded px-2 py-1 text-sm" disabled>
                                <select id="other_formation_year" class="border rounded px-2 py-1 text-sm" disabled>
                                    <option value="">Select Year</option>
                                    @for ($y = date('Y'); $y >= 1980; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div>
                        <h3 class="font-semibold text-sm mb-2">Volunteer Timeline</h3>
                        <p class="text-xs text-gray-500 mb-2">Please indicate all Organization/Ministry you belong to in the Shrine</p>
                        <div id="timeline-container" class="space-y-3">
                            <div class="timeline-entry grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                                <input type="text" name="timeline_org[]" placeholder="Organization/Ministry"
                                    class="border rounded px-3 py-2 col-span-1">
                                <div class="flex gap-2 col-span-1">
                                    <select name="timeline_start_year[]"
                                        class="border rounded px-2 py-2 w-full year-select">
                                        <option value="">Start Year</option>
                                        @for ($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                    <span class="flex items-center text-sm">–</span>
                                    <select name="timeline_end_year[]"
                                        class="border rounded px-2 py-2 w-full year-select">
                                        <option value="">End Year</option>
                                        <option value="present">Present</option>
                                        @for ($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <input type="number" name="timeline_total[]" min="0"
                                    class="w-full border rounded px-3 py-2 total-years" placeholder="Total" readonly>
                            </div>
                        </div>
                        <button type="button" id="add-timeline" class="mt-2 px-3 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200">
                            + Add Another Timeline Entry
                        </button>
                    </div>

                    <!-- Other Affiliations -->
                    <div>
                        <h3 class="font-semibold text-sm mb-2">Other Affiliations</h3>
                        <p class="text-xs text-gray-500 mb-2">Please indicate any Organization/Ministry outside the Shrine</p>
                        <div id="affiliations-container" class="space-y-3">
                            <div class="affiliation-entry grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                                <input type="text" name="affil_org[]" placeholder="Organization/Ministry"
                                    class="border rounded px-3 py-2 col-span-1">
                                <div class="flex gap-2 col-span-1">
                                    <select name="affil_start_year[]"
                                        class="border rounded px-2 py-2 w-full year-select">
                                        <option value="">Start Year</option>
                                        @for ($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                    <span class="flex items-center text-sm">–</span>
                                    <select name="affil_end_year[]"
                                        class="border rounded px-2 py-2 w-full year-select">
                                        <option value="">End Year</option>
                                        <option value="present">Present</option>
                                        @for ($y = date('Y'); $y >= 1980; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <input type="number" name="affil_total[]" min="0"
                                    class="w-full border rounded px-3 py-2 total-years" placeholder="Total" readonly>
                            </div>
                        </div>
                        <button type="button" id="add-affiliation" class="mt-2 px-3 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200">
                            + Add Another Affiliation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-8 flex justify-end gap-3">
                <button id="cancelRegistration"
                    class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button id="nextToSheet"
                    class="px-4 py-2 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700 transition">
                    Next
                </button>
                <button id="submitRegistration"
                    class="hidden px-4 py-2 text-sm rounded-md bg-green-600 text-white hover:bg-green-700 transition">
                    Register Volunteer
                </button>
            </div>
        </div>
    </div>

</div>

<div id="profileModal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-40 z-50">
    <!-- Modal Box -->
    <div class="w-full mx-4 sm:mx-auto p-6 relative max-h-[90vh] overflow-y-auto rounded-lg bg-white shadow-lg" style="max-width: 44rem;">
        <!-- Close Button -->
        <button id="closeProfile" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Profile Content -->
        <div id="profileContent"></div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-2 mt-4">
            <button id="editProfile" class="px-4 py-2 bg-blue-600 text-white rounded flex items-center">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Edit Profile
            </button>

            <button id="saveChanges" class="px-4 py-2 bg-green-600 text-white rounded hidden">Save</button>
            <button id="cancelEdit" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hidden">Cancel</button>
        </div>
    </div>
</div>


</div>
<div id="archive-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="modal-container bg-white rounded-lg shadow-xl p-6 m-4 max-w-md w-full">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Archive Volunteer</h3>
            </div>
        </div>
        <div class="mb-4">
            <label for="archive-reason" class="block text-sm font-medium text-gray-700 mb-1">
                Reason for archiving
            </label>
            <textarea id="archive-reason" rows="3"
                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors"
                placeholder="Enter reason..."></textarea>
        </div>
        <div class="flex space-x-3 justify-end">
            <button id="cancel-archive" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Cancel
            </button>
            <button id="confirm-archive" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Archive
            </button>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<script src="{{ asset('assets/js/volunteer.js') }}"></script>
<script src="{{ asset('assets/js/add_volunteer.js') }}"></script>
<script src="{{ asset('assets/js/edit_volunteer.js') }}"></script>
<script src="{{ asset('assets/js/archive_volunteer.js') }}"></script>
{{-- Alpine.js CDN --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection