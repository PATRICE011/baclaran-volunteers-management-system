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

    // Attach click listeners
    gridBtn.addEventListener("click", () => switchView("grid"));
    listBtn.addEventListener("click", () => switchView("list"));
});
// Handle ministry filter changes
document
    .getElementById("ministryFilter")
    .addEventListener("change", function () {
        applyFilters();
    });

// Handle status filter changes
document.getElementById("statusFilter").addEventListener("change", function () {
    applyFilters();
});

// Function to apply all active filters
async function applyFilters() {
    try {
        const currentView = localStorage.getItem("volunteerViewType") || "grid";
        showLoadingState();

        const searchQuery = document.getElementById("searchInput").value;
        const ministryFilter = document.getElementById("ministryFilter").value;
        const statusFilter = document.getElementById("statusFilter").value;

        const data = await fetchFilteredData(
            currentView,
            searchQuery,
            ministryFilter,
            statusFilter
        );

        if (data.success) {
            updateViewContent(currentView, data.html);
            attachVolunteerCardListeners();
            attachPaginationListeners();

            // Update URL without page refresh
            const url = new URL(window.location.href);
            url.searchParams.set("view", currentView);
            url.searchParams.delete("page"); // Reset to page 1 when changing filters

            if (searchQuery) {
                url.searchParams.set("search", searchQuery);
            } else {
                url.searchParams.delete("search");
            }

            if (ministryFilter) {
                url.searchParams.set("ministry", ministryFilter);
            } else {
                url.searchParams.delete("ministry");
            }

            if (statusFilter) {
                url.searchParams.set("status", statusFilter);
            } else {
                url.searchParams.delete("status");
            }

            window.history.pushState({}, "", url.toString());
        }
    } catch (error) {
        console.error("Error applying filters:", error);
        const currentView = localStorage.getItem("volunteerViewType") || "grid";
        showErrorState(currentView);
    }
}

