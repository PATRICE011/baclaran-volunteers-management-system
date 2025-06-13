{{-- resources/views/ministries.blade.php --}}

@php
// Static array of ministries—no database involved
$ministries = [
[
'name' => 'Worship Team',
'description' => 'Lead worship services through music and song',
'category' => 'Worship',
'volunteers' => 12,
],
[
'name' => "Children's Ministry",
'description' => 'Teach and care for children during services',
'category' => 'Education',
'volunteers' => 8,
],
[
'name' => 'Welcome Team',
'description' => 'Greet visitors and help them find their way',
'category' => 'Hospitality',
'volunteers' => 6,
],
[
'name' => 'Media Team',
'description' => 'Handle audio, video, and presentation during services',
'category' => 'Technical',
'volunteers' => 5,
],
[
'name' => 'Prayer Team',
'description' => 'Lead and organize prayer meetings and initiatives',
'category' => 'Spiritual',
'volunteers' => 10,
],
[
'name' => 'Outreach',
'description' => 'Organize community service and evangelism events',
'category' => 'Missions',
'volunteers' => 15,
],
];
@endphp

@extends('components.layout')

@section('title', 'Ministries')

@section('styles')
<style>
    .modal-bg {
        background: rgba(0, 0, 0, 0.5);
    }
</style>
@endsection

