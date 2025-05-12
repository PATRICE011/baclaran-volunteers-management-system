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
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
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
                d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
        </svg>
      </div>
      <input id="searchInput" type="text" placeholder="Search volunteers by name, email, skills, or ministry..."
             class="pl-10 pr-3 py-2 border rounded w-full focus:outline-none focus:border-blue-500">
    </div>
    <div class="flex gap-2">
      <button id="gridViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Grid</button>
      <button id="listViewBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">List</button>
      <button id="filterBtn" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 flex items-center">
        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707v4.586a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1v-4.586a1 1 0 0 0-.293-.707L1.293 6.707A1 1 0 0 1 1 6V4z"/>
        </svg>
        Filter
      </button>
    </div>
  </div>

  <!-- Volunteers Data -->
  @php
  $current = request()->is('volunteers') ? 'active' : '';

    $volunteers = [
      ['id'=>'1','name'=>'John Smith','email'=>'john.smith@example.com','phone'=>'(555) 123-4567','avatar'=>'https://api.dicebear.com/7.x/avataaars/svg?seed=john','skills'=>['Music','Teaching','Leadership'],'ministries'=>['Worship','Youth'],'availability'=>['Sunday Morning','Wednesday Evening'],'joinDate'=>'2022-01-15','status'=>'active'],
      ['id'=>'2','name'=>'Sarah Johnson','email'=>'sarah.j@example.com','phone'=>'(555) 987-6543','avatar'=>'https://api.dicebear.com/7.x/avataaars/svg?seed=sarah','skills'=>['Administration','Hospitality'],'ministries'=>['Children','Hospitality'],'availability'=>['Sunday Morning','Sunday Evening'],'joinDate'=>'2021-08-22','status'=>'active'],
      ['id'=>'3','name'=>'Michael Chen','email'=>'michael.c@example.com','phone'=>'(555) 456-7890','avatar'=>'https://api.dicebear.com/7.x/avataaars/svg?seed=michael','skills'=>['Technical','Media','Design'],'ministries'=>['Media','Tech Team'],'availability'=>['Sunday Morning','Saturday'],'joinDate'=>'2022-03-10','status'=>'active'],
      ['id'=>'4','name'=>'Emily Rodriguez','email'=>'emily.r@example.com','phone'=>'(555) 234-5678','avatar'=>'https://api.dicebear.com/7.x/avataaars/svg?seed=emily','skills'=>['Teaching','Counseling'],'ministries'=>['Women','Prayer'],'availability'=>['Tuesday Evening','Thursday Evening'],'joinDate'=>'2021-11-05','status'=>'inactive'],
      ['id'=>'5','name'=>'David Wilson','email'=>'david.w@example.com','phone'=>'(555) 876-5432','avatar'=>'https://api.dicebear.com/7.x/avataaars/svg?seed=david','skills'=>['Leadership','Finance','Teaching'],'ministries'=>['Men','Finance'],'availability'=>['Sunday Morning','Monday Evening'],'joinDate'=>'2022-02-18','status'=>'active'],
      ['id'=>'6','name'=>'Lisa Thompson','email'=>'lisa.t@example.com','phone'=>'(555) 345-6789','avatar'=>'https://api.dicebear.com/7.x/avataaars/svg?seed=lisa','skills'=>['Music','Hospitality'],'ministries'=>['Worship','Hospitality'],'availability'=>['Sunday Morning','Wednesday Evening'],'joinDate'=>'2021-09-30','status'=>'active'],
    ];
  @endphp

  <!-- Grid View -->
  <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach($volunteers as $vol)
      <div class="bg-white shadow rounded overflow-hidden cursor-pointer volunteer-card"
           data-name="{{ strtolower($vol['name']) }}"
           data-email="{{ strtolower($vol['email']) }}"
           data-skills="{{ strtolower(implode(' ', $vol['skills'])) }}"
           data-ministries="{{ strtolower(implode(' ', $vol['ministries'])) }}"
           data-id="{{ $vol['id'] }}"
           onclick="openProfile('{{ $vol['id'] }}')">
        <div class="p-4 flex flex-col items-center">
          <img src="{{ $vol['avatar'] }}" alt="{{ $vol['name'] }}" class="w-20 h-20 rounded-full mb-4">
          <h3 class="font-semibold text-lg">{{ $vol['name'] }}</h3>
          <p class="text-sm text-gray-500 mb-2">{{ $vol['email'] }}</p>
          <div class="flex flex-wrap gap-1 justify-center mt-2">
            @foreach($vol['ministries'] as $m)
              <span class="inline-block px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-200">{{ $m }}</span>
            @endforeach
          </div>
          <span class="mt-3 inline-block px-2 py-1 text-xs rounded {{ $vol['status']==='active' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
            {{ ucfirst($vol['status']) }}
          </span>
        </div>
      </div>
    @endforeach
  </div>

  <!-- List View -->
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
        @foreach($volunteers as $vol)
          <tr class="border-t hover:bg-gray-100 volunteer-row"
              data-name="{{ strtolower($vol['name']) }}"
              data-email="{{ strtolower($vol['email']) }}"
              data-skills="{{ strtolower(implode(' ', $vol['skills'])) }}"
              data-ministries="{{ strtolower(implode(' ', $vol['ministries'])) }}"
              data-id="{{ $vol['id'] }}">
            <td class="px-4 py-2 flex items-center gap-2 cursor-pointer" onclick="openProfile('{{ $vol['id'] }}')">
              <img src="{{ $vol['avatar'] }}" alt="{{ $vol['name'] }}" class="w-8 h-8 rounded-full">
              {{ $vol['name'] }}
            </td>
            <td class="px-4 py-2 hidden md:table-cell">{{ $vol['email'] }}</td>
            <td class="px-4 py-2 hidden lg:table-cell">{{ $vol['phone'] }}</td>
            <td class="px-4 py-2 hidden lg:table-cell">
              <div class="flex gap-1">
                @foreach($vol['ministries'] as $m)
                  <span class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">{{ $m }}</span>
                @endforeach
              </div>
            </td>
            <td class="px-4 py-2 hidden md:table-cell">
              <span class="px-2 py-1 text-xs rounded {{ $vol['status']==='active' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">{{ ucfirst($vol['status']) }}</span>
            </td>
            <td class="px-4 py-2 text-right">
              <button class="text-blue-500 mr-2" onclick="openProfile('{{ $vol['id'] }}'); event.stopPropagation();">View</button>
              <button class="text-blue-500 mr-2">Edit</button>
              <button class="text-red-500">Delete</button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="flex justify-center space-x-2 mt-6">
    <button class="px-3 py-1 border rounded text-gray-500 hover:bg-gray-100">Previous</button>
    <button class="px-3 py-1 bg-blue-600 text-white rounded">1</button>
    <button class="px-3 py-1 border rounded text-gray-500 hover:bg-gray-100">2</button>
    <button class="px-3 py-1 border rounded text-gray-500 hover:bg-gray-100">3</button>
    <button class="px-3 py-1 border rounded text-gray-500 hover:bg-gray-100">Next</button>
  </div>