// Function to fetch data with filters
async function fetchFilteredData(
    viewType,
    searchQuery = "",
    ministryFilter = "",
    statusFilter = ""
) {
    try {
        const url = new URL("/volunteers", window.location.origin);
        url.searchParams.set("view", viewType);

        if (searchQuery) {
            url.searchParams.set("search", searchQuery);
        }

        if (ministryFilter) {
            url.searchParams.set("ministry", ministryFilter);
        }

        if (statusFilter) {
            url.searchParams.set("status", statusFilter);
        }

        const response = await fetch(url.toString(), {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            credentials: "include",
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(
                errorData.message || `HTTP error! status: ${response.status}`
            );
        }

        return await response.json();
    } catch (error) {
        console.error("Error fetching filtered data:", error);
        throw error;
    }
}
// Function to show loading state
function showLoadingState() {
    const loadingHTML = `
    <div class="flex items-center justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Loading...</span>
    </div>
  `;

    // Replace current view content with loading
    const gridView = document.getElementById("gridView");
    const listView = document.getElementById("listView");

    if (gridView && !gridView.classList.contains("hidden")) {
        gridView.innerHTML = loadingHTML;
    }
    if (listView && !listView.classList.contains("hidden")) {
        listView.innerHTML = loadingHTML;
    }
}

// Function to fetch view data
async function fetchViewData(viewType, searchQuery = "") {
    try {
        // Use the correct URL (matches your route)
        const url = new URL("/volunteers", window.location.origin);
        url.searchParams.set("view", viewType);
        if (searchQuery) {
            url.searchParams.set("search", searchQuery);
        }

        const response = await fetch(url.toString(), {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            credentials: "include", // This sends cookies/session for auth
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(
                errorData.message || `HTTP error! status: ${response.status}`
            );
        }

        return await response.json();
    } catch (error) {
        console.error("Error fetching view data:", error);
        throw error;
    }
}

// Function to update view content
function updateViewContent(viewType, html) {
    const gridView = document.getElementById("gridView");
    const listView = document.getElementById("listView");

    if (viewType === "grid") {
        gridView.innerHTML = html;
        gridView.style.display = "grid";
        listView.style.display = "none";

        // Update button styles
        gridBtn.classList.add("bg-blue-50", "border-blue-200");
        gridBtn.classList.remove("bg-white");
        listBtn.classList.remove("bg-blue-50", "border-blue-200");
        listBtn.classList.add("bg-white");
    } else {
        listView.innerHTML = html;
        listView.style.display = "block";
        gridView.style.display = "none";

        // Update button styles
        listBtn.classList.add("bg-blue-50", "border-blue-200");
        listBtn.classList.remove("bg-white");
        gridBtn.classList.remove("bg-blue-50", "border-blue-200");
        gridBtn.classList.add("bg-white");
    }

    // Store current view in localStorage for persistence
    localStorage.setItem("volunteerViewType", viewType);
}

// Function to handle view switching
async function switchView(viewType) {
    try {
        showLoadingState();

        const searchQuery = document.getElementById("searchInput").value;
        const data = await fetchViewData(viewType, searchQuery);

        if (data.success) {
            updateViewContent(viewType, data.html);
            attachVolunteerCardListeners();
            attachPaginationListeners(); // ← Add this line
            // Update URL without page refresh
            const url = new URL(window.location.href);
            url.searchParams.set("view", viewType);
            url.searchParams.delete("page"); // Reset to page 1 when switching views
            window.history.pushState({}, "", url.toString());

            // Re-attach event listeners for new content
            attachVolunteerCardListeners();
            attachPaginationListeners(); // ← Add this line
        } else {
            throw new Error(data.message || "Failed to load view");
        }
    } catch (error) {
        console.error("Error switching view:", error);
        showErrorState(viewType);
    }
}

// Function to re-attach event listeners after AJAX load
function attachVolunteerCardListeners() {
    // Re-attach click listeners for volunteer cards/rows
    document
        .querySelectorAll(".volunteer-card, .volunteer-row td[onclick]")
        .forEach((element) => {
            // The onclick attributes should still work, but you can also add listeners here if needed
        });

    // Re-attach action button listeners
    document.querySelectorAll('[onclick^="editVolunteer"]').forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            const volunteerId =
                this.getAttribute("onclick").match(/'([^']+)'/)[1];
            editVolunteer(volunteerId);
        });
    });

    document.querySelectorAll('[onclick^="deleteVolunteer"]').forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            const volunteerId =
                this.getAttribute("onclick").match(/'([^']+)'/)[1];
            deleteVolunteer(volunteerId);
        });
    });

    // ← Add this line to attach pagination listeners every time content is loaded
    attachPaginationListeners();
}

// Enhanced search with AJAX
/* --- Enhanced search with AJAX (single source of truth) --- */
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
                attachPaginationListeners(); // ← Add this line

                // Update the address bar
                const url = new URL(window.location.href);
                url.searchParams.delete("page"); // Reset to page 1 when searching
                if (searchQuery === "") {
                    url.searchParams.delete("search");
                } else {
                    url.searchParams.set("search", searchQuery);
                }
                window.history.replaceState({}, "", url);
            }
        } catch (err) {
            console.error("Search error:", err);
            showErrorState(currentView);
        }
    }, 300);
});

// Add error state function
function showErrorState(viewType) {
    const errorHTML = `
    <div class="flex flex-col items-center justify-center py-12 text-red-600">
      <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <p class="text-lg font-medium">Error loading content</p>
      <p class="text-sm">Please try again later.</p>
      <button onclick="location.reload()" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Refresh Page
      </button>
    </div>
  `;

    if (viewType === "grid") {
        document.getElementById("gridView").innerHTML = errorHTML;
    } else {
        document.getElementById("listView").innerHTML = errorHTML;
    }
}
// Handle browser back/forward buttons
window.addEventListener("popstate", function (event) {
    const urlParams = new URLSearchParams(window.location.search);
    const viewType = urlParams.get("view") || "grid";
    const searchQuery = urlParams.get("search") || "";
    const ministryFilter = urlParams.get("ministry") || "";
    const statusFilter = urlParams.get("status") || "";

    document.getElementById("searchInput").value = searchQuery;
    document.getElementById("ministryFilter").value = ministryFilter;
    document.getElementById("statusFilter").value = statusFilter;

    switchView(viewType);
});
// Registration modal toggle
document.getElementById("addVolunteerBtn").addEventListener("click", () => {
    document
        .getElementById("registrationModal")
        .classList.replace("hidden", "flex");
});
["closeRegistration", "cancelRegistration"].forEach((id) =>
    document.getElementById(id).addEventListener("click", () => {
        document
            .getElementById("registrationModal")
            .classList.replace("flex", "hidden");
        resetVolunteerForm(); // ← Add this line
        resetModalTabs(); // ← And this if you want to go back to the Basic Info tab
    })
);
function resetModalTabs() {
    // Reset tab view
    document.querySelectorAll(".reg-tab").forEach((tab) => {
        tab.classList.remove("border-blue-600");
        tab.classList.add("border-transparent");

        if (tab.dataset.tab === "personal") {
            tab.classList.add("border-blue-600");
            tab.classList.remove("tab-locked");
        } else {
            tab.classList.add("tab-locked");
        }
    });

    // Reset tab content
    document.querySelectorAll(".reg-content").forEach((content) => {
        content.classList.add("hidden");
    });
    document.getElementById("tab-personal").classList.remove("hidden");

    // Show "Next", hide "Register" button
    document.getElementById("nextToSheet").classList.remove("hidden");
    document.getElementById("submitRegistration").classList.add("hidden");
}
document
    .getElementById("reg-applied-date")
    .addEventListener("change", function () {
        const appliedDate = new Date(this.value + "-01");
        const now = new Date();

        if (!isNaN(appliedDate)) {
            const totalMonths =
                (now.getFullYear() - appliedDate.getFullYear()) * 12 +
                (now.getMonth() - appliedDate.getMonth());

            const years = Math.floor(totalMonths / 12);
            const months = totalMonths % 12;

            let result = "";
            if (years > 0) result += `${years} yr${years > 1 ? "s" : ""}`;
            if (months > 0)
                result += `${years > 0 ? " " : ""}${months} mo${
                    months > 1 ? "s" : ""
                }`;

            document.getElementById("reg-regular-duration").value =
                result || "0";
        }
    });

document.getElementById("submitRegistration").addEventListener("click", () => {
    const formData = new FormData();

    // Profile Picture
    const profilePictureInput = document.querySelector(
        '[name="profile_picture"]'
    );
    if (profilePictureInput.files[0]) {
        formData.append("profile_picture", profilePictureInput.files[0]);
    }

    // Basic Info
    formData.append(
        "nickname",
        document.querySelector('[name="nickname"]').value
    );
    formData.append("dob", document.querySelector('[name="dob"]').value);
    formData.append(
        "sex",
        document.querySelector('[name="sex"]:checked')?.value || ""
    );
    formData.append(
        "address",
        document.querySelector('[name="address"]').value
    );
    formData.append("phone", document.querySelector('[name="phone"]').value);
    formData.append("email", document.querySelector('[name="email"]').value);
    formData.append(
        "occupation",
        document.querySelector('[name="occupation"]').value
    );

    // ✅ Civil status with dynamic handling of "Others"
    const civilStatusVal =
        document.querySelector('[name="civil_status"]:checked')?.value || "";
    formData.append("civil_status", civilStatusVal);
    if (civilStatusVal === "others") {
        formData.append(
            "civil_status_other",
            document.querySelector('[name="civil_status_other"]').value
        );
    }

    document
        .querySelectorAll('input[name="sacraments[]"]:checked')
        .forEach((cb) => formData.append("sacraments[]", cb.value));
    document
        .querySelectorAll('input[name="formations[]"]:checked')
        .forEach((cb) => formData.append("formations[]", cb.value));

    // Info Sheet
    formData.append(
        "ministry_id",
        document.querySelector('[name="ministry_id"]').value
    );
    formData.append(
        "applied_date",
        document.querySelector('[name="applied_date"]').value
    );
    formData.append(
        "regular_duration",
        document.querySelector('[name="regular_duration"]').value
    );
    formData.append(
        "last_name",
        document.querySelector('[name="last_name"]').value
    );
    formData.append(
        "first_name",
        document.querySelector('[name="first_name"]').value
    );
    formData.append(
        "middle_initial",
        document.querySelector('[name="middle_initial"]').value
    );

    document
        .querySelectorAll('input[name="timeline_org[]"]')
        .forEach((el, i) => {
            formData.append(`timeline_org[${i}]`, el.value);
        });
    document
        .querySelectorAll('input[name="timeline_years[]"]')
        .forEach((el, i) => {
            formData.append(`timeline_years[${i}]`, el.value);
        });
    document
        .querySelectorAll('input[name="timeline_total[]"]')
        .forEach((el, i) => {
            formData.append(`timeline_total[${i}]`, el.value);
        });
    document
        .querySelectorAll('select[name="timeline_active[]"]')
        .forEach((el, i) => {
            formData.append(`timeline_active[${i}]`, el.value);
        });

    document.querySelectorAll('input[name="affil_org[]"]').forEach((el, i) => {
        formData.append(`affil_org[${i}]`, el.value);
    });
    document
        .querySelectorAll('input[name="affil_years[]"]')
        .forEach((el, i) => {
            formData.append(`affil_years[${i}]`, el.value);
        });
    document
        .querySelectorAll('select[name="affil_active[]"]')
        .forEach((el, i) => {
            formData.append(`affil_active[${i}]`, el.value);
        });

    fetch("/volunteers/register", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: formData,
    })
        .then((res) =>
            res.json().then((data) => ({
                ok: res.ok,
                status: res.status,
                body: data,
            }))
        )
        .then(({ ok, status, body }) => {
            if (ok) {
                toastr.success(body.message);
                document
                    .getElementById("registrationModal")
                    .classList.replace("flex", "hidden");
                resetVolunteerForm();
                const currentView =
                    localStorage.getItem("volunteerViewType") || "grid";
                switchView(currentView);
            } else {
                if (status === 409) {
                    toastr.warning(
                        body.message || "Duplicate volunteer found."
                    );
                } else if (status === 422) {
                    // Handle validation errors
                    if (body.errors && body.errors.profile_picture) {
                        toastr.error(body.errors.profile_picture[0]);
                    } else {
                        toastr.error(
                            body.message || "Validation error occurred."
                        );
                    }
                } else {
                    toastr.error(
                        body.message || "An error occurred during registration."
                    );
                }
            }
        })
        .catch((err) => {
            toastr.error("An error occurred while registering the volunteer.");
            console.error(err);
        });
});

function resetVolunteerForm() {
    // Reset text, email, date, number, textarea, select
    document
        .querySelectorAll(
            '#registrationModal input[type="text"], ' +
                '#registrationModal input[type="email"], ' +
                '#registrationModal input[type="tel"], ' +
                '#registrationModal input[type="date"], ' +
                '#registrationModal input[type="month"], ' +
                '#registrationModal input[type="file"], ' +
                "#registrationModal textarea, " +
                "#registrationModal select"
        )
        .forEach((el) => (el.value = ""));

    // Reset radio and checkboxes
    document
        .querySelectorAll(
            '#registrationModal input[type="checkbox"], #registrationModal input[type="radio"]'
        )
        .forEach((el) => (el.checked = false));

    // Reset profile picture preview
    const profilePreview = document.getElementById("profilePreview");
    const removeBtn = document.getElementById("removeProfilePicture");
    if (profilePreview) {
        profilePreview.src =
            "data:image/svg+xml,%3csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100' height='100' fill='%23f3f4f6'/%3e%3ctext x='50%25' y='50%25' font-size='14' text-anchor='middle' alignment-baseline='middle' fill='%23374151'%3eNo Image%3c/text%3e%3c/svg%3e";
    }
    if (removeBtn) {
        removeBtn.classList.add("hidden");
    }

    // Reset dynamic "civil status other" field visibility
    const otherInput = document.getElementById("civilOtherInput");
    if (otherInput) otherInput.classList.add("hidden");

    // Reset tab view to Basic Info
    document.querySelectorAll(".reg-tab").forEach((tab, i) => {
        if (i === 0) {
            tab.classList.replace("border-transparent", "border-blue-600");
        } else {
            tab.classList.replace("border-blue-600", "border-transparent");
            tab.classList.add("tab-locked");
        }
    });

    document.querySelectorAll(".reg-content").forEach((content, i) => {
        if (i === 0) {
            content.classList.remove("hidden");
        } else {
            content.classList.add("hidden");
        }
    });

    // Reset visibility of buttons
    document.getElementById("nextToSheet")?.classList.remove("hidden");
    document.getElementById("submitRegistration")?.classList.add("hidden");

    // Scroll modal to top
    document
        .querySelector("#registrationModal .overflow-y-auto")
        ?.scrollTo(0, 0);
}
document.querySelectorAll(".year-select").forEach((select) => {
    select.addEventListener("change", function () {
        const row = this.dataset.row;
        const startYear = parseInt(
            document.querySelector(
                `select[name="timeline_start_year[]"][data-row="${row}"]`
            ).value
        );
        const endYear = parseInt(
            document.querySelector(
                `select[name="timeline_end_year[]"][data-row="${row}"]`
            ).value
        );
        const totalInput = document.querySelectorAll(
            'input[name="timeline_total[]"]'
        )[row];
        if (!isNaN(startYear) && !isNaN(endYear)) {
            const total = endYear >= startYear ? endYear - startYear : 0;
            totalInput.value = `${total} year${total !== 1 ? "s" : ""}`;
        } else {
            totalInput.value = "";
        }
    });
});

// Tabs switching
document.querySelectorAll(".reg-tab").forEach((tab) => {
    tab.addEventListener("click", function () {
        // Block tab if it’s locked
        if (this.classList.contains("tab-locked")) return;

        // Visual and content switching
        document
            .querySelectorAll(".reg-tab")
            .forEach((t) =>
                t.classList.replace("border-blue-600", "border-transparent")
            );
        this.classList.replace("border-transparent", "border-blue-600");

        document
            .querySelectorAll(".reg-content")
            .forEach((c) => c.classList.add("hidden"));
        document
            .getElementById("tab-" + this.dataset.tab)
            .classList.remove("hidden");
    });
});

// Profile modal
function openProfile(id) {
    // Show loading state
    const profileContent = document.getElementById("profileContent");
    profileContent.innerHTML = `
    <div class="flex items-center justify-center py-16">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-3 border-blue-600 mx-auto mb-4"></div>
        <span class="text-lg text-gray-600">Loading profile...</span>
      </div>
    </div>
  `;

    // Show modal immediately with loading state
    document.getElementById("profileModal").classList.replace("hidden", "flex");

    // Fetch volunteer data from backend
    fetch(`/volunteers/${id}`, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Failed to fetch volunteer data");
            }
            return response.json();
        })
        .then((volunteer) => {
            // Generate avatar seed from name
            const displayName =
                volunteer.nickname || volunteer.detail?.full_name || "No Name";
            const avatarSeed = displayName.replace(/\s/g, "").toLowerCase();
            const avatarUrl = `https://api.dicebear.com/7.x/avataaars/svg?seed=${avatarSeed}`;

            // Format join date
            const joinDateStr = volunteer.detail?.applied_month_year;
            const joinDate = joinDateStr
                ? new Date(
                      joinDateStr + (joinDateStr.length === 7 ? "-01" : "")
                  )
                : new Date();

            const currentDate = new Date();

            // Calculate the number of years and months active
            const diffInYears =
                currentDate.getFullYear() - joinDate.getFullYear();
            const diffInMonths = currentDate.getMonth() - joinDate.getMonth();
            const activeYears = diffInYears - (diffInMonths < 0 ? 1 : 0);
            const activeMonths = (diffInMonths + 12) % 12;

            const activeTime = `${activeYears} year${
                activeYears !== 1 ? "s" : ""
            } ${activeMonths} month${activeMonths !== 1 ? "s" : ""}`;

            // Get ministry information
            const ministryName =
                volunteer.detail?.ministry?.ministry_name ||
                "No Ministry Assigned";

            // Get profile completion status
            const volunteerStatus =
                volunteer.detail?.volunteer_status || "No Status";
            const statusClass =
                volunteerStatus === "Active"
                    ? "bg-emerald-100 text-emerald-800 border-emerald-200"
                    : "bg-red-100 text-red-800 border-red-200";

            // Format sacraments
            const sacraments = volunteer.detail?.sacraments
                ? volunteer.detail.sacraments
                      .split(",")
                      .map((s) => s.trim())
                      .filter((s) => s)
                : [];

            // Format formations
            const formations = volunteer.detail?.formations
                ? volunteer.detail.formations
                      .split(",")
                      .map((f) => f.trim())
                      .filter((f) => f)
                : [];

            // Build the profile HTML with improved design
            const html = `
      <!-- Header Section -->
       <div class="bg-gradient-to-r from-blue-50 to-indigo-50 -m-6 mb-8 p-8 rounded-t-lg border-b border-gray-100">
    <div class="flex items-start gap-6">
      <div class="relative">
    <img src="${avatarUrl}" alt="${displayName}" class="w-24 h-24 rounded-full shadow-lg ring-4 ring-white">
    ${
        volunteerStatus === "Active"
            ? `
        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
        </svg>
        </div>
    `
            : ""
    }
    </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">${displayName}</h2>
        <div class="flex flex-wrap items-center gap-3 mb-3">
          <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border ${statusClass}">
            <span class="w-2 h-2 bg-current rounded-full mr-2"></span>
           ${volunteerStatus}
          </span>
          <span class="inline-flex items-center px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-full border border-blue-200">
            ${ministryName}
          </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600">
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
            </svg>
            Joined  ${joinDate.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
            })}

          </div>
          <div class="flex items-center">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
            </svg>
            Active for ${activeTime}
          </div>
        </div>
      </div>
    </div>
  </div>

      <!-- Content Sections -->
      <div class="space-y-8">
        <!-- Contact Information Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
              <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
            </svg>
            Contact Information
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Email Address</label>
                <div class="text-base text-gray-900 overflow-x-auto max-w-full">
                    <p class="truncate">${
                        volunteer.email_address || "Not provided"
                    }</p>
                </div>
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Phone Number</label>
              <p class="text-base text-gray-900">${
                  volunteer.mobile_number || "Not provided"
              }</p>
            </div>
            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Date of Birth</label>
              <p class="text-base text-gray-900">${
                  volunteer.date_of_birth
                      ? new Date(volunteer.date_of_birth).toLocaleDateString()
                      : "Not provided"
              }</p>
            </div>
            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Occupation</label>
              <p class="text-base text-gray-900">${
                  volunteer.occupation || "Not provided"
              }</p>
            </div>
          </div>
          ${
              volunteer.address
                  ? `
            <div class="mt-6 pt-6 border-t border-gray-100">
              <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Address</label>
              <p class="text-base text-gray-900 mt-1">${volunteer.address}</p>
            </div>
          `
                  : ""
          }
        </div>

        <!-- Personal Information Card -->
        ${volunteer.sex || volunteer.civil_status || volunteer.detail?.full_name ? `
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
            </svg>
            Personal Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            ${volunteer.detail?.full_name ? `
            <div class="space-y-1">
            <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Full Name</label>
            <p class="text-base text-gray-900">${volunteer.detail.full_name}</p>
            </div>
            ` : ""}
            ${volunteer.sex ? `
            <div class="space-y-1">
            <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Gender</label>
            <p class="text-base text-gray-900 capitalize">${volunteer.sex}</p>
            </div>
            ` : ""}
            ${volunteer.civil_status ? `
            <div class="space-y-1">
            <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Civil Status</label>
            <p class="text-base text-gray-900 capitalize">${volunteer.civil_status}</p>
            </div>
            ` : ""}
        </div>
        </div>
        ` : ""}


        <!-- Sacraments Card -->
        ${
            sacraments.length > 0
                ? `
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Sacraments Received
          </h3>
          <div class="flex flex-wrap gap-3">
            ${sacraments
                .map(
                    (sacrament) => `
              <span class="inline-flex items-center px-4 py-2 text-sm font-medium bg-purple-50 text-purple-800 rounded-lg border border-purple-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                ${sacrament}
              </span>
            `
                )
                .join("")}
          </div>
        </div>
        `
                : ""
        }

        <!-- Formations Card -->
        ${
            formations.length > 0
                ? `
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
            </svg>
            Formations Received
          </h3>
          <div class="flex flex-wrap gap-3">
            ${formations
                .map(
                    (formation) => `
              <span class="inline-flex items-center px-4 py-2 text-sm font-medium bg-indigo-50 text-indigo-800 rounded-lg border border-indigo-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                ${formation}
              </span>
            `
                )
                .join("")}
          </div>
        </div>
        `
                : ""
        }

        <!-- Volunteer Timeline Card -->
        ${
            volunteer.detail?.applied_date || volunteer.detail?.regular_duration
                ? `
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
            </svg>
            Volunteer Timeline
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            ${
                volunteer.detail?.applied_date
                    ? `
            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Month & Year Applied</label>
              <p class="text-base text-gray-900">${new Date(
                  volunteer.detail.applied_date + "-01"
              ).toLocaleDateString("en-US", {
                  year: "numeric",
                  month: "long",
              })}</p>
            </div>
            `
                    : ""
            }
            ${
                volunteer.detail?.regular_duration
                    ? `
            <div class="space-y-1">
              <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Years as Regular Volunteer</label>
              <p class="text-base text-gray-900">${volunteer.detail.regular_duration}</p>
            </div>
            `
                    : ""
            }
          </div>
        </div>
        `
                : ""
        }

        <!-- Additional Notes Card -->
        ${
            volunteer.others
                ? `
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
            </svg>
            Additional Notes
          </h3>
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-base text-gray-700 leading-relaxed">${volunteer.others}</p>
          </div>
        </div>
        `
                : ""
        }
      </div>
    `;

            profileContent.innerHTML = html;

            // Store volunteer ID for edit functionality
            document
                .getElementById("editProfile")
                .setAttribute("data-volunteer-id", id);
        })
        .catch((error) => {
            console.error("Error fetching volunteer data:", error);
            profileContent.innerHTML = `
      <div class="flex items-center justify-center py-16 text-center">
        <div class="max-w-md">
          <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Failed to load profile</h3>
          <p class="text-sm text-gray-500">We encountered an issue while loading the volunteer profile. Please try again later.</p>
          <button onclick="openProfile(${id})" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            Try Again
          </button>
        </div>
      </div>
    `;
        });
}

// Update the edit profile button functionality
document.getElementById("editProfile").addEventListener("click", function () {
    const volunteerId = this.getAttribute("data-volunteer-id");
    if (volunteerId) {
        editVolunteer(volunteerId);
    }
});

// Update the schedule volunteer button functionality
// document
//     .getElementById("scheduleVolunteer")
//     .addEventListener("click", function () {
//         const volunteerId = this.getAttribute("data-volunteer-id");
//         if (volunteerId) {

//             alert(`Scheduling volunteer with ID: ${volunteerId}`);

//         }
//     });

// Add these helper functions if they don't exist
function editVolunteer(id) {
    // Implement your edit volunteer functionality
    alert(`Edit volunteer with ID: ${id}`);
    // You might want to redirect to an edit page or open an edit modal
    // window.location.href = `/volunteers/${id}/edit`;
}

// function deleteVolunteer(id) {
//     if (confirm("Are you sure you want to delete this volunteer?")) {
//         fetch(`/volunteers/${id}`, {
//             method: "DELETE",
//             headers: {
//                 "X-CSRF-TOKEN": document.querySelector(
//                     'meta[name="csrf-token"]'
//                 ).content,
//                 Accept: "application/json",
//             },
//         })
//             .then((response) => response.json())
//             .then((data) => {
//                 if (data.success) {
//                     toastr.success("Volunteer deleted successfully");
//                     location.reload();
//                 } else {
//                     toastr.error("Failed to delete volunteer");
//                 }
//             })
//             .catch((error) => {
//                 console.error("Error:", error);
//                 toastr.error("An error occurred while deleting the volunteer");
//             });
//     }
// }
document.addEventListener("DOMContentLoaded", function () {
    const closeProfileButton = document.getElementById("closeProfile");

    if (closeProfileButton) {
        closeProfileButton.addEventListener("click", () => {
            document
                .getElementById("profileModal")
                .classList.replace("flex", "hidden");
        });
    }
});

// document.getElementById("scheduleVolunteer").addEventListener("click", () => {
//     alert("Navigating to schedule volunteer page…");
//     document.getElementById("profileModal").classList.replace("flex", "hidden");
// });

// Show/hide civil_status_other field
document.addEventListener("DOMContentLoaded", () => {
    const radios = document.querySelectorAll('input[name="civil_status"]');
    const otherInput = document.getElementById("civilOtherInput");
    radios.forEach((r) => {
        r.addEventListener("change", () => {
            otherInput.classList.toggle("hidden", r.value !== "others");
        });
    });
});

document.getElementById("nextToSheet").addEventListener("click", () => {
    const requiredFields = [
        "nickname",
        "dob",
        "sex",
        "address",
        "phone",
        "email",
        "occupation",
    ];
    let valid = true;

    requiredFields.forEach((name) => {
        const field = document.querySelector(`[name="${name}"]`);
        if (!field || !field.value) {
            if (field) field.classList.add("border-red-500");
            valid = false;
        } else {
            field.classList.remove("border-red-500");
        }
    });

    const sex = document.querySelector('[name="sex"]:checked');
    const civil = document.querySelector('[name="civil_status"]:checked');
    if (!sex || !civil) valid = false;

    if (!valid) {
        toastr.warning("Please fill out all required fields.");
        return;
    }

    // Unlock and switch to Info Sheet tab
    const infoTab = document.querySelector('.reg-tab[data-tab="sheet"]');
    infoTab.classList.remove("tab-locked");
    infoTab.click();

    // Hide Next, Show Register
    document.getElementById("nextToSheet").classList.add("hidden");
    document.getElementById("submitRegistration").classList.remove("hidden");
});

//function for pagination

// Add this function to handle pagination clicks
function attachPaginationListeners() {
    // Handle pagination clicks
    document.querySelectorAll(".pagination a").forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            const url = new URL(this.href);
            const page = url.searchParams.get("page");
            const currentView =
                localStorage.getItem("volunteerViewType") || "grid";
            const searchQuery = document.getElementById("searchInput").value;

            // Update current URL
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set("page", page);
            if (searchQuery) {
                currentUrl.searchParams.set("search", searchQuery);
            }
            currentUrl.searchParams.set("view", currentView);

            // Update browser history
            window.history.pushState({}, "", currentUrl.toString());

            // Load the new page
            loadPageData(page, currentView, searchQuery);
        });
    });
}

// New function to load page data
async function loadPageData(page, viewType, searchQuery = "") {
    try {
        showLoadingState();

        const url = new URL("/volunteers", window.location.origin);
        url.searchParams.set("view", viewType);
        url.searchParams.set("page", page);

        if (searchQuery) {
            url.searchParams.set("search", searchQuery);
        }

        // Include active filters
        const ministryFilter = document.getElementById("ministryFilter").value;
        const statusFilter = document.getElementById("statusFilter").value;

        if (ministryFilter) {
            url.searchParams.set("ministry", ministryFilter);
        }

        if (statusFilter) {
            url.searchParams.set("status", statusFilter);
        }

        const response = await fetch(url.toString(), {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
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
// Profile picture preview functionality
document
    .getElementById("profilePictureInput")
    .addEventListener("change", function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById("profilePreview");
        const removeBtn = document.getElementById("removeProfilePicture");

        if (file) {
            // Validate file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                alert("File size must be less than 2MB");
                e.target.value = "";
                return;
            }

            // Validate file type
            if (!file.type.startsWith("image/")) {
                alert("Please select a valid image file");
                e.target.value = "";
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                removeBtn.classList.remove("hidden");
            };
            reader.readAsDataURL(file);
        }
    });

// Remove profile picture
document
    .getElementById("removeProfilePicture")
    .addEventListener("click", function () {
        const input = document.getElementById("profilePictureInput");
        const preview = document.getElementById("profilePreview");
        const removeBtn = document.getElementById("removeProfilePicture");

        input.value = "";
        preview.src =
            "data:image/svg+xml,%3csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100' height='100' fill='%23f3f4f6'/%3e%3ctext x='50%25' y='50%25' font-size='14' text-anchor='middle' alignment-baseline='middle' fill='%23374151'%3eNo Image%3c/text%3e%3c/svg%3e";
        removeBtn.classList.add("hidden");
    });
