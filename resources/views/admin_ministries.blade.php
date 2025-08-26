@extends('components.layout')

@section('title', 'Ministries')

@section('styles')
    <style>
        .modal-bg {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .fade-in {
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: #10b981;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .ministry-card {
            min-height: 208px;
            max-height: 208px;
        }
    </style>
@endsection

@section('content')
    @include('components.navs')

    <div class="md:ml-64">
        <main class="flex-1 overflow-auto p-4 sm:p-6">
            <div class="bg-background min-h-screen p-6">
                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Ministries Management</h1>
                    <p class="text-gray-600">
                        @if(Auth::user()->isStaff() && Auth::user()->ministry)
                            Viewing ministries under: {{ Auth::user()->ministry->ministry_name }}
                        @else
                            View church ministries and volunteer assignments
                        @endif
                    </p>
                </div>

                {{-- Search bar / Filters / Add Ministry Button --}}
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <form id="filterForm" method="GET" action="{{ route('ministries.index') }}"
                        class="flex flex-col lg:flex-row gap-4 items-center justify-between w-full">
                        {{-- Search input --}}
                        <div class="relative w-full max-w-md">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="absolute left-3 top-3 h-4 w-4 text-gray-400">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                            <input type="search" name="search" id="searchQuery" class="form-input pl-10"
                                value="{{ request('search') }}" placeholder="Search ministries...">
                        </div>

                        {{-- Category dropdown & Add Ministry --}}
                        <div class="flex items-center gap-4">
                            {{-- Category selector --}}
                            <select name="category" id="categorySelector" class="form-input min-w-[150px]">
                                <option value="All">All Categories</option>
                                @foreach($categories as $category)
                                    @if($category->ministry_type !== 'SUB_GROUP')
                                        <option value="{{ $category->ministry_type }}" {{ request('category') === $category->ministry_type ? 'selected' : '' }}>
                                            {{ ucwords(strtolower(str_replace('_', ' ', $category->ministry_type))) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                {{-- Grid of Ministry Cards --}}
                @if(!$showEmptyState)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="ministries-grid">
                        @foreach ($ministries as $ministry)
                                <div class="card-hover rounded-lg border bg-white shadow-sm overflow-hidden ministry-card h-52 flex flex-col"
                                    data-id="{{ $ministry->id }}" data-category="{{ $ministry->ministry_type }}" @if($ministry->parent)
                                    data-parent-type="{{ $ministry->parent->ministry_type }}" @endif
                                    data-name="{{ strtolower($ministry->ministry_name) }}">

                                    {{-- Card header - Fixed height --}}
                                    <div class="p-4 pb-3 flex-shrink-0">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="font-semibold text-base text-gray-900 mb-1 line-clamp-2 leading-tight">
                                                    {{ $ministry->ministry_name }}
                                                </h3>
                                                @if($ministry->parent)
                                                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                                        <span class="inline-flex items-center">
                                                            <svg class="w-2.5 h-2.5 mr-1 flex-shrink-0" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            <span class="truncate">Sub-ministry of
                                                                {{ $ministry->parent->ministry_name }}</span>
                                                        </span>
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="flex-shrink-0 ml-2">
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap">
                                                    {{
                            $ministry->ministry_type === 'SUB_GROUP' && $ministry->parent
                            ? ucwords(str_replace('_', ' ', strtolower($ministry->parent->ministry_type)))
                            : ucwords(str_replace('_', ' ', strtolower($ministry->ministry_type)))
                                        }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Stats section - Fixed height --}}
                                    <div class="px-4 pb-3 flex-shrink-0">
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center text-gray-600">
                                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                                </svg>
                                                <span class="font-semibold text-gray-900">{{ $ministry->total_volunteers }}</span>
                                                <span class="ml-1 text-gray-500">volunteers</span>
                                            </div>
                                            @if($ministry->children_count > 0)
                                                <div class="flex items-center text-gray-500">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="text-xs">{{ $ministry->children_count }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Spacer to push button to bottom --}}
                                    <div class="flex-grow"></div>

                                    {{-- Action buttons - Fixed at bottom --}}
                                    <div class="border-t bg-gray-50 px-4 py-3 flex-shrink-0">
                                        <button
                                            class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 w-full"
                                            onclick="viewMinistry({{ $ministry->id }})">
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            View Details
                                        </button>
                                    </div>
                                </div>
                        @endforeach
                    </div>
                @endif

                {{-- Empty state --}}
                @if($showEmptyState)
                    <div id="empty-state" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5a2 2 0 00-2 2v10a2 2 0 002 2h14m-9-8l2 2 4-4M15 20h14a2 2 0 002-2V8a2 2 0 00-2-2H15" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No ministries found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    </div>
                @endif

                {{-- Pagination --}}
                @if ($ministries->hasPages())
                    <div class="mt-10 flex justify-center">
                        {!! $ministries->appends(request()->query())->links() !!}
                    </div>
                @endif
            </div>
        </main>
    </div>

    {{-- View Ministry Details Modal --}}
    <div id="viewModal" class="modal-bg hidden items-center justify-center z-50">
        <div class="fade-in relative w-[90%] max-w-4xl rounded-xl bg-white shadow-2xl max-h-[90vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b">
                <h2 id="view-modal-title" class="text-xl font-semibold text-gray-900">Ministry Details</h2>
                <button type="button" onclick="closeViewModal()"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div id="view-modal-content" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('assets/js/ministries.js') }}"></script>
@endsection