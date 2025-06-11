let gridBtn;
let listBtn;

document.addEventListener("DOMContentLoaded", function () {
    gridBtn = document.getElementById("gridViewBtn");
    listBtn = document.getElementById("listViewBtn");

    const storedView = localStorage.getItem("volunteerViewType") || "grid";
    const urlParams = new URLSearchParams(window.location.search);
    const urlView = urlParams.get("view");
    const initialView = urlView || storedView;

    if (initialView === "list") {
        listBtn.classList.add("bg-blue-50", "border-blue-200");
        gridBtn.classList.remove("bg-blue-50", "border-blue-200");
    } else {
        gridBtn.classList.add("bg-blue-50", "border-blue-200");
        listBtn.classList.remove("bg-blue-50", "border-blue-200");
    }

    localStorage.setItem("volunteerViewType", initialView);
    switchView(initialView);
    attachVolunteerCardListeners();

    gridBtn.addEventListener("click", () => switchView("grid"));
    listBtn.addEventListener("click", () => switchView("list"));
});

document.getElementById("ministryFilter").addEventListener("change", applyFilters);
document.getElementById("statusFilter").addEventListener("change", applyFilters);

// Applies active filters and refreshes the view
async function applyFilters() {
    try {
        const currentView = localStorage.getItem("volunteerViewType") || "grid";
        showLoadingState();

        const searchQuery = document.getElementById("searchInput").value;
        const ministryFilter = document.getElementById("ministryFilter").value;
        const statusFilter = document.getElementById("statusFilter").value;

        const data = await fetchFilteredData(currentView, searchQuery, ministryFilter, statusFilter);

        if (data.success) {
            updateViewContent(currentView, data.html);
            attachVolunteerCardListeners();
            attachPaginationListeners();

            const url = new URL(window.location.href);
            url.searchParams.set("view", currentView);
            url.searchParams.delete("page");

            searchQuery ? url.searchParams.set("search", searchQuery) : url.searchParams.delete("search");
            ministryFilter ? url.searchParams.set("ministry", ministryFilter) : url.searchParams.delete("ministry");
            statusFilter ? url.searchParams.set("status", statusFilter) : url.searchParams.delete("status");

            window.history.pushState({}, "", url.toString());
        }
    } catch (error) {
        console.error("Error applying filters:", error);
        showErrorState(localStorage.getItem("volunteerViewType") || "grid");
    }
}

// Sends AJAX request with current filters and returns filtered data
async function fetchFilteredData(viewType, searchQuery = "", ministryFilter = "", statusFilter = "") {
    try {
        const url = new URL("/volunteers", window.location.origin);
        url.searchParams.set("view", viewType);
        if (searchQuery) url.searchParams.set("search", searchQuery);
        if (ministryFilter) url.searchParams.set("ministry", ministryFilter);
        if (statusFilter) url.searchParams.set("status", statusFilter);

        const response = await fetch(url.toString(), {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            credentials: "include",
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error("Error fetching filtered data:", error);
        throw error;
    }
}

// Displays loading animation in active view
function showLoadingState() {
    const loadingHTML = `<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2 text-gray-600">Loading...</span></div>`;
    const gridView = document.getElementById("gridView");
    const listView = document.getElementById("listView");

    if (gridView && !gridView.classList.contains("hidden")) gridView.innerHTML = loadingHTML;
    if (listView && !listView.classList.contains("hidden")) listView.innerHTML = loadingHTML;
}

// Loads and returns view content from server
async function fetchViewData(viewType, searchQuery = "") {
    try {
        const url = new URL("/volunteers", window.location.origin);
        url.searchParams.set("view", viewType);
        if (searchQuery) url.searchParams.set("search", searchQuery);

        const response = await fetch(url.toString(), {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            credentials: "include",
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error("Error fetching view data:", error);
        throw error;
    }
}

// Replaces current view (grid/list) with new HTML and updates UI
function updateViewContent(viewType, html) {
    const gridView = document.getElementById("gridView");
    const listView = document.getElementById("listView");

    if (viewType === "grid") {
        gridView.innerHTML = html;
        gridView.style.display = "grid";
        listView.style.display = "none";
        gridBtn.classList.add("bg-blue-50", "border-blue-200");
        gridBtn.classList.remove("bg-white");
        listBtn.classList.remove("bg-blue-50", "border-blue-200");
        listBtn.classList.add("bg-white");
    } else {
        listView.innerHTML = html;
        listView.style.display = "block";
        gridView.style.display = "none";
        listBtn.classList.add("bg-blue-50", "border-blue-200");
        listBtn.classList.remove("bg-white");
        gridBtn.classList.remove("bg-blue-50", "border-blue-200");
        gridBtn.classList.add("bg-white");
    }

    localStorage.setItem("volunteerViewType", viewType);
}

// Triggers full switch of view with AJAX and updates browser state
async function switchView(viewType) {
    try {
        showLoadingState();
        const searchQuery = document.getElementById("searchInput").value;
        const data = await fetchViewData(viewType, searchQuery);

        if (data.success) {
            updateViewContent(viewType, data.html);
            attachVolunteerCardListeners();
            attachPaginationListeners();

            const url = new URL(window.location.href);
            url.searchParams.set("view", viewType);
            url.searchParams.delete("page");
            window.history.pushState({}, "", url.toString());
        } else {
            throw new Error(data.message || "Failed to load view");
        }
    } catch (error) {
        console.error("Error switching view:", error);
        showErrorState(viewType);
    }
}

// Sets up all interactive handlers for dynamically loaded volunteer content
function attachVolunteerCardListeners() {
    document.querySelectorAll('[onclick^="editVolunteer"]').forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            const id = this.getAttribute("onclick").match(/'([^']+)'/)[1];
            editVolunteer(id);
        });
    });

    document.querySelectorAll('[onclick^="deleteVolunteer"]').forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            const id = this.getAttribute("onclick").match(/'([^']+)'/)[1];
            deleteVolunteer(id);
        });
    });

    attachPaginationListeners();
}

// Live search functionality with debounce
let searchTimeout;
document.getElementById("searchInput").addEventListener("input", function () {
    const searchQuery = this.value.trim();
    const currentView = localStorage.getItem("volunteerViewType") || "grid";

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        try {
            showLoadingState();
            const data = await fetchViewData(currentView, searchQuery);

            if (data.success) {
                updateViewContent(currentView, data.html);
                attachVolunteerCardListeners();
                attachPaginationListeners();

                const url = new URL(window.location.href);
                url.searchParams.delete("page");
                searchQuery ? url.searchParams.set("search", searchQuery) : url.searchParams.delete("search");
                window.history.replaceState({}, "", url);
            }
        } catch (err) {
            console.error("Search error:", err);
            showErrorState(currentView);
        }
    }, 300);
});

// Displays user-friendly error when AJAX fails
function showErrorState(viewType) {
    const errorHTML = `<div class="flex flex-col items-center justify-center py-12 text-red-600"><svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-lg font-medium">Error loading content</p><p class="text-sm">Please try again later.</p><button onclick="location.reload()" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Refresh Page</button></div>`;

    const target = viewType === "grid" ? "gridView" : "listView";
    document.getElementById(target).innerHTML = errorHTML;
}

// Re-load view state from URL when browser back/forward is used
window.addEventListener("popstate", function () {
    const params = new URLSearchParams(window.location.search);
    const viewType = params.get("view") || "grid";
    const searchQuery = params.get("search") || "";
    const ministryFilter = params.get("ministry") || "";
    const statusFilter = params.get("status") || "";

    document.getElementById("searchInput").value = searchQuery;
    document.getElementById("ministryFilter").value = ministryFilter;
    document.getElementById("statusFilter").value = statusFilter;

    switchView(viewType);
});

// Loads a specific page during pagination and updates browser URL
async function loadPageData(page, viewType, searchQuery = "") {
    try {
        showLoadingState();

        const url = new URL("/volunteers", window.location.origin);
        url.searchParams.set("view", viewType);
        url.searchParams.set("page", page);
        if (searchQuery) url.searchParams.set("search", searchQuery);

        const ministryFilter = document.getElementById("ministryFilter").value;
        const statusFilter = document.getElementById("statusFilter").value;

        if (ministryFilter) url.searchParams.set("ministry", ministryFilter);
        if (statusFilter) url.searchParams.set("status", statusFilter);

        const response = await fetch(url.toString(), {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            credentials: "include",
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            updateViewContent(viewType, data.html);
            attachVolunteerCardListeners();
            attachPaginationListeners();
        } else {
            throw new Error(data.message || "Failed to load page");
        }
    } catch (error) {
        console.error("Error loading page data:", error);
        showErrorState(viewType);
    }
}

// Handles pagination clicks and triggers data load
function attachPaginationListeners() {
    document.querySelectorAll(".pagination a").forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            const url = new URL(this.href);
            const page = url.searchParams.get("page");
            const currentView = localStorage.getItem("volunteerViewType") || "grid";
            const searchQuery = document.getElementById("searchInput").value;

            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set("page", page);
            if (searchQuery) currentUrl.searchParams.set("search", searchQuery);
            currentUrl.searchParams.set("view", currentView);

            window.history.pushState({}, "", currentUrl.toString());

            loadPageData(page, currentView, searchQuery);
        });
    });
}