@section('content')
@include('components.navs')
<div class="md:ml-64">
    {{-- Alpine root: show/hide both modals + track selected ministry index + category filter --}}
    <main class="flex-1 overflow-auto p-4 sm:p-6" x-data="{
        showModal: false, // controls “View Details”
        showAddModal: false, // controls “Add New Ministry”
        selectedIndex: null, // which ministry is currently “viewing details”
        selectedCategory: 'All', // filter dropdown state
        ministries: {{ json_encode($ministries) }}
    }">
        <div class="bg-background min-h-screen p-6">
            {{-- ─────────────────────────────────────────────────────────────────── --}}
            {{-- Search bar / Filters / Add Ministry Button                         --}}
            {{-- ─────────────────────────────────────────────────────────────────── --}}
            <div class="flex justify-between items-center mb-6">
                {{-- Search input --}}
                <div class="relative w-full max-w-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-search absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <input type="search"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 pl-8"
                        placeholder="Search ministries...">
                </div>

                {{-- Category dropdown & Add Ministry --}}
                <div class="flex items-center gap-4">
                    {{-- ▼ Category selector (now a real <select>) --}}
                    <select x-model="selectedCategory"
                        class="h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="All">All Categories</option>
                        <option value="Education">Education</option>
                        <option value="Hospitality">Hospitality</option>
                        <option value="Technical">Technical</option>
                    </select>

                    {{-- Add Ministry button (opens the “Add New Ministry” modal) --}}
                    <button type="button"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md bg-primary text-primary-foreground text-sm font-medium shadow hover:bg-primary/90 h-9 px-4 py-2"
                        @click="showAddModal = true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-plus mr-2 h-4 w-4">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                        Add Ministry
                    </button>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────────────── --}}
            {{-- Grid of Ministry Cards                                             --}}
            {{-- ─────────────────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="(ministry, idx) in ministries" :key="idx">
                    <div class="rounded-xl border bg-card text-card-foreground shadow overflow-hidden"
                        x-show="selectedCategory === 'All' || ministry.category === selectedCategory" x-cloak>
                        {{-- Card header: name, description, category badge --}}
                        <div class="flex flex-col space-y-1.5 p-6 pb-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold leading-none tracking-tight" x-text="ministry.name"></h3>
                                    <p class="text-sm text-muted-foreground mt-1" x-text="ministry.description"></p>
                                </div>
                                <div class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground hover:bg-secondary/80"
                                    x-text="ministry.category"></div>
                            </div>
                        </div>

                        {{-- Volunteer count --}}
                        <div class="p-6 pt-0">
                            <div class="text-sm">
                                <span class="font-medium" x-text="ministry.volunteers"></span>
                                volunteers assigned
                            </div>
                        </div>

                        {{-- View Details button --}}
                        <div class="flex items-center p-6 border-t bg-muted/50 px-6 py-3">
                            <button
                                class="ml-auto inline-flex items-center justify-center rounded-md px-3 py-1 text-xs font-medium hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:opacity-50"
                                @click="selectedIndex = idx; showModal = true">
                                View Details
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-chevron-down ml-1 h-4 w-4">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────────── --}}
        {{-- Modal #1: View Details for Selected Ministry                       --}}
        {{-- ─────────────────────────────────────────────────────────────────── --}}
        <div x-show="showModal" x-cloak x-transition.opacity
            class="fixed inset-0 modal-bg flex items-center justify-center z-50">
            <div x-show="showModal" x-cloak x-transition @click.away="showModal = false" role="dialog"
                aria-labelledby="modal-title" aria-describedby="modal-description"
                class="relative w-[90%] max-w-xl rounded-lg bg-white p-6 shadow-xl" tabindex="-1">
                {{-- Close “X” --}}
                <button type="button" @click="showModal = false"
                    class="absolute right-4 top-4 rounded-sm opacity-60 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4">
                        <path
                            d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z"
                            fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close</span>
                </button>

                {{-- Header: Ministry Name + Category Badge --}}
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 id="modal-title" class="text-lg font-semibold leading-none tracking-tight"
                            x-text="ministries[selectedIndex]?.name"></h2>
                        <p id="modal-description" class="text-sm text-muted-foreground mt-1"
                            x-text="ministries[selectedIndex]?.description"></p>
                    </div>
                    <div class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold bg-secondary text-secondary-foreground hover:bg-secondary/80"
                        x-text="ministries[selectedIndex]?.category"></div>
                </div>

                {{-- Body: Volunteer Info + Ministry Details --}}
                <div class="grid grid-cols-2 gap-6 mb-6">
                    {{-- Volunteer Information --}}
                    <div>
                        <h3 class="font-medium mb-2">Volunteer Information</h3>
                        <p class="text-sm text-muted-foreground mb-3">
                            <span class="font-medium" x-text="ministries[selectedIndex]?.volunteers"></span>
                            volunteers currently assigned
                        </p>
                        <div class="max-h-48 overflow-y-auto border rounded-md" x-data="{
                            volunteersList: [
                                { name: 'John Smith', email: 'john.smith@example.com', phone: '(555) 123-4567' },
                                { name: 'Sarah Johnson', email: 'sarah.j@example.com', phone: '(555) 234-5678' },
                                { name: 'Michael Brown', email: 'mbrown@example.com', phone: '(555) 345-6789' },
                                { name: 'Emily Davis', email: 'emily.d@example.com', phone: '(555) 456-7890' }
                            ]
                        }">
                            <template x-for="(vol, vIdx) in volunteersList" :key="vIdx">
                                <div class="p-2 border-b last:border-b-0 hover:bg-muted/50">
                                    <div class="font-medium" x-text="vol.name"></div>
                                    <div class="text-xs text-muted-foreground" x-text="vol.email"></div>
                                    <div class="text-xs text-muted-foreground" x-text="vol.phone"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Ministry Details --}}
                    <div>
                        <h3 class="font-medium mb-2">Ministry Details</h3>
                        <p class="text-sm text-muted-foreground">
                            Ministry ID: <span class="font-medium" x-text="selectedIndex + 1"></span><br>
                            Name: <span class="font-medium" x-text="ministries[selectedIndex]?.name"></span><br>
                            Category: <span class="font-medium" x-text="ministries[selectedIndex]?.category"></span><br>
                            Volunteers: <span class="font-medium" x-text="ministries[selectedIndex]?.volunteers"></span>
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mb-6">
                    <h3 class="font-medium mb-2">Actions</h3>
                    <div class="flex gap-2">
                        <button
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md border border-input bg-background px-3 py-2 text-xs font-medium hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:opacity-50">
                            Clone Ministry
                        </button>
                        <button
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md bg-red-500 text-white px-3 py-2 text-xs font-medium shadow-sm hover:bg-red-600 focus:outline-none focus:ring-1 focus:ring-red-400 disabled:opacity-50">
                            Delete Ministry
                        </button>
                    </div>
                </div>

                {{-- Footer: Close Button --}}
                <div class="flex justify-end">
                    <button
                        class="rounded-md bg-gray-100 px-4 py-2 text-sm font-medium shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-1 focus:ring-ring disabled:opacity-50"
                        @click="showModal = false">
                        Close
                    </button>
                </div>
            </div>
        </div>


        {{-- ─────────────────────────────────────────────────────────────────── --}}
        {{-- Modal #2: Add New Ministry (as provided by the user)             --}}
        {{-- ─────────────────────────────────────────────────────────────────── --}}
        <div x-show="showAddModal" x-cloak x-transition.opacity
            class="fixed inset-0 modal-bg flex items-center justify-center z-50">
            <div x-show="showAddModal" x-cloak x-transition @click.away="showAddModal = false" role="dialog"
                aria-labelledby="add-modal-title" aria-describedby="add-modal-description"
                class="fixed left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border bg-white p-6 shadow-lg rounded-lg sm:max-w-[425px]"
                tabindex="-1">
                {{-- Header --}}
                <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                    <h2 id="add-modal-title" class="text-lg font-semibold leading-none tracking-tight">
                        Add New Ministry
                    </h2>
                    <p id="add-modal-description" class="text-sm text-muted-foreground">
                        Create a new ministry for volunteers to join.
                    </p>
                </div>

                {{-- Form fields: Name, Category, Description, Volunteers --}}
                <div class="grid gap-4 py-4" x-data="{
                    newName: '',
                    newCategory: '',
                    newDescription: '',
                    newVolunteers: null
                }">
                    {{-- Name --}}
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="name" class="text-sm font-medium leading-none text-right">Name</label>
                        <input id="name" type="text" x-model="newName"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring col-span-3"
                            placeholder="e.g. Youth Fellowship">
                    </div>

                    {{-- Category (now a proper <select>) --}}
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="category" class="text-sm font-medium leading-none text-right">Category</label>
                        <select id="category" x-model="newCategory"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring col-span-3">
                            <option value="" disabled selected>Select category</option>
                            <option value="Education">Education</option>
                            <option value="Hospitality">Hospitality</option>
                            <option value="Technical">Technical</option>
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="description" class="text-sm font-medium leading-none text-right">Description</label>
                        <textarea id="description" x-model="newDescription"
                            class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring col-span-3"
                            placeholder="Describe this ministry..."></textarea>
                    </div>

                    {{-- Volunteers --}}
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="volunteers" class="text-sm font-medium leading-none text-right">Volunteers</label>
                        <input id="volunteers" type="number" x-model="newVolunteers"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring col-span-3"
                            placeholder="0">
                    </div>
                </div>

                {{-- Buttons: Cancel / Add Ministry --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                    <button
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:opacity-50 mt-2 sm:mt-0"
                        @click="showAddModal = false" type="button">
                        Cancel
                    </button>
                    <button
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md bg-primary text-primary-foreground px-4 py-2 text-sm font-medium shadow hover:bg-primary/90 focus:outline-none focus:ring-1 focus:ring-ring disabled:opacity-50 mb-2 sm:mb-0"
                        @click="
                            // In a real app you might push the new ministry into `ministries`
                            // or do an AJAX/POST. For this demo we simply close the modal:
                            showAddModal = false
                        "
                        type="button">
                        Add Ministry
                    </button>
                </div>

                {{-- “X” close button in top‐right corner --}}
                <button type="button"
                    class="absolute right-4 top-4 rounded-sm opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    @click="showAddModal = false">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                        <path
                            d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z"
                            fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
            </div>
        </div>

    </main>

</div>

@endsection
@section('scripts')
{{-- Alpine.js CDN --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection