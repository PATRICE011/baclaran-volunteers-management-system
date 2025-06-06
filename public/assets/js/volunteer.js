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

    document.getElementById("searchInput").value = searchQuery;
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
        document.getElementById("registrationModal").classList.replace("flex", "hidden");
        resetVolunteerForm(); // ← Add this line
        resetModalTabs();     // ← And this if you want to go back to the Basic Info tab
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
document.getElementById("reg-applied-date").addEventListener("change", function () {
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
    if (months > 0) result += `${years > 0 ? " " : ""}${months} mo${months > 1 ? "s" : ""}`;

    document.getElementById("reg-regular-duration").value = result || "0";
  }
});
document.getElementById("submitRegistration").addEventListener("click", () => {
    const formData = new FormData();

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
            res
                .json()
                .then((data) => ({
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
    <div class="flex items-center justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Loading profile...</span>
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
            const joinDate = volunteer.created_at
                ? new Date(volunteer.created_at).toLocaleDateString()
                : "Unknown";

            // Get ministry information
            const ministryName =
                volunteer.detail?.ministry?.ministry_name ||
                "No Ministry Assigned";

            // Get profile completion status
            const profileStatus = volunteer.detail?.volunteer_status;
            const statusClass = volunteer.has_complete_profile
                ? "bg-green-100 text-green-700"
                : "bg-yellow-100 text-yellow-700";

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

            // Build the profile HTML
            const html = `
      <div class="flex items-center gap-4 mb-6">
        <img src="${avatarUrl}" alt="${displayName}" class="w-16 h-16 rounded-full">
        <div>
          <h2 class="text-xl font-bold">${displayName}</h2>
          <p class="text-sm text-gray-500">Joined on ${joinDate}</p>
          <span class="inline-block px-2 py-1 text-xs rounded-full ${statusClass} mt-1">
            ${profileStatus}
          </span>
        </div>
      </div>
      
      <div class="space-y-4">
        <!-- Contact Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-medium text-gray-500">Email</p>
            <p class="text-sm">${volunteer.email_address || "Not provided"}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Phone</p>
            <p class="text-sm">${volunteer.mobile_number || "Not provided"}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Date of Birth</p>
            <p class="text-sm">${
                volunteer.date_of_birth
                    ? new Date(volunteer.date_of_birth).toLocaleDateString()
                    : "Not provided"
            }
</p>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Occupation</p>
            <p class="text-sm">${volunteer.occupation || "Not provided"}</p>
          </div>
        </div>
        
        <!-- Address -->
        ${
            volunteer.address
                ? `
                                        <div>
                                          <p class="text-sm font-medium text-gray-500">Address</p>
                                          <p class="text-sm">${volunteer.address}</p>
                                        </div>
                                        `
                : ""
        }
        
        <!-- Ministry -->
        <div>
          <p class="text-sm font-medium text-gray-500">Ministry</p>
          <span class="inline-block px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">
            ${ministryName}
          </span>
        </div>
        
        <!-- Personal Information -->
        ${
            volunteer.sex || volunteer.civil_status
                ? `
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                          ${
                                              volunteer.sex
                                                  ? `
          <div>
            <p class="text-sm font-medium text-gray-500">Gender</p>
            <p class="text-sm capitalize">${volunteer.sex}</p>
          </div>
          `
                                                  : ""
                                          }
                                          ${
                                              volunteer.civil_status
                                                  ? `
          <div>
            <p class="text-sm font-medium text-gray-500">Civil Status</p>
            <p class="text-sm capitalize">${volunteer.civil_status}</p>
          </div>
          `
                                                  : ""
                                          }
                                        </div>
                                        `
                : ""
        }
        
        <!-- Sacraments -->
        ${
            sacraments.length > 0
                ? `
                                        <div>
                                          <p class="text-sm font-medium text-gray-500 mb-2">Sacraments Received</p>
                                          <div class="flex flex-wrap gap-1">
                                            ${sacraments
                                                .map(
                                                    (sacrament) =>
                                                        `<span class="px-2 py-1 text-xs bg-purple-50 text-purple-700 rounded border border-purple-200">${sacrament}</span>`
                                                )
                                                .join("")}
                                          </div>
                                        </div>
                                        `
                : ""
        }
        
        <!-- Formations -->
        ${
            formations.length > 0
                ? `
                                        <div>
                                          <p class="text-sm font-medium text-gray-500 mb-2">Formations Received</p>
                                          <div class="flex flex-wrap gap-1">
                                            ${formations
                                                .map(
                                                    (formation) =>
                                                        `<span class="px-2 py-1 text-xs bg-green-50 text-green-700 rounded border border-green-200">${formation}</span>`
                                                )
                                                .join("")}
                                          </div>
                                        </div>
                                        `
                : ""
        }
        
        <!-- Volunteer Timeline -->
        ${
            volunteer.detail?.applied_date || volunteer.detail?.regular_duration
                ? `
                                        <div>
                                          <p class="text-sm font-medium text-gray-500 mb-2">Volunteer Information</p>
                                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            ${
                                                volunteer.detail?.applied_date
                                                    ? `
            <div>
              <p class="text-xs text-gray-400">Month & Year Applied</p>
              <p class="text-sm">${new Date(
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
                                                volunteer.detail
                                                    ?.regular_duration
                                                    ? `
            <div>
              <p class="text-xs text-gray-400">Years as Regular Volunteer</p>
              <p class="text-sm">${volunteer.detail.regular_duration}</p>
            </div>
            `
                                                    : ""
                                            }
                                          </div>
                                        </div>
                                        `
                : ""
        }
        
        <!-- Additional Notes -->
        ${
            volunteer.others
                ? `
                                        <div>
                                          <p class="text-sm font-medium text-gray-500">Additional Notes</p>
                                          <p class="text-sm">${volunteer.others}</p>
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
            document
                .getElementById("scheduleVolunteer")
                .setAttribute("data-volunteer-id", id);
        })
        .catch((error) => {
            console.error("Error fetching volunteer data:", error);
            profileContent.innerHTML = `
      <div class="flex items-center justify-center py-8 text-center">
        <div class="text-red-600">
          <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="font-medium">Failed to load profile</p>
          <p class="text-sm text-gray-500 mt-1">Please try again later</p>
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
document
    .getElementById("scheduleVolunteer")
    .addEventListener("click", function () {
        const volunteerId = this.getAttribute("data-volunteer-id");
        if (volunteerId) {
            // You can customize this action based on your needs
            alert(`Scheduling volunteer with ID: ${volunteerId}`);
            // Or redirect to a scheduling page:
            // window.location.href = `/volunteers/${volunteerId}/schedule`;
        }
    });

// Add these helper functions if they don't exist
function editVolunteer(id) {
    // Implement your edit volunteer functionality
    alert(`Edit volunteer with ID: ${id}`);
    // You might want to redirect to an edit page or open an edit modal
    // window.location.href = `/volunteers/${id}/edit`;
}

function deleteVolunteer(id) {
    if (confirm("Are you sure you want to delete this volunteer?")) {
        fetch(`/volunteers/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    toastr.success("Volunteer deleted successfully");
                    location.reload(); // Refresh the page to update the list
                } else {
                    toastr.error("Failed to delete volunteer");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                toastr.error("An error occurred while deleting the volunteer");
            });
    }
}
document.getElementById("closeProfile").addEventListener("click", () => {
    document.getElementById("profileModal").classList.replace("flex", "hidden");
});
document.getElementById("scheduleVolunteer").addEventListener("click", () => {
    alert("Navigating to schedule volunteer page…");
    document.getElementById("profileModal").classList.replace("flex", "hidden");
});

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
            attachPaginationListeners(); // ← This is crucial!
        } else {
            throw new Error(data.message || "Failed to load page");
        }
    } catch (error) {
        console.error("Error loading page data:", error);
        showErrorState(viewType);
    }
}
