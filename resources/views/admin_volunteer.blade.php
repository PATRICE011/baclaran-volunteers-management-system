@extends('components.layout')
@section('title','Volunteers')
@section('styles')
<style>
  /* Modal background style */
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
    <button id="addVolunteerBtn" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none">
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
      <button id="gridViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 bg-blue-50 border-blue-200">Grid</button>
      <button id="listViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">List</button>
      <button id="filterBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 flex items-center">
        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707v4.586a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1v-4.586a1 1 0 0 0-.293-.707L1.293 6.707A1 1 0 0 1 1 6V4z" />
        </svg>
        Filter
      </button>
    </div>
  </div>

  <!-- Grid View -->
  <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($volunteers as $volunteer)
    <div class="bg-white shadow rounded overflow-hidden cursor-pointer volunteer-card"
      data-name="{{ strtolower($volunteer->nickname ?? $volunteer->detail?->full_name ?? 'No Name') }}"
      data-email="{{ strtolower($volunteer->email_address ?? '') }}"
      data-ministry="{{ strtolower($volunteer->ministry_name) }}"
      data-id="{{ $volunteer->id }}"
      onclick="openProfile('{{ $volunteer->id }}')">
      <div class="p-4 flex flex-col items-center">
        <!-- Generate avatar based on name -->
        @php
        $displayName = $volunteer->nickname ?? $volunteer->detail?->full_name ?? 'No Name';
        $avatarSeed = str_replace(' ', '', strtolower($displayName));
        @endphp
        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $avatarSeed }}"
          alt="{{ $displayName }}" class="w-20 h-20 rounded-full mb-4">

        <h3 class="font-semibold text-lg text-center">{{ $displayName }}</h3>
        <p class="text-sm text-gray-500 mb-2 text-center">{{ $volunteer->email_address ?? 'No email' }}</p>

        <!-- Ministry Display -->
        <div class="flex flex-wrap gap-1 justify-center mt-2">
          @if($volunteer->detail && $volunteer->detail->ministry)
          <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-200">
            {{ $volunteer->detail->ministry->ministry_name }}
          </span>
          @else
          <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-50 text-gray-500 border border-gray-200">
            No Ministry Assigned
          </span>
          @endif
        </div>

        <!-- Status (based on profile completion) -->
        <span class="mt-3 inline-block px-2 py-1 text-xs rounded {{ $volunteer->hasCompleteProfile() ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200' }}">
          {{ $volunteer->detail->volunteer_status }}
        </span>
      </div>
    </div>
    @empty
    <div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-500">
      <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
      </svg>
      <p class="text-lg font-medium">No volunteers found</p>
      <p class="text-sm">Start by adding your first volunteer to the system.</p>
    </div>
    @endforelse
  </div>

  <!-- List View -->
  <div id="listView" class="hidden">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Phone</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Ministry</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($volunteers as $volunteer)
          <tr class="hover:bg-gray-50 volunteer-row"
            data-name="{{ strtolower($volunteer->nickname ?? $volunteer->detail?->full_name ?? 'No Name') }}"
            data-email="{{ strtolower($volunteer->email_address ?? '') }}"
            data-ministry="{{ strtolower($volunteer->ministry_name) }}"
            data-id="{{ $volunteer->id }}">

            <td class="px-6 py-4 whitespace-nowrap cursor-pointer" onclick="openProfile('{{ $volunteer->id }}')">
              <div class="flex items-center">
                @php
                $displayName = $volunteer->nickname ?? $volunteer->detail?->full_name ?? 'No Name';
                $avatarSeed = str_replace(' ', '', strtolower($displayName));
                @endphp
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $avatarSeed }}"
                  alt="{{ $displayName }}" class="w-10 h-10 rounded-full mr-3">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ $displayName }}</div>
                  @if($volunteer->occupation)
                  <div class="text-sm text-gray-500">{{ $volunteer->occupation }}</div>
                  @endif
                </div>
              </div>
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
              {{ $volunteer->email_address ?? 'No email' }}
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
              {{ $volunteer->mobile_number ?? 'No phone' }}
            </td>

            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
              @if($volunteer->detail && $volunteer->detail->ministry)
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $volunteer->detail->ministry->ministry_name }}
              </span>
              @else
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                No Ministry Assigned
              </span>
              @endif
            </td>

            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $volunteer->hasCompleteProfile() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{  $volunteer->detail->volunteer_status  }}
              </span>
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="openProfile('{{ $volunteer->id }}'); event.stopPropagation();">
                View
              </button>
              <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="editVolunteer('{{ $volunteer->id }}'); event.stopPropagation();">
                Edit
              </button>
              <button class="text-red-600 hover:text-red-900" onclick="deleteVolunteer('{{ $volunteer->id }}'); event.stopPropagation();">
                Delete
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
              <div class="flex flex-col items-center">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-lg font-medium">No volunteers found</p>
                <p class="text-sm">Start by adding your first volunteer to the system.</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  @if($volunteers->hasPages())
  <div class="flex justify-center mt-6">
    {{ $volunteers->links() }}
  </div>
  @endif
