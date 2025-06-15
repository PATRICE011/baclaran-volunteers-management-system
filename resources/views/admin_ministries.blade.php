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
                <p class="text-gray-600">Manage church ministries and volunteer assignments</p>
            </div>

            {{-- Search bar / Filters / Add Ministry Button --}}
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <form id="filterForm" method="GET" action="{{ route('ministries.index') }}" class="flex flex-col lg:flex-row gap-4 items-center justify-between w-full">
                    {{-- Search input --}}
                    <div class="relative w-full max-w-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="absolute left-3 top-3 h-4 w-4 text-gray-400">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                        <input type="search" name="search" id="searchQuery"
                            class="form-input pl-10"
                            value="{{ request('search') }}"
                            placeholder="Search ministries...">
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

                        {{-- Add Ministry button --}}
                        <button type="button" class="btn-primary inline-flex items-center justify-center whitespace-nowrap rounded-lg text-white text-sm font-medium h-10 px-6 py-2"
                            onclick="openAddModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2 h-4 w-4">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            Add Ministry
                        </button>
                    </div>
                </form>
            </div>


            {{-- Grid of Ministry Cards --}}
            @if(!$showEmptyState)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="ministries-grid">
                @foreach ($ministries as $ministry)
                <div class="card-hover rounded-xl border bg-white shadow-sm overflow-hidden ministry-card"
                    data-id="{{ $ministry->id }}"
                    data-category="{{ $ministry->ministry_type }}"
                    @if($ministry->parent)
                    data-parent-type="{{ $ministry->parent->ministry_type }}"
                    @endif
                    data-name="{{ strtolower($ministry->ministry_name) }}">
                    {{-- Card header --}}
                    <div class="p-6 pb-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900 mb-1">{{ $ministry->ministry_name }}</h3>
                                @if($ministry->parent)
                                <p class="text-xs text-gray-400 mt-1">
                                    <span class="inline-flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Sub-ministry of {{ $ministry->parent->ministry_name }}
                                    </span>
                                </p>
                                @endif
                            </div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800">
                                {{
                        $ministry->ministry_type === 'SUB_GROUP' && $ministry->parent
                            ? ucwords(str_replace('_', ' ', strtolower($ministry->parent->ministry_type)))
                            : ucwords(str_replace('_', ' ', strtolower($ministry->ministry_type)))
                    }}
                            </span>
                        </div>
                    </div>

                    {{-- Stats section --}}
                    <div class="px-6 pb-4">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                <span class="font-medium">{{ $ministry->total_volunteers }}</span>
                                <span class="ml-1">volunteers</span>
                            </div>
                            @if($ministry->children_count > 0)
                            <div class="flex items-center text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs">{{ $ministry->children_count }} sub-ministries</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="border-t bg-gray-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <button
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                                onclick="viewMinistry({{ $ministry->id }})">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                View Details
                            </button>
                            {{-- Remove Edit Button --}}
                            <div class="flex gap-1">
                                {{-- Keep only Delete button --}}
                                <button
                                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors"
                                   >
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Archive
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Empty state --}}
            @if($showEmptyState)
            <div id="empty-state" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5a2 2 0 00-2 2v10a2 2 0 002 2h14m-9-8l2 2 4-4M15 20h14a2 2 0 002-2V8a2 2 0 00-2-2H15" />
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

{{-- Add/Edit Ministry Modal --}}
<div id="ministryModal" class="modal-bg hidden items-center justify-center z-50">
    <div class="fade-in relative w-[90%] max-w-2xl rounded-xl bg-white shadow-2xl max-h-[90vh] overflow-y-auto">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-6 border-b">
            <h2 id="modal-title" class="text-xl font-semibold text-gray-900">Add Ministry</h2>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6">
            <form id="ministryForm">
                <input type="hidden" id="ministry_id" name="ministry_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group md:col-span-2">
                        <label for="ministry_name" class="form-label">Ministry Name *</label>
                        <input type="text" id="ministry_name" name="ministry_name" class="form-input" required>
                        <div id="ministry_name_error" class="error-message hidden"></div>
                    </div>

                    <div class="form-group">
                        <label for="ministry_code" class="form-label">Ministry Code</label>
                        <input type="text" id="ministry_code" name="ministry_code" class="form-input" maxlength="20">
                        <div id="ministry_code_error" class="error-message hidden"></div>
                    </div>

                    <div class="form-group">
                        <label for="ministry_type" class="form-label">Ministry Type *</label>
                        <select id="ministry_type" name="ministry_type" class="form-input" required>
                            <option value="">Select Type</option>
                            <option value="LITURGICAL">Liturgical</option>
                            <option value="PASTORAL">Pastoral</option>
                            <option value="SOCIAL_MISSION">Social Mission</option>
                            <option value="SUB_GROUP">Sub Group</option>
                        </select>
                        <div id="ministry_type_error" class="error-message hidden"></div>
                    </div>

                    <div class="form-group md:col-span-2">
                        <label for="parent_id" class="form-label">Parent Ministry (Optional)</label>
                        <select id="parent_id" name="parent_id" class="form-input">
                            <option value="">No Parent (Top Level)</option>
                        </select>
                        <div id="parent_id_error" class="error-message hidden"></div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submit-btn"
                        class="btn-primary inline-flex items-center px-6 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <div class="loading-spinner mr-2"></div>
                        <span id="submit-text">Save Ministry</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Ministry Details Modal --}}