</div>

<!-- Registration Modal -->
<div id="registrationModal" class="fixed inset-0 hidden items-center justify-center modal-bg">
  <div class="bg-white rounded-lg w-full max-w-lg p-6 relative max-h-[80vh] overflow-y-auto">
    <button id="closeRegistration" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

    <h2 class="text-xl font-bold mb-4">Volunteer Registration</h2>

    <!-- Tabs -->
    <div id="registrationTabs" class="mb-4 flex gap-2">
      <button class="reg-tab px-4 py-2 border-b-2 border-blue-600" data-tab="personal">Basic Info</button>
      <button class="reg-tab px-4 py-2 border-b-2 border-transparent" data-tab="sheet">Info Sheet</button>

      <button class="reg-tab px-4 py-2 border-b-2 border-transparent" data-tab="skills">Skills</button>
      <button class="reg-tab px-4 py-2 border-b-2 border-transparent" data-tab="availability">Availability</button>
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
            <label class="flex items-center"><input type="radio" name="civil_status" value="single" class="form-radio mr-2"> Single</label>
            <label class="flex items-center"><input type="radio" name="civil_status" value="married" class="form-radio mr-2"> Married</label>
            <label class="flex items-center"><input type="radio" name="civil_status" value="widower" class="form-radio mr-2"> Widow(er)</label>
            <label class="flex items-center"><input type="radio" name="civil_status" value="separated" class="form-radio mr-2"> Separated</label>
          </div>
          <div class="mt-2 ml-6 flex gap-4">
            <label class="inline-flex items-center"><input type="checkbox" name="marriage_type[]" value="church" class="form-checkbox mr-2"> Church</label>
            <label class="inline-flex items-center"><input type="checkbox" name="marriage_type[]" value="civil" class="form-checkbox mr-2"> Civil</label>
          </div>
          <div class="mt-2">
            <label class="block text-sm font-medium mb-1" for="reg-civil-other">Others</label>
            <input id="reg-civil-other" name="civil_other" type="text" placeholder="Specify if needed" class="w-full border rounded px-3 py-2">
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
          <input id="reg-ministry" name="ministry" type="text" placeholder="If member of RMM, RYM or RCCOM, specify group" class="w-full border rounded px-3 py-2">
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
                <td class="border p-1"><input type="text" name="timeline_org[]" class="w-full border rounded px-2 py-1"></td>
                <td class="border p-1"><input type="text" name="timeline_years[]" placeholder="YYYY–YYYY" class="w-full border rounded px-2 py-1"></td>
                <td class="border p-1"><input type="text" name="timeline_total[]" class="w-full border rounded px-2 py-1"></td>
                <td class="border p-1 text-center">
                  <select name="timeline_active[]" class="border rounded px-2 py-1 w-full">
                    <option value="">–</option><option value="Y">Y</option><option value="N">N</option>
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
                <td class="border p-1"><input type="text" name="affil_org[]" class="w-full border rounded px-2 py-1"></td>
                <td class="border p-1"><input type="text" name="affil_years[]" placeholder="YYYY–YYYY" class="w-full border rounded px-2 py-1"></td>
                <td class="border p-1 text-center">
                  <select name="affil_active[]" class="border rounded px-2 py-1 w-full">
                    <option value="">–</option><option value="Y">Y</option><option value="N">N</option>
                  </select>
                </td>
              </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <div class="mb-4 text-sm text-gray-500">
          <p>
            Privacy Notice: The personal information collected in this form are part of the requirement for your application as volunteer… See the
            <a href="#" class="underline text-blue-600">Shrine’s Data Privacy Policy</a>.
          </p>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Volunteer’s Signature</label>
            <input type="text" name="signature" class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Date</label>
            <input type="date" name="signature_date" class="w-full border rounded px-3 py-2">
          </div>
        </div>
      </div>

      <!-- Skills -->
      <div class="reg-content hidden" id="tab-skills">
        <div class="mb-4">
          <span class="block text-sm font-medium mb-1">Skills & Talents</span>
          <div class="grid grid-cols-2 gap-2">
            @foreach(['Music','Teaching','Leadership','Administration','Technical','Hospitality'] as $skill)
              <label class="flex items-center"><input type="checkbox" name="skills[]" value="{{ strtolower($skill) }}" class="mr-2"> {{ $skill }}</label>
            @endforeach
          </div>
        </div>
        <div class="mb-4">
          <span class="block text-sm font-medium mb-1">Ministry Preferences</span>
          <div class="grid grid-cols-2 gap-2">
            @foreach(['Worship','Children','Youth','Media','Hospitality','Prayer'] as $min)
              <label class="flex items-center"><input type="checkbox" name="ministries[]" value="{{ strtolower($min) }}" class="mr-2"> {{ $min }}</label>
            @endforeach
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-experience">Previous Experience</label>
          <textarea id="reg-experience" name="experience" placeholder="Describe any relevant experience..." class="w-full border rounded px-3 py-2"></textarea>
        </div>
      </div>

      <!-- Availability -->
      <div class="reg-content hidden" id="tab-availability">
        <div class="mb-4">
          <span class="block text-sm font-medium mb-1">Availability</span>
          <div class="grid grid-cols-2 gap-2">
            @foreach(['Sunday Morning','Sunday Evening','Monday Evening','Tuesday Evening','Wednesday Evening','Thursday Evening','Friday Evening','Saturday'] as $slot)
              <label class="flex items-center"><input type="checkbox" name="availability[]" value="{{ strtolower(str_replace(' ', '_', $slot)) }}" class="mr-2"> {{ $slot }}</label>
            @endforeach
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-frequency">Preferred Frequency</label>
          <select id="reg-frequency" name="frequency" class="w-full border rounded px-3 py-2">
            <option value="">Select frequency</option>
            @foreach(['weekly'=>'Weekly','biweekly'=>'Bi-weekly','monthly'=>'Monthly','asneeded'=>'As needed'] as $val => $label)
              <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1" for="reg-notes">Additional Notes</label>
          <textarea id="reg-notes" name="notes" placeholder="Any additional information about your availability..." class="w-full border rounded px-3 py-2"></textarea>
        </div>
      </div>
    </div>

    <div class="mt-4 flex justify-end gap-2">
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
              d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
    <div id="profileContent"></div>
    <div class="flex justify-end gap-2 mt-4">
      <button id="editProfile" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100 flex items-center">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/>
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
    document.getElementById('registrationModal').classList.replace('hidden','flex');
  });
  ['closeRegistration','cancelRegistration'].forEach(id =>
    document.getElementById(id).addEventListener('click', () => {
      document.getElementById('registrationModal').classList.replace('flex','hidden');
    })
  );
  document.getElementById('submitRegistration').addEventListener('click', () => {
    alert('Volunteer registered successfully!');
    document.getElementById('registrationModal').classList.replace('flex','hidden');
  });

  // Tabs switching
  document.querySelectorAll('.reg-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      document.querySelectorAll('.reg-tab').forEach(t => t.classList.replace('border-blue-600','border-transparent'));
      this.classList.replace('border-transparent','border-blue-600');
      document.querySelectorAll('.reg-content').forEach(c => c.classList.add('hidden'));
      document.getElementById('tab-'+this.dataset.tab).classList.remove('hidden');
    });
  });

  // Profile modal
  function openProfile(id) {
    const vols = @json($volunteers);
    const v = vols.find(x => x.id === id);
    if (!v) return;
    let html = `
      <div class="flex items-center gap-4">
        <img src="${v.avatar}" alt="${v.name}" class="w-16 h-16 rounded-full">
        <div>
          <h2 class="text-xl font-bold">${v.name}</h2>
          <p class="text-sm text-gray-500">Joined on ${new Date(v.joinDate).toLocaleDateString()}</p>
        </div>
      </div>
      <div class="mt-4 grid grid-cols-2 gap-4">
        <div><p class="text-sm font-medium text-gray-500">Email</p><p>${v.email}</p></div>
        <div><p class="text-sm font-medium text-gray-500">Phone</p><p>${v.phone}</p></div>
        <div><p class="text-sm font-medium text-gray-500">Status</p><p>${v.status==='active'?'Active':'Inactive'}</p></div>
      </div>
      <div class="mt-4">
        <p class="text-sm font-medium text-gray-500">Ministries</p>
        <div class="flex flex-wrap gap-1">
          ${v.ministries.map(m=>`<span class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">${m}</span>`).join('')}
        </div>
      </div>`;
    document.getElementById('profileContent').innerHTML = html;
    document.getElementById('profileModal').classList.replace('hidden','flex');
  }
  document.getElementById('closeProfile').addEventListener('click', () => {
    document.getElementById('profileModal').classList.replace('flex','hidden');
  });
  document.getElementById('scheduleVolunteer').addEventListener('click', () => {
    alert('Navigating to schedule volunteer page…');
    document.getElementById('profileModal').classList.replace('flex','hidden');
  });
</script>
@endsection
