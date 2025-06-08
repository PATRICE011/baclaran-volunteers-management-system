{{-- resources/views/archives.blade.php --}}
@extends('components.layout')

@section('title', 'Archives')

@section('styles')
    <style>
        .modal-bg {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
@endsection

@section('content')
    @include('components.navs')
    <main class="flex-1 overflow-auto p-4 sm:p-6" x-data="{ tab: 'volunteers' }">
        <div class="bg-white rounded-lg shadow p-6">

            {{-- Heading --}}
            <h2 class="text-2xl font-bold mb-6">Archives</h2>

            {{-- Tabs --}}
            <div class="border-b mb-6">
                <div class="flex space-x-2">
                    <button @click="tab = 'volunteers'"
                        :class="tab === 'volunteers' ? 'border-b-2 border-blue-600 text-gray-900 font-medium' : 'text-gray-500'"
                        class="px-4 py-2">
                        Volunteers
                    </button>
                    <button @click="tab = 'ministries'"
                        :class="tab === 'ministries' ? 'border-b-2 border-blue-600 text-gray-900 font-medium' : 'text-gray-500'"
                        class="px-4 py-2">
                        Ministries
                    </button>
                    <button @click="tab = 'tasks'"
                        :class="tab === 'tasks' ? 'border-b-2 border-blue-600 text-gray-900 font-medium' : 'text-gray-500'"
                        class="px-4 py-2">
                        Tasks
                    </button>
                    <button @click="tab = 'events'"
                        :class="tab === 'events' ? 'border-b-2 border-blue-600 text-gray-900 font-medium' : 'text-gray-500'"
                        class="px-4 py-2">
                        Events
                    </button>
                </div>
            </div>

            {{-- Volunteers Tab --}}
            <div x-show="tab === 'volunteers'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border p-2 text-left text-sm">Name</th>
                                <th class="border p-2 text-left text-sm">Archived Date</th>
                                <th class="border p-2 text-left text-sm">Reason</th>
                                <th class="border p-2 text-left text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Example static rows --}}
                            <tr>
                                <td class="border p-2 text-sm">John Smith</td>
                                <td class="border p-2 text-sm">2023-10-15</td>
                                <td class="border p-2 text-sm">Moved to another city</td>
                                <td class="border p-2 space-x-2">
                                    <button
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md border border-gray-300 bg-white text-sm font-medium shadow-sm transition-colors duration-200 hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-300">
                                        Restore
                                    </button>
                                    <button
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-red-600 text-white text-sm font-medium shadow-sm transition-colors duration-200 hover:bg-red-500 focus:outline-none focus:ring-1 focus:ring-red-300">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="border p-2 text-sm">Sarah Johnson</td>
                                <td class="border p-2 text-sm">2023-11-20</td>
                                <td class="border p-2 text-sm">No longer available on weekends</td>
                                <td class="border p-2 space-x-2">
                                    <button
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md border border-gray-300 bg-white text-sm font-medium shadow-sm transition-colors duration-200 hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-300">
                                        Restore
                                    </button>
                                    <button
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-red-600 text-white text-sm font-medium shadow-sm transition-colors duration-200 hover:bg-red-500 focus:outline-none focus:ring-1 focus:ring-red-300">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Ministries Tab --}}
            <div x-show="tab === 'ministries'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border p-2 text-left text-sm">Name</th>
                                <th class="border p-2 text-left text-sm">Archived Date</th>
                                <th class="border p-2 text-left text-sm">Reason</th>
                                <th class="border p-2 text-left text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border p-2 text-sm">Youth Ministry</td>
                                <td class="border p-2 text-sm">2023-09-05</td>
                                <td class="border p-2 text-sm">Merged with Children's Ministry</td>
                                <td class="border p-2 space-x-2">
                                    <button
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md border border-gray-300 bg-white text-sm font-medium shadow-sm
                         transition-colors duration-200 hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-300">
                                        Restore
                                    </button>

                                    <button
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-red-600 text-white text-sm font-medium shadow-sm
                         transition-colors duration-200 hover:bg-red-500 focus:outline-none focus:ring-1 focus:ring-red-300">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="border p-2 text-sm">Community Outreach</td>
                                <td class="border p-2 text-sm">2023-12-01</td>
                                <td class="border p-2 text-sm">Program ended</td>
                                <td class="border p-2 space-x-2">
                                    <button
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md border border-gray-300 bg-white text-sm font-medium shadow-sm
                         transition-colors duration-200 hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-300">
                                        Restore
                                    </button>
                                    <button
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md bg-red-600 text-white text-sm font-medium shadow-sm
                         transition-colors duration-200 hover:bg-red-500 focus:outline-none focus:ring-1 focus:ring-red-300">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tasks Tab --}}
            <div x-show="tab === 'tasks'" x-cloak>
                <p class="text-gray-600">Tasks archive will go here.</p>
            </div>

            {{-- Events Tab --}}
            <div x-show="tab === 'events'" x-cloak>
                <p class="text-gray-600">Events archive will go here.</p>
            </div>

        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
