@extends('components.layout')

@section('title', 'Volunteers')

@section('styles')
<style>
  .modal-bg {
    background: rgba(0, 0, 0, 0.5);
  }
</style>
@endsection

@section('content')
@include('components.navs')

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
    <div class="relative flex-grow">
      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
        </svg>
      </div>
      <input id="searchInput" type="text" placeholder="Search volunteers by name, email, or ministry..."
        class="pl-10 pr-3 py-2 border rounded w-full focus:outline-none focus:border-blue-500">
    </div>
    <div class="flex gap-2">
      <button id="gridViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 bg-white"
        data-view="grid">
        <svg class="inline mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
        </svg>
        Grid
      </button>
      <button id="listViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 bg-white"
        data-view="list">
        <svg class="inline mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
        </svg>
        List
      </button>
      <button id="filterBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 flex items-center">
        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707v4.586a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1v-4.586a1 1 0 0 0-.293-.707L1.293 6.707A1 1 0 0 1 1 6V4z" />
        </svg>
        Filter
      </button>
    </div>

    <div id="viewLoadingOverlay"
      class="fixed inset-0 bg-black bg-opacity-25 hidden items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="text-gray-700">Loading view...</span>
      </div>
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

<!-- Registration Modal -->
<div id="registrationModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
  <div class="bg-white rounded-lg w-full max-w-lg p-6 relative max-h-[80vh] overflow-y-auto min-h-[500px]">
    <button id="closeRegistration"
      class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <h2 class="text-xl font-bold mb-4">Volunteer Registration</h2>

    <!-- Tabs -->
    <div id="registrationTabs" class="mb-4 flex gap-2">
      <button class="reg-tab px-4 py-2 border-b-2 border-blue-600" data-tab="personal" data-step="1">Basic
        Info</button>
      <button class="reg-tab px-4 py-2 border-b-2 border-transparent tab-locked" data-tab="sheet"
        data-step="2">Info Sheet</button>
    </div>

    <div id="regTabContent">
      <!-- Basic Info -->
      <div class="reg-content" id="tab-personal">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-nickname">Nickname</label>
          <input id="reg-nickname" name="nickname" type="text" placeholder="e.g. Chaz"
            class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-dob">Date of Birth</label>
          <input id="reg-dob" name="dob" type="date" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Sex</label>
          <div class="flex items-center space-x-6">
            <label class="inline-flex items-center">
              <input type="radio" name="sex" value="male"
                class="form-radio text-blue-600">
              <span class="ml-2">Male</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="sex" value="female"
                class="form-radio text-pink-600">
              <span class="ml-2">Female</span>
            </label>
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-address">Address</label>
          <textarea id="reg-address" name="address" placeholder="123 Main St, City, Province"
            class="w-full border rounded px-3 py-2"></textarea>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-phone">Mobile Number</label>
          <input id="reg-phone" name="phone" type="tel" placeholder="+63 912 345 6789"
            class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-email">Email Address</label>
          <input id="reg-email" name="email" type="email" placeholder="you@example.com"
            class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-occupation">Occupation</label>
          <input id="reg-occupation" name="occupation" type="text"
            placeholder="e.g. Software Developer" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Civil Status</label>
          <div class="grid grid-cols-2 gap-2">
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="single" class="form-radio mr-2">
              Single
            </label>
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="married" class="form-radio mr-2">
              Married
            </label>
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="widower" class="form-radio mr-2">
              Widow(er)
            </label>
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="separated" class="form-radio mr-2">
              Separated
            </label>
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="others" class="form-radio mr-2"
                id="civilOthers"> Others
            </label>
          </div>

          <!-- Custom input for others -->
          <div id="civilOtherInput" class="mt-2 hidden">
            <input type="text" name="civil_status_other" placeholder="Specify other civil status"
              class="w-full border rounded px-3 py-2">
          </div>
        </div>

        <div class="mb-4">
          <span class="block text-sm font-medium mb-1">Sacrament/s Received</span>
          <div class="grid grid-cols-3 gap-2">
            <label class="flex items-center"><input type="checkbox" name="sacraments[]"
                value="baptism" class="form-checkbox mr-2"> Baptism</label>
            <label class="flex items-center"><input type="checkbox" name="sacraments[]"
                value="first_communion" class="form-checkbox mr-2"> First Communion</label>
            <label class="flex items-center"><input type="checkbox" name="sacraments[]"
                value="confirmation" class="form-checkbox mr-2"> Confirmation</label>
          </div>
        </div>
        <div class="mb-4">
          <span class="block text-sm font-medium mb-1">Formations Received</span>
          <div class="grid grid-cols-1 gap-2">
            <label class="flex items-center"><input type="checkbox" name="formations[]"
                value="BOS" class="form-checkbox mr-2"> Basic Orientation Seminar (BOS)</label>
            <label class="flex items-center"><input type="checkbox" name="formations[]"
                value="BFF" class="form-checkbox mr-2"> Basic Faith Formation (BFF)</label>
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-others">Others</label>
          <input id="reg-others" name="others" type="text" placeholder="Anything else?"
            class="w-full border rounded px-3 py-2">
        </div>
      </div>

      <!-- Info Sheet -->
      <div class="reg-content hidden" id="tab-sheet">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-ministry">Ministry</label>
          <select name="ministry_id" id="reg-ministry"
            class="w-full border rounded px-3 py-2">
            <option value="">-- Select Ministry --</option>
            @foreach ($ministries as $ministry)
            <optgroup label="{{ $ministry->ministry_name }}">
              @foreach ($ministry->children as $sub)
              <option value="{{ $sub->id }}">{{ $sub->ministry_name }}
              </option>
              @endforeach
            </optgroup>
            @endforeach
          </select>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1" for="reg-applied-date">Month
              &amp; Year
              Applied</label>
            <input id="reg-applied-date" name="applied_date" type="month"
              class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" for="reg-regular-duration">No.
              of Years as
              Regular Volunteer</label>
            <input id="reg-regular-duration" name="regular_duration" type="text"
              placeholder="e.g. 1 yr 6 mos" class="w-full border rounded px-3 py-2">
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Name</label>
          <div class="grid grid-cols-3 gap-2">
            <input type="text" name="last_name" placeholder="Surname"
              class="border rounded px-2 py-1">
            <input type="text" name="first_name" placeholder="First Name"
              class="border rounded px-2 py-1">
            <input type="text" name="middle_initial" placeholder="M.I."
              class="border rounded px-2 py-1">
          </div>
        </div>

        <div class="mb-4">
          <h3 class="font-semibold mb-2">Volunteer Timeline</h3>
          <p class="text-sm text-gray-500 mb-2">Please indicate all Organization/Ministry you
            belong to
            in the Shrine</p>
          <table class="w-full table-auto border-collapse mb-4">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1">Organization/Ministry</th>
                <th class="border px-2 py-1">Year Started–Year Ended</th>
                <th class="border px-2 py-1">Total Years</th>
                <th class="border px-2 py-1">Active? Y/N</th>
              </tr>
            </thead>
            <tbody>
              @for ($i = 0; $i < 3; $i++)
                <tr>
                <td class="border p-1">
                  <input type="text" name="timeline_org[]"
                    class="w-full border rounded px-2 py-1">
                </td>
                <td class="border p-1 flex gap-1 items-center">
                  <select name="timeline_start_year[]"
                    class="border rounded px-2 py-1 year-select"
                    data-row="{{ $i }}">
                    @for ($y = date('Y'); $y >= 1980; $y--)
                    <option value="{{ $y }}">{{ $y }}
                    </option>
                    @endfor
                  </select>
                  <span>–</span>
                  <select name="timeline_end_year[]"
                    class="border rounded px-2 py-1 year-select"
                    data-row="{{ $i }}">
                    @for ($y = date('Y'); $y >= 1980; $y--)
                    <option value="{{ $y }}">
                      {{ $y }}
                    </option>
                    @endfor
                  </select>
                </td>
                <td class="border p-1">
                  <input type="text" name="timeline_total[]"
                    class="w-full border rounded px-2 py-1 total-years"
                    readonly>
                </td>
                <td class="border p-1 text-center">
                  <select name="timeline_active[]"
                    class="border rounded px-2 py-1 w-full">
                    <option value="">–</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </td>
                </tr>
                @endfor
            </tbody>
          </table>
        </div>

        <div class="mb-4">
          <h3 class="font-semibold mb-2">Other Affiliations</h3>
          <p class="text-sm text-gray-500 mb-2">Please indicate any Organization/Ministry you
            belong to
            outside the Shrine</p>
          <table class="w-full table-auto border-collapse mb-4">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1">Organization/Ministry</th>
                <th class="border px-2 py-1">Year Started–Year Ended</th>
                <th class="border px-2 py-1">Active? Y/N</th>
              </tr>
            </thead>
            <tbody>
              @for ($i = 0; $i < 3; $i++)
                <tr>
                <td class="border p-1">
                  <input type="text" name="affil_org[]"
                    class="w-full border rounded px-2 py-1">
                </td>
                <td class="border p-1 flex gap-1 items-center">
                  <select name="affil_start_year[]"
                    class="border rounded px-2 py-1 year-select"
                    data-row="{{ $i }}">
                    @for ($y = date('Y'); $y >= 1980; $y--)
                    <option value="{{ $y }}">
                      {{ $y }}
                    </option>
                    @endfor
                  </select>
                  <span>–</span>
                  <select name="affil_end_year[]"
                    class="border rounded px-2 py-1 year-select"
                    data-row="{{ $i }}">
                    @for ($y = date('Y'); $y >= 1980; $y--)
                    <option value="{{ $y }}">
                      {{ $y }}
                    </option>
                    @endfor
                  </select>
                </td>
                <td class="border p-1 text-center">
                  <select name="affil_active[]"
                    class="border rounded px-2 py-1 w-full">
                    <option value="">–</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                  </select>
                </td>
                </tr>
                @endfor
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Footer buttons -->
    <div class="mt-4 flex justify-end gap-2">
      <button id="cancelRegistration"
        class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Cancel</button>
      <button id="nextToSheet" class="px-4 py-2 bg-blue-600 text-white rounded">Next</button>
      <button id="submitRegistration"
        class="px-4 py-2 bg-green-600 text-white rounded hidden">Register
        Volunteer</button>
    </div>
  </div>
</div>

<!-- Profile Modal -->
<div id="profileModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
  <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
    <button id="closeProfile"
      class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
    <div id="profileContent"></div>
    <div class="flex justify-end gap-2 mt-4">
      <button id="editProfile"
        class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 flex items-center">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
        </svg>
        Edit Profile
      </button>
      <button id="scheduleVolunteer"
        class="px-4 py-2 bg-blue-600 text-white rounded flex items-center">
        Schedule Volunteer
      </button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/volunteer.js') }}"></script>
{{-- Alpine.js CDN --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection