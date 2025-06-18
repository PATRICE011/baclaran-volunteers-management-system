@props(['title'])

<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
    <h3 class="text-md font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    <div class="space-y-4">
        {{ $slot }}
    </div>
</div>
