<div class="bg-white shadow rounded-lg overflow-hidden">
  <div  class="overflow-x-auto">
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
      @php
        $displayName = $volunteer->nickname ?? $volunteer->detail?->full_name ?? 'No Name';
        $avatarSeed = str_replace(' ', '', strtolower($displayName));
        $status = $volunteer->detail?->volunteer_status ?? 'Unknown';
        $statusClass = match ($status) {
            'Active' => 'bg-green-100 text-green-700 border border-green-200',
            'Inactive' => 'bg-red-100 text-red-700 border border-red-200',
            default => 'bg-gray-100 text-gray-500 border border-gray-200',
        };
      @endphp
      <tr class="hover:bg-gray-50 volunteer-row"
          data-name="{{ strtolower($displayName) }}"
          data-email="{{ strtolower($volunteer->email_address ?? '') }}"
          data-ministry="{{ strtolower($volunteer->detail?->ministry?->ministry_name ?? '') }}"
          data-id="{{ $volunteer->id }}">
        <td class="px-6 py-4 whitespace-nowrap cursor-pointer" onclick="openProfile('{{ $volunteer->id }}')">
          <div class="flex items-center">
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
          <span class="inline-block px-2 py-1 text-xs rounded-full {{ $statusClass }}">
            {{ $status }}
          </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
          <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="openProfile('{{ $volunteer->id }}'); event.stopPropagation();">View</button>
          <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="editVolunteer('{{ $volunteer->id }}'); event.stopPropagation();">Edit</button>
          <button class="text-red-600 hover:text-red-900" onclick="deleteVolunteer('{{ $volunteer->id }}'); event.stopPropagation();">Delete</button>
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
@if ($volunteers->hasPages())
  <div class="flex justify-center space-x-2 mt-6">
     {{ $volunteers->appends(request()->query())->links() }}
</div>
@endif