</div>

<!-- Registration Modal -->
<div id="registrationModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
  <div class="bg-white rounded-lg w-full max-w-lg p-6 relative max-h-[80vh] overflow-y-auto">
    <button id="closeRegistration" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <h2 class="text-xl font-bold mb-4">Volunteer Registration</h2>

    <!-- Tabs -->
    <div id="registrationTabs" class="mb-4 flex gap-2">
      <button class="reg-tab px-4 py-2 border-b-2 border-blue-600" data-tab="personal" data-step="1">Basic Info</button>
      <button class="reg-tab px-4 py-2 border-b-2 border-transparent tab-locked" data-tab="sheet" data-step="2">Info Sheet</button>
    </div>


    <div id="regTabContent">
      <!-- Basic Info -->
      <div class="reg-content" id="tab-personal">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-nickname">Nickname</label>
          <input id="reg-nickname" name="nickname" type="text" placeholder="e.g. Chaz" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-dob">Date of Birth</label>
          <input id="reg-dob" name="dob" type="date" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Sex</label>
          <div class="flex items-center space-x-6">
            <label class="inline-flex items-center">
              <input type="radio" name="sex" value="male" class="form-radio text-blue-600">
              <span class="ml-2">Male</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="sex" value="female" class="form-radio text-pink-600">
              <span class="ml-2">Female</span>
            </label>
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-address">Address</label>
          <textarea id="reg-address" name="address" placeholder="123 Main St, City, Province" class="w-full border rounded px-3 py-2"></textarea>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-phone">Mobile Number</label>
          <input id="reg-phone" name="phone" type="tel" placeholder="+63 912 345 6789" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-email">Email Address</label>
          <input id="reg-email" name="email" type="email" placeholder="you@example.com" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-occupation">Occupation</label>
          <input id="reg-occupation" name="occupation" type="text" placeholder="e.g. Software Developer" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Civil Status</label>
          <div class="grid grid-cols-2 gap-2">
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="single" class="form-radio mr-2"> Single
            </label>
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="married" class="form-radio mr-2"> Married
            </label>
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="widower" class="form-radio mr-2"> Widow(er)
            </label>
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="separated" class="form-radio mr-2"> Separated
            </label>
            <label class="flex items-center">
              <input type="radio" name="civil_status" value="others" class="form-radio mr-2" id="civilOthers"> Others
            </label>
          </div>

          <!-- Custom input for others -->
          <div id="civilOtherInput" class="mt-2 hidden">
            <input type="text" name="civil_status_other" placeholder="Specify other civil status" class="w-full border rounded px-3 py-2">
          </div>
        </div>


        <div class="mb-4">
          <span class="block text-sm font-medium mb-1">Sacrament/s Received</span>
          <div class="grid grid-cols-3 gap-2">
            <label class="flex items-center"><input type="checkbox" name="sacraments[]" value="baptism" class="form-checkbox mr-2"> Baptism</label>
            <label class="flex items-center"><input type="checkbox" name="sacraments[]" value="first_communion" class="form-checkbox mr-2"> First Communion</label>
            <label class="flex items-center"><input type="checkbox" name="sacraments[]" value="confirmation" class="form-checkbox mr-2"> Confirmation</label>
          </div>
        </div>
        <div class="mb-4">
          <span class="block text-sm font-medium mb-1">Formations Received</span>
          <div class="grid grid-cols-1 gap-2">
            <label class="flex items-center"><input type="checkbox" name="formations[]" value="BOS" class="form-checkbox mr-2"> Basic Orientation Seminar (BOS)</label>
            <label class="flex items-center"><input type="checkbox" name="formations[]" value="BFF" class="form-checkbox mr-2"> Basic Faith Formation (BFF)</label>
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-others">Others</label>
          <input id="reg-others" name="others" type="text" placeholder="Anything else?" class="w-full border rounded px-3 py-2">
        </div>
      </div>

      <!-- Info Sheet -->
      <div class="reg-content hidden" id="tab-sheet">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-ministry">Ministry</label>
          <select name="ministry_id" id="reg-ministry" class="w-full border rounded px-3 py-2">
            <option value="">-- Select Ministry --</option>
            @foreach($ministries as $ministry)
            <optgroup label="{{ $ministry->ministry_name }}">
              @foreach($ministry->children as $sub)
              <option value="{{ $sub->id }}">{{ $sub->ministry_name }}</option>
              @endforeach
            </optgroup>
            @endforeach
          </select>

        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium mb-1" for="reg-applied-date">Month &amp; Year Applied</label>
            <input id="reg-applied-date" name="applied_date" type="month" class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" for="reg-regular-duration">No. of Years as Regular Volunteer</label>
            <input id="reg-regular-duration" name="regular_duration" type="text" placeholder="e.g. 1 yr 6 mos" class="w-full border rounded px-3 py-2">
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Name</label>
          <div class="grid grid-cols-3 gap-2">
            <input type="text" name="last_name" placeholder="Surname" class="border rounded px-2 py-1">
            <input type="text" name="first_name" placeholder="First Name" class="border rounded px-2 py-1">
            <input type="text" name="middle_initial" placeholder="M.I." class="border rounded px-2 py-1">
          </div>
        </div>

        <div class="mb-4">
          <h3 class="font-semibold mb-2">Volunteer Timeline</h3>
          <p class="text-sm text-gray-500 mb-2">Please indicate all Organization/Ministry you belong to in the Shrine</p>
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
              @for($i=0; $i<3; $i++)
                <tr>
                <td class="border p-1">
                  <input type="text" name="timeline_org[]" class="w-full border rounded px-2 py-1">
                </td>
                <td class="border p-1 flex gap-1 items-center">
                  <select name="timeline_start_year[]" class="border rounded px-2 py-1 year-select" data-row="{{ $i }}">
                    @for ($y = date('Y'); $y >= 1980; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                  </select>
                  <span>–</span>
                  <select name="timeline_end_year[]" class="border rounded px-2 py-1 year-select" data-row="{{ $i }}">
                    @for ($y = date('Y'); $y >= 1980; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                  </select>
                </td>
                <td class="border p-1">
                  <input type="text" name="timeline_total[]" class="w-full border rounded px-2 py-1 total-years" readonly>
                </td>
                <td class="border p-1 text-center">
                  <select name="timeline_active[]" class="border rounded px-2 py-1 w-full">
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
          <p class="text-sm text-gray-500 mb-2">Please indicate any Organization/Ministry you belong to outside the Shrine</p>
          <table class="w-full table-auto border-collapse mb-4">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1">Organization/Ministry</th>
                <th class="border px-2 py-1">Year Started–Year Ended</th>
                <th class="border px-2 py-1">Active? Y/N</th>
              </tr>
            </thead>
            <tbody>
              @for($i=0; $i<3; $i++)
                <tr>
                <td class="border p-1">
                  <input type="text" name="affil_org[]" class="w-full border rounded px-2 py-1">
                </td>
                <td class="border p-1 flex gap-1 items-center">
                  <select name="affil_start_year[]" class="border rounded px-2 py-1 year-select" data-row="{{ $i }}">
                    @for ($y = date('Y'); $y >= 1980; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                  </select>
                  <span>–</span>
                  <select name="affil_end_year[]" class="border rounded px-2 py-1 year-select" data-row="{{ $i }}">
                    @for ($y = date('Y'); $y >= 1980; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                  </select>
                </td>
                <td class="border p-1 text-center">
                  <select name="affil_active[]" class="border rounded px-2 py-1 w-full">
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
    <!-- At bottom of modal -->
    <div class="mt-4 flex justify-end gap-2">
      <button id="cancelRegistration" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Cancel</button>
      <button id="nextToSheet" class="px-4 py-2 bg-blue-600 text-white rounded">Next</button>
      <button id="submitRegistration" class="px-4 py-2 bg-green-600 text-white rounded hidden">Register Volunteer</button>
    </div>

  </div>


</div>
</div>

<!-- Profile Modal -->
<div id="profileModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
  <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto">
    <button id="closeProfile" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
    <div id="profileContent"></div>
    <div class="flex justify-end gap-2 mt-4">
      <button id="editProfile" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 flex items-center">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
        </svg>
        Edit Profile
      </button>
      <button id="scheduleVolunteer" class="px-4 py-2 bg-blue-600 text-white rounded flex items-center">
        Schedule Volunteer
      </button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // Toggle grid/list
  document.getElementById('gridViewBtn').addEventListener('click', () => {
    document.getElementById('gridView').style.display = 'grid';
    document.getElementById('listView').style.display = 'none';
  });
  document.getElementById('listViewBtn').addEventListener('click', () => {
    document.getElementById('gridView').style.display = 'none';
    document.getElementById('listView').style.display = 'block';
  });

  // Search filter
  document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.volunteer-card, .volunteer-row').forEach(el => {
      const vals = [el.dataset.name, el.dataset.email, el.dataset.skills, el.dataset.ministries].join(' ');
      el.style.display = vals.includes(q) ? '' : 'none';
    });
  });

  // Registration modal toggle
  document.getElementById('addVolunteerBtn').addEventListener('click', () => {
    document.getElementById('registrationModal').classList.replace('hidden', 'flex');
  });
  ['closeRegistration', 'cancelRegistration'].forEach(id =>
    document.getElementById(id).addEventListener('click', () => {
      document.getElementById('registrationModal').classList.replace('flex', 'hidden');
    })
  );
  document.getElementById('submitRegistration').addEventListener('click', () => {
    const formData = new FormData();


    // Basic Info
    formData.append('nickname', document.querySelector('[name="nickname"]').value);
    formData.append('dob', document.querySelector('[name="dob"]').value);
    formData.append('sex', document.querySelector('[name="sex"]:checked')?.value || '');
    formData.append('address', document.querySelector('[name="address"]').value);
    formData.append('phone', document.querySelector('[name="phone"]').value);
    formData.append('email', document.querySelector('[name="email"]').value);
    formData.append('occupation', document.querySelector('[name="occupation"]').value);

    // ✅ Civil status with dynamic handling of "Others"
    const civilStatusVal = document.querySelector('[name="civil_status"]:checked')?.value || '';
    formData.append('civil_status', civilStatusVal);
    if (civilStatusVal === 'others') {
      formData.append('civil_status_other', document.querySelector('[name="civil_status_other"]').value);
    }

    document.querySelectorAll('input[name="sacraments[]"]:checked').forEach(cb => formData.append('sacraments[]', cb.value));
    document.querySelectorAll('input[name="formations[]"]:checked').forEach(cb => formData.append('formations[]', cb.value));

    // Info Sheet
    formData.append('ministry_id', document.querySelector('[name="ministry_id"]').value);
    formData.append('applied_date', document.querySelector('[name="applied_date"]').value);
    formData.append('regular_duration', document.querySelector('[name="regular_duration"]').value);
    formData.append('last_name', document.querySelector('[name="last_name"]').value);
    formData.append('first_name', document.querySelector('[name="first_name"]').value);
    formData.append('middle_initial', document.querySelector('[name="middle_initial"]').value);

    document.querySelectorAll('input[name="timeline_org[]"]').forEach((el, i) => {
      formData.append(`timeline_org[${i}]`, el.value);
    });
    document.querySelectorAll('input[name="timeline_years[]"]').forEach((el, i) => {
      formData.append(`timeline_years[${i}]`, el.value);
    });
    document.querySelectorAll('input[name="timeline_total[]"]').forEach((el, i) => {
      formData.append(`timeline_total[${i}]`, el.value);
    });
    document.querySelectorAll('select[name="timeline_active[]"]').forEach((el, i) => {
      formData.append(`timeline_active[${i}]`, el.value);
    });

    document.querySelectorAll('input[name="affil_org[]"]').forEach((el, i) => {
      formData.append(`affil_org[${i}]`, el.value);
    });
    document.querySelectorAll('input[name="affil_years[]"]').forEach((el, i) => {
      formData.append(`affil_years[${i}]`, el.value);
    });
    document.querySelectorAll('select[name="affil_active[]"]').forEach((el, i) => {
      formData.append(`affil_active[${i}]`, el.value);
    });

    fetch("{{ route('volunteers.register') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        toastr.success(data.message);
        document.getElementById('registrationModal').classList.replace('flex', 'hidden');
        resetVolunteerForm();
      })
      .catch(err => {
        toastr.error('An error occurred while registering the volunteer.');
        console.error(err);
      });
  });

  function resetVolunteerForm() {
    // Text, email, date, and number fields
    document.querySelectorAll('#registrationModal input[type="text"], #registrationModal input[type="email"], #registrationModal input[type="tel"], #registrationModal input[type="date"], #registrationModal input[type="month"]').forEach(el => el.value = '');

    // Textareas
    document.querySelectorAll('#registrationModal textarea').forEach(el => el.value = '');

    // Selects
    document.querySelectorAll('#registrationModal select').forEach(el => el.selectedIndex = 0);

    // Checkboxes & radios
    document.querySelectorAll('#registrationModal input[type="checkbox"], #registrationModal input[type="radio"]').forEach(el => el.checked = false);
  }

  document.querySelectorAll('.year-select').forEach(select => {
    select.addEventListener('change', function() {
      const row = this.dataset.row;
      const startYear = parseInt(document.querySelector(`select[name="timeline_start_year[]"][data-row="${row}"]`).value);
      const endYear = parseInt(document.querySelector(`select[name="timeline_end_year[]"][data-row="${row}"]`).value);
      const totalInput = document.querySelectorAll('input[name="timeline_total[]"]')[row];
      if (!isNaN(startYear) && !isNaN(endYear)) {
        const total = endYear >= startYear ? endYear - startYear : 0;
        totalInput.value = `${total} year${total !== 1 ? 's' : ''}`;
      } else {
        totalInput.value = '';
      }
    });
  });

  // Tabs switching
  document.querySelectorAll('.reg-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      // Block tab if it’s locked
      if (this.classList.contains('tab-locked')) return;

      // Visual and content switching
      document.querySelectorAll('.reg-tab').forEach(t => t.classList.replace('border-blue-600', 'border-transparent'));
      this.classList.replace('border-transparent', 'border-blue-600');

      document.querySelectorAll('.reg-content').forEach(c => c.classList.add('hidden'));
      document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
    });
  });


  // Profile modal
  function openProfile(id) {
    // Show loading state
    const profileContent = document.getElementById('profileContent');
    profileContent.innerHTML = `
    <div class="flex items-center justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Loading profile...</span>
    </div>
  `;

    // Show modal immediately with loading state
    document.getElementById('profileModal').classList.replace('hidden', 'flex');

    // Fetch volunteer data from backend
    fetch(`/volunteers/${id}`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to fetch volunteer data');
        }
        return response.json();
      })
      .then(volunteer => {
        // Generate avatar seed from name
        const displayName = volunteer.nickname || volunteer.detail?.full_name || 'No Name';
        const avatarSeed = displayName.replace(/\s/g, '').toLowerCase();
        const avatarUrl = `https://api.dicebear.com/7.x/avataaars/svg?seed=${avatarSeed}`;

        // Format join date
        const joinDate = volunteer.created_at ? new Date(volunteer.created_at).toLocaleDateString() : 'Unknown';

        // Get ministry information
        const ministryName = volunteer.detail?.ministry?.ministry_name || 'No Ministry Assigned';

        // Get profile completion status
        const profileStatus = volunteer.detail?.volunteer_status
        const statusClass = volunteer.has_complete_profile ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';

        // Format sacraments
        const sacraments = volunteer.detail?.sacraments ?
          volunteer.detail.sacraments.split(',').map(s => s.trim()).filter(s => s) : [];

        // Format formations
        const formations = volunteer.detail?.formations ?
          volunteer.detail.formations.split(',').map(f => f.trim()).filter(f => f) : [];

        // Build the profile HTML
        const html = `
      <div class="flex items-center gap-4 mb-6">
        <img src="${avatarUrl}" alt="${displayName}" class="w-16 h-16 rounded-full">
        <div>
          <h2 class="text-xl font-bold">${displayName}</h2>
          <p class="text-sm text-gray-500">Joined on ${joinDate}</p>
          <span class="inline-block px-2 py-1 text-xs rounded-full ${statusClass} mt-1">
            ${profileStatus}
          </span>
        </div>
      </div>
      
      <div class="space-y-4">
        <!-- Contact Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500">Email</p>
            <p class="text-sm">${volunteer.email_address || 'Not provided'}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Phone</p>
            <p class="text-sm">${volunteer.mobile_number || 'Not provided'}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Date of Birth</p>
            <p class="text-sm">${volunteer.date_birth ? new Date(volunteer.date_birth).toLocaleDateString() : 'Not provided'}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Occupation</p>
            <p class="text-sm">${volunteer.occupation || 'Not provided'}</p>
          </div>
        </div>
        
        <!-- Address -->
        ${volunteer.address ? `
        <div>
          <p class="text-sm font-medium text-gray-500">Address</p>
          <p class="text-sm">${volunteer.address}</p>
        </div>
        ` : ''}
        
        <!-- Ministry -->
        <div>
          <p class="text-sm font-medium text-gray-500">Ministry</p>
          <span class="inline-block px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">
            ${ministryName}
          </span>
        </div>
        
        <!-- Personal Information -->
        ${volunteer.sex || volunteer.civil_status ? `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          ${volunteer.sex ? `
          <div>
            <p class="text-sm font-medium text-gray-500">Gender</p>
            <p class="text-sm capitalize">${volunteer.sex}</p>
          </div>
          ` : ''}
          ${volunteer.civil_status ? `
          <div>
            <p class="text-sm font-medium text-gray-500">Civil Status</p>
            <p class="text-sm capitalize">${volunteer.civil_status}</p>
          </div>
          ` : ''}
        </div>
        ` : ''}
        
        <!-- Sacraments -->
        ${sacraments.length > 0 ? `
        <div>
          <p class="text-sm font-medium text-gray-500 mb-2">Sacraments Received</p>
          <div class="flex flex-wrap gap-1">
            ${sacraments.map(sacrament => 
              `<span class="px-2 py-1 text-xs bg-purple-50 text-purple-700 rounded border border-purple-200">${sacrament}</span>`
            ).join('')}
          </div>
        </div>
        ` : ''}
        
        <!-- Formations -->
        ${formations.length > 0 ? `
        <div>
          <p class="text-sm font-medium text-gray-500 mb-2">Formations Received</p>
          <div class="flex flex-wrap gap-1">
            ${formations.map(formation => 
              `<span class="px-2 py-1 text-xs bg-green-50 text-green-700 rounded border border-green-200">${formation}</span>`
            ).join('')}
          </div>
        </div>
        ` : ''}
        
        <!-- Volunteer Timeline -->
        ${volunteer.detail?.applied_date || volunteer.detail?.regular_duration ? `
        <div>
          <p class="text-sm font-medium text-gray-500 mb-2">Volunteer Information</p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            ${volunteer.detail?.applied_date ? `
            <div>
              <p class="text-xs text-gray-400">Month & Year Applied</p>
              <p class="text-sm">${new Date(volunteer.detail.applied_date + '-01').toLocaleDateString('en-US', { year: 'numeric', month: 'long' })}</p>
            </div>
            ` : ''}
            ${volunteer.detail?.regular_duration ? `
            <div>
              <p class="text-xs text-gray-400">Years as Regular Volunteer</p>
              <p class="text-sm">${volunteer.detail.regular_duration}</p>
            </div>
            ` : ''}
          </div>
        </div>
        ` : ''}
        
        <!-- Additional Notes -->
        ${volunteer.others ? `
        <div>
          <p class="text-sm font-medium text-gray-500">Additional Notes</p>
          <p class="text-sm">${volunteer.others}</p>
        </div>
        ` : ''}
      </div>
    `;

        profileContent.innerHTML = html;

        // Store volunteer ID for edit functionality
        document.getElementById('editProfile').setAttribute('data-volunteer-id', id);
        document.getElementById('scheduleVolunteer').setAttribute('data-volunteer-id', id);
      })
      .catch(error => {
        console.error('Error fetching volunteer data:', error);
        profileContent.innerHTML = `
      <div class="flex items-center justify-center py-8 text-center">
        <div class="text-red-600">
          <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="font-medium">Failed to load profile</p>
          <p class="text-sm text-gray-500 mt-1">Please try again later</p>
        </div>
      </div>
    `;
      });
  }

  // Update the edit profile button functionality
  document.getElementById('editProfile').addEventListener('click', function() {
    const volunteerId = this.getAttribute('data-volunteer-id');
    if (volunteerId) {
      editVolunteer(volunteerId);
    }
  });

  // Update the schedule volunteer button functionality
  document.getElementById('scheduleVolunteer').addEventListener('click', function() {
    const volunteerId = this.getAttribute('data-volunteer-id');
    if (volunteerId) {
      // You can customize this action based on your needs
      alert(`Scheduling volunteer with ID: ${volunteerId}`);
      // Or redirect to a scheduling page:
      // window.location.href = `/volunteers/${volunteerId}/schedule`;
    }
  });

  // Add these helper functions if they don't exist
  function editVolunteer(id) {
    // Implement your edit volunteer functionality
    alert(`Edit volunteer with ID: ${id}`);
    // You might want to redirect to an edit page or open an edit modal
    // window.location.href = `/volunteers/${id}/edit`;
  }

  function deleteVolunteer(id) {
    if (confirm('Are you sure you want to delete this volunteer?')) {
      fetch(`/volunteers/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            toastr.success('Volunteer deleted successfully');
            location.reload(); // Refresh the page to update the list
          } else {
            toastr.error('Failed to delete volunteer');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          toastr.error('An error occurred while deleting the volunteer');
        });
    }
  }
  document.getElementById('closeProfile').addEventListener('click', () => {
    document.getElementById('profileModal').classList.replace('flex', 'hidden');
  });
  document.getElementById('scheduleVolunteer').addEventListener('click', () => {
    alert('Navigating to schedule volunteer page…');
    document.getElementById('profileModal').classList.replace('flex', 'hidden');
  });

  // Show/hide civil_status_other field
  document.addEventListener('DOMContentLoaded', () => {
    const radios = document.querySelectorAll('input[name="civil_status"]');
    const otherInput = document.getElementById('civilOtherInput');
    radios.forEach(r => {
      r.addEventListener('change', () => {
        otherInput.classList.toggle('hidden', r.value !== 'others');
      });
    });
  });

  document.getElementById('nextToSheet').addEventListener('click', () => {
    const requiredFields = ['nickname', 'dob', 'sex', 'address', 'phone', 'email', 'occupation'];
    let valid = true;

    requiredFields.forEach(name => {
      const field = document.querySelector(`[name="${name}"]`);
      if (!field || !field.value) {
        if (field) field.classList.add('border-red-500');
        valid = false;
      } else {
        field.classList.remove('border-red-500');
      }
    });

    const sex = document.querySelector('[name="sex"]:checked');
    const civil = document.querySelector('[name="civil_status"]:checked');
    if (!sex || !civil) valid = false;

    if (!valid) {
      toastr.warning('Please fill out all required fields.');
      return;
    }

    // Unlock and switch to Info Sheet tab
    const infoTab = document.querySelector('.reg-tab[data-tab="sheet"]');
    infoTab.classList.remove('tab-locked');
    infoTab.click();

    // Hide Next, Show Register
    document.getElementById('nextToSheet').classList.add('hidden');
    document.getElementById('submitRegistration').classList.remove('hidden');
  });
</script>
@endsection