<div id="viewModal" class="modal-bg hidden items-center justify-center z-50">
    <div class="fade-in relative w-[90%] max-w-4xl rounded-xl bg-white shadow-2xl max-h-[90vh] overflow-y-auto">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-6 border-b">
            <h2 id="view-modal-title" class="text-xl font-semibold text-gray-900">Ministry Details</h2>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
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
<script>
    let isEditing = false;
    let currentMinistryId = null;

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadParentMinistries();
        initializeEventListeners();
    });

    function initializeEventListeners() {
        document.getElementById('categorySelector')?.addEventListener('change', function() {
            document.getElementById('filterForm')?.submit();
        });

        // Debounced search submit
        const searchInput = document.getElementById('searchQuery');
        searchInput.addEventListener('input', debounce(function() {
            document.getElementById('filterForm').submit();
        }, 500));

        // Form submission
        document.getElementById('ministryForm').addEventListener('submit', handleFormSubmit);

        // Close modals on backdrop click
        document.getElementById('ministryModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.getElementById('viewModal').addEventListener('click', function(e) {
            if (e.target === this) closeViewModal();
        });
    }

    // Debounce function for search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }


    // Load parent ministries for dropdown
    async function loadParentMinistries() {
        try {
            const response = await fetch('/ministries/parents/list');
            const data = await response.json();

            if (data.success) {
                const parentSelect = document.getElementById('parent_id');
                parentSelect.innerHTML = '<option value="">No Parent (Top Level)</option>';

                data.parents.forEach(parent => {
                    const option = document.createElement('option');
                    option.value = parent.id;
                    option.textContent = `${parent.ministry_name} (${parent.ministry_type})`;
                    parentSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading parent ministries:', error);
        }
    }

    // Open Add Ministry Modal
    function openAddModal() {
        isEditing = false;
        currentMinistryId = null;
        document.getElementById('modal-title').textContent = 'Add New Ministry';
        document.getElementById('submit-text').textContent = 'Save Ministry';
        resetForm();
        showModal();
    }

    // Open Edit Ministry Modal
    async function openEditModal(ministryId) {
        try {
            showLoadingState();

            const response = await fetch(`/ministries/${ministryId}`);
            const data = await response.json();

            if (data.success) {
                isEditing = true;
                currentMinistryId = ministryId;
                document.getElementById('modal-title').textContent = 'Edit Ministry';
                document.getElementById('submit-text').textContent = 'Update Ministry';

                // Populate form
                document.getElementById('ministry_id').value = ministryId;
                document.getElementById('ministry_name').value = data.ministry.name;
                document.getElementById('ministry_code').value = data.ministry.code || '';
                document.getElementById('ministry_type').value = data.ministry.type;
                document.getElementById('parent_id').value = data.ministry.parent_id || '';

                showModal();
            } else {
                showErrorMessage('Failed to load ministry data');
            }
        } catch (error) {
            console.error('Error loading ministry:', error);
            showErrorMessage('An error occurred while loading ministry data');
        } finally {
            hideLoadingState();
        }
    }

    // View Ministry Details
    async function viewMinistry(ministryId) {
        try {
            showViewModal();
            showViewLoadingState();

            const response = await fetch(`/ministries/${ministryId}`);
            const data = await response.json();

            if (data.success) {
                const ministry = data.ministry;
                const volunteers = data.volunteers;

                document.getElementById('view-modal-title').textContent = ministry.name;

                const content = `
                <div class="space-y-6">
                    <!-- Ministry Info -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ministry Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Full Path</label>
                                <p class="text-gray-900">${ministry.full_path}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Type</label>
                                <p class="text-gray-900">${ministry.category}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Volunteers</label>
                                <p class="text-gray-900">${ministry.volunteers} total (including sub-ministries)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Volunteers Section -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Assigned Volunteers (${volunteers.length})</h3>
                        </div>
                        
                        ${volunteers.length > 0 ? `
                            <div class="bg-white border rounded-lg overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ministry</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            ${volunteers.map(volunteer => `
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">${volunteer.name}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${volunteer.ministry_name}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(volunteer.status)}">
                                                            ${volunteer.status}
                                                        </span>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        ` : `
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5m-2.5-6v12M9 11v6"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No volunteers assigned</h3>
                                <p class="mt-1 text-sm text-gray-500">This ministry doesn't have any volunteers assigned yet.</p>
                            </div>
                        `}
                    </div>
                </div>
            `;

                document.getElementById('view-modal-content').innerHTML = content;
            } else {
                showErrorMessage('Failed to load ministry details');
            }
        } catch (error) {
            console.error('Error loading ministry details:', error);
            showErrorMessage('An error occurred while loading ministry details');
        }
    }

    // Get status color for volunteer status
    function getStatusColor(status) {
        switch (status?.toLowerCase()) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'inactive':
                return 'bg-red-100 text-red-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    // Handle form submission
    async function handleFormSubmit(e) {
        e.preventDefault();

        if (document.querySelector('.loading-spinner').style.display !== 'none') {
            return; // Prevent double submission
        }

        showLoadingState();
        clearErrors();

        try {
            const formData = new FormData(e.target);
            const url = isEditing ? `/ministries/${currentMinistryId}` : '/ministries';
            const method = isEditing ? 'PUT' : 'POST';

            // Convert FormData to regular object for PUT requests
            const data = {};
            for (let [key, value] of formData.entries()) {
                if (value) data[key] = value;
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showSuccessMessage(result.message);
                closeModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                if (result.errors) {
                    displayErrors(result.errors);
                } else {
                    showErrorMessage(result.message || 'An error occurred');
                }
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showErrorMessage('An error occurred while saving the ministry');
        } finally {
            hideLoadingState();
        }
    }

    // Modal functions
    function showModal() {
        document.getElementById('ministryModal').classList.remove('hidden');
        document.getElementById('ministryModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('ministryModal').classList.add('hidden');
        document.getElementById('ministryModal').classList.remove('flex');
        document.body.style.overflow = '';
        resetForm();
    }

    function showViewModal() {
        document.getElementById('viewModal').classList.remove('hidden');
        document.getElementById('viewModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.add('hidden');
        document.getElementById('viewModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    function showViewLoadingState() {
        document.getElementById('view-modal-content').innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="loading-spinner" style="display: block;"></div>
                <span class="ml-2 text-gray-600">Loading ministry details...</span>
            </div>
        `;
    }

    // Form helper functions
    function resetForm() {
        document.getElementById('ministryForm').reset();
        document.getElementById('ministry_id').value = '';
        clearErrors();
    }

    function clearErrors() {
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });

        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.classList.remove('border-red-500');
        });
    }

    function displayErrors(errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(`${field}_error`);
            const inputElement = document.getElementById(field);

            if (errorElement && inputElement) {
                errorElement.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                errorElement.classList.remove('hidden');
                inputElement.classList.add('border-red-500');
            }
        });
    }

    function showLoadingState() {
        const spinner = document.querySelector('.loading-spinner');
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');

        spinner.style.display = 'block';
        submitBtn.disabled = true;
        submitText.textContent = isEditing ? 'Updating...' : 'Saving...';
    }

    function hideLoadingState() {
        const spinner = document.querySelector('.loading-spinner');
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');

        spinner.style.display = 'none';
        submitBtn.disabled = false;
        submitText.textContent = isEditing ? 'Update Ministry' : 'Save Ministry';
    }

    // Notification functions
    function showSuccessMessage(message) {
        showNotification(message, 'success');
    }

    function showErrorMessage(message) {
        showNotification(message, 'error');
    }

    function showNotification(message, type) {
        // Remove existing notifications
        const existing = document.querySelector('.notification');
        if (existing) existing.remove();

        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.style.transform = 'translateX(100%)';
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${type === 'success' 
                        ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                        : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    // Add CSS for fade out animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.95); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection