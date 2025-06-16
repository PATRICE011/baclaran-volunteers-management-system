// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
    initializeEventListeners();
});

function initializeEventListeners() {
    // Get elements safely
    const categorySelector = document.getElementById("categorySelector");
    const searchInput = document.getElementById("searchQuery");
    const filterForm = document.getElementById("filterForm");
    const viewModal = document.getElementById("viewModal");

    // Attach category selector handler if exists
    if (categorySelector) {
        categorySelector.addEventListener("change", function () {
            if (filterForm) filterForm.submit();
        });
    }

    // Attach search input handler if exists
    if (searchInput && filterForm) {
        searchInput.addEventListener(
            "input",
            debounce(function () {
                filterForm.submit();
            }, 500)
        );
    }

    // Attach view modal handler if exists
    if (viewModal) {
        viewModal.addEventListener("click", function (e) {
            if (e.target === this) closeViewModal();
        });
    }
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

            const content = `
                <div class="max-w-6xl mx-auto p-6 space-y-8">
                    <!-- Header -->
                    <div class="border-b border-gray-200 pb-4">
                        <h2 class="text-2xl font-bold text-gray-900">${
                            ministry.full_path
                        }</h2>
                        <p class="text-sm text-gray-600 mt-1">${
                            ministry.category
                        } Ministry</p>
                    </div>

                    <!-- Ministry Overview Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h2M7 7h.01M7 3h5c1.11 0 2 .89 2 2v1"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Ministry Type</p>
                                    <p class="text-lg font-bold text-blue-900">${
                                        ministry.category
                                    }</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Total Volunteers</p>
                                    <p class="text-lg font-bold text-green-900">${
                                        ministry.volunteers
                                    }</p>
                                    <p class="text-xs text-green-600">Including sub-ministries</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600">Active Members</p>
                                    <p class="text-lg font-bold text-purple-900">${
                                        volunteers.filter(
                                            (v) => v.status === "Active"
                                        ).length
                                    }</p>
                                    <p class="text-xs text-purple-600">Currently serving</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Volunteers Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Ministry Volunteers</h3>
                                    <p class="text-sm text-gray-600 mt-1">${
                                        volunteers.length
                                    } members assigned to this ministry</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                        <span class="text-sm text-gray-600">Active</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                        <span class="text-sm text-gray-600">Inactive</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${
                            volunteers.length > 0
                                ? `
                            <div class="overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Volunteer
                                                </th>
                                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Specific Ministry
                                                </th>
                                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            ${volunteers
                                                .map(
                                                    (volunteer, index) => `
                                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                ${
                                                                    volunteer.profile_picture
                                                                        ? `
                                                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-blue-200" 
                                                                         src="${
                                                                             volunteer.profile_picture
                                                                         }" 
                                                                         alt="${
                                                                             volunteer.name
                                                                         }"
                                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center" style="display: none;">
                                                                        <span class="text-sm font-medium text-white">
                                                                            ${volunteer.name
                                                                                .split(
                                                                                    " "
                                                                                )
                                                                                .map(
                                                                                    (
                                                                                        n
                                                                                    ) =>
                                                                                        n[0]
                                                                                )
                                                                                .join(
                                                                                    ""
                                                                                )
                                                                                .toUpperCase()}
                                                                        </span>
                                                                    </div>
                                                                `
                                                                        : `
                                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                                        <span class="text-sm font-medium text-white">
                                                                            ${volunteer.name
                                                                                .split(
                                                                                    " "
                                                                                )
                                                                                .map(
                                                                                    (
                                                                                        n
                                                                                    ) =>
                                                                                        n[0]
                                                                                )
                                                                                .join(
                                                                                    ""
                                                                                )
                                                                                .toUpperCase()}
                                                                        </span>
                                                                    </div>
                                                                `
                                                                }
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900">${
                                                                    volunteer.name
                                                                }</div>
                                                                <div class="text-sm text-gray-500">Member #${String(
                                                                    index + 1
                                                                ).padStart(
                                                                    3,
                                                                    "0"
                                                                )}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">${
                                                            volunteer.ministry_name
                                                        }</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(
                                                            volunteer.status
                                                        )}">
                                                            <div class="w-2 h-2 rounded-full mr-2 ${
                                                                volunteer.status ===
                                                                "Active"
                                                                    ? "bg-green-400"
                                                                    : "bg-yellow-400"
                                                            }"></div>
                                                            ${volunteer.status}
                                                        </span>
                                                    </td>
                                                </tr>
                                            `
                                                )
                                                .join("")}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `
                                : `
                            <div class="text-center py-12">
                                <div class="mx-auto h-24 w-24 text-gray-300 mb-4">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No volunteers assigned</h3>
                                <p class="text-gray-500 mb-6 max-w-sm mx-auto">This ministry doesn't have any volunteers assigned.</p>
                            
                            </div>
                        `
                        }
                    </div>
                </div>
            `;

            document.getElementById("view-modal-content").innerHTML = content;
        } else {
            showErrorMessage("Failed to load ministry details");
        }
    } catch (error) {
        console.error("Error loading ministry details:", error);
        showErrorMessage("An error occurred while loading ministry details");
    }
}
// Get status color for volunteer status
function getStatusColor(status) {
    switch (status?.toLowerCase()) {
        case "active":
            return "bg-green-100 text-green-800";
        case "inactive":
            return "bg-red-100 text-red-800";
        case "pending":
            return "bg-yellow-100 text-yellow-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
}
// Modal functions
function showModal() {
    document.getElementById("ministryModal").classList.remove("hidden");
    document.getElementById("ministryModal").classList.add("flex");
    document.body.style.overflow = "hidden";
}

function showViewModal() {
    document.getElementById("viewModal").classList.remove("hidden");
    document.getElementById("viewModal").classList.add("flex");
    document.body.style.overflow = "hidden";
}

function closeViewModal() {
    document.getElementById("viewModal").classList.add("hidden");
    document.getElementById("viewModal").classList.remove("flex");
    document.body.style.overflow = "";
}

function showViewLoadingState() {
    document.getElementById("view-modal-content").innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="loading-spinner" style="display: block;"></div>
                <span class="ml-2 text-gray-600">Loading ministry details...</span>
            </div>
        `;
}

// Notification functions
function showSuccessMessage(message) {
    showNotification(message, "success");
}

function showErrorMessage(message) {
    showNotification(message, "error");
}

function showNotification(message, type) {
    // Remove existing notifications
    const existing = document.querySelector(".notification");
    if (existing) existing.remove();

    const notification = document.createElement("div");
    notification.className = `notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 ${
        type === "success" ? "bg-green-500 text-white" : "bg-red-500 text-white"
    }`;
    notification.style.transform = "translateX(100%)";
    notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${
                        type === "success"
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
        notification.style.transform = "translateX(0)";
    }, 100);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = "translateX(100%)";
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Add CSS for fade out animation
const style = document.createElement("style");
style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.95); }
        }
    `;
document.head.appendChild(style);
