<div class="w-full px-4 xl:px-8">
    <div class="max-w-screen-2xl mx-auto">
        {{-- Volunteer Cards Grid --}}
        @if($volunteers->isEmpty())
        <div class="w-full">
            <table class="min-w-full">
                <tbody>
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
                </tbody>
            </table>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            @foreach ($volunteers as $volunteer)
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

            <div class="bg-white shadow rounded overflow-hidden cursor-pointer volunteer-card"
                data-name="{{ strtolower($displayName) }}"
                data-email="{{ strtolower($volunteer->email_address ?? '') }}"
                data-ministry="{{ strtolower($volunteer->detail?->ministry?->ministry_name ?? '') }}"
                data-id="{{ $volunteer->id }}"
                onclick="openProfile('{{ $volunteer->id }}')">
                <div class="p-4 flex flex-col items-center">
                    @if($volunteer->profile_picture)
                    <img src="{{ asset('storage/' . $volunteer->profile_picture) }}"
                        alt="{{ $displayName }}" class="w-20 h-20 rounded-full mb-4 object-cover">
                    @else
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $avatarSeed }}"
                        alt="{{ $displayName }}" class="w-20 h-20 rounded-full mb-4">
                    @endif
                    <h3 class="font-semibold text-lg text-center">{{ $displayName }}</h3>
                    <p class="text-sm text-gray-500 mb-2 text-center break-words leading-tight w-full">
                        {{ $volunteer->email_address ?? 'No email' }}
                    </p>

                    <div class="flex justify-center mt-2">
                        @if ($volunteer->detail?->ministry)
                        <span class="inline-block px-3 py-1 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-center">
                            {{ $volunteer->detail->ministry->ministry_name }}
                        </span>
                        @else
                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-gray-50 text-gray-500 border border-gray-200">
                            No Ministry Assigned
                        </span>
                        @endif
                    </div>

                    <span class="mt-2 inline-block px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                        {{ $status }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Pagination --}}
        @if ($volunteers->hasPages())
        <div class="mt-10 flex justify-center pagination">
            {!! $volunteers->appends(request()->query())->links() !!}
        </div>
        @endif
    </div>
</div>