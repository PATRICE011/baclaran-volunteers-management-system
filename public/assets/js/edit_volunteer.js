// =============================================================================
// GLOBAL STATE MANAGEMENT
// =============================================================================

let volunteerChanges = {
    basicInfo: {},
    timelines: [],
    affiliations: [],
    sacraments: [],
    formations: [],
};

let currentActiveTabId = "contact-tab";

// Initialize changes object when opening a profile
function initializeChanges(volunteerData) {
    volunteerChanges = {
        basicInfo: {},
        timelines: [...(volunteerData.timelines || [])],
        affiliations: [...(volunteerData.affiliations || [])],
        sacraments: [...(volunteerData.sacraments || [])],
        formations: [...(volunteerData.formations || [])],
    };
}

// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

function calculateTotalYears(startYear, endYear) {
    if (!startYear) return 0;

    const currentYear = new Date().getFullYear();
    const start = parseInt(startYear);
    const end = endYear === "present" ? currentYear : parseInt(endYear);

    if (isNaN(start) || isNaN(end)) return 0;
    return end - start + 1;
}

function calculateAge(dateString) {
    if (!dateString) return null;
    const birthDate = new Date(dateString);
    if (isNaN(birthDate)) return null;

    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (
        monthDiff < 0 ||
        (monthDiff === 0 && today.getDate() < birthDate.getDate())
    ) {
        age--;
    }
    return age;
}

function generateYearOptions(selectedYear) {
    let options = "";
    const currentYear = new Date().getFullYear();
    for (let year = currentYear; year >= 1980; year--) {
        options += `<option value="${year}" ${
            year == selectedYear ? "selected" : ""
        }>${year}</option>`;
    }
    return options;
}

function buildYearOptions() {
    const current = new Date().getFullYear();
    let opts = '<option value="">Select Year</option>';
    for (let y = current; y >= 1980; y--) {
        opts += `<option value="${y}">${y}</option>`;
    }
    return opts;
}

// =============================================================================
// UI GENERATION HELPERS
// =============================================================================

function renderMinistryOptions(ministries, selectedId = null, level = 0) {
    return ministries
        .map((ministry) => {
            const indent = "&nbsp;".repeat(level * 4) + (level > 0 ? "→ " : "");
            let option = `<option value="${ministry.id}" ${
                ministry.id === selectedId ? "selected" : ""
            }>
                ${indent}${ministry.ministry_name}
            </option>`;

            if (ministry.children && ministry.children.length > 0) {
                option += renderMinistryOptions(
                    ministry.children,
                    selectedId,
                    level + 1
                );
            }
            return option;
        })
        .join("");
}

function generateEditableField(
    label,
    fieldName,
    value,
    volunteerId,
    inputType = "text",
    fullWidth = false,
    options = null
) {
    const displayValue = value || "Not provided";
    const sanitizedFieldName = fieldName.replace(/\s+/g, "_").toLowerCase();
    const fieldId = `field-${sanitizedFieldName}`;

    const inputHtml = (() => {
        if (options && Array.isArray(options)) {
            const normalizedValue = String(value || "").toLowerCase();
            return `
                <select id="${fieldId}-input" data-field="${fieldName}" data-original="${normalizedValue}" 
                        class="form-input editable-input hidden w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    ${options
                        .map((opt) => {
                            const optLower = opt.toLowerCase();
                            return `<option value="${optLower}" ${
                                optLower === normalizedValue ? "selected" : ""
                            }>
                            ${opt.charAt(0).toUpperCase() + opt.slice(1)}
                        </option>`;
                        })
                        .join("")}
                </select>
            `;
        }

        if (inputType === "textarea") {
            return `
                <textarea id="${fieldId}-input" data-field="${fieldName}" data-original="${
                value || ""
            }" 
                          class="form-textarea editable-input hidden w-full mt-1 border-gray-300 rounded-md shadow-sm" rows="3">${
                              value || ""
                          }</textarea>
            `;
        }

        if (inputType === "date") {
            let formatted = "";
            if (value) {
                const dateObj = new Date(value);
                if (!isNaN(dateObj)) {
                    const yyyy = dateObj.getFullYear();
                    const mm = String(dateObj.getMonth() + 1).padStart(2, "0");
                    const dd = String(dateObj.getDate()).padStart(2, "0");
                    formatted = `${yyyy}-${mm}-${dd}`;
                }
            }
            return `
                <input type="date" id="${fieldId}-input" data-field="${fieldName}" data-original="${formatted}" 
                       value="${formatted}" class="form-input editable-input hidden w-full max-w-xs sm:max-w-sm md:max-w-md mt-1 border-gray-300 rounded-md shadow-sm" />
            `;
        }

        return `
            <input type="${inputType}" id="${fieldId}-input" data-field="${fieldName}" data-original="${
            value || ""
        }" 
                   value="${
                       value || ""
                   }" class="form-input editable-input hidden w-full max-w-xs sm:max-w-sm md:max-w-md mt-1 border-gray-300 rounded-md shadow-sm" />
        `;
    })();

    const isEmail = fieldName === "email_address";
    const displayDivClass = isEmail
        ? "text-gray-800 max-w-[220px] overflow-x-auto whitespace-nowrap scrollbar-thin scrollbar-thumb-gray-300 px-1 rounded bg-white border border-gray-100"
        : "text-gray-800";

    return `
        <div class="${fullWidth ? "col-span-full" : ""} space-y-1">
            <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">${label}</label>
            <div class="relative group">
                <div id="${fieldId}-display" class="${displayDivClass}">${displayValue}</div>
                ${inputHtml}
                <button onclick="toggleEditField('${fieldId}', event)" class="absolute top-2 right-2 text-gray-400 hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
}

// =============================================================================
// PROFILE LOADING & RENDERING
// =============================================================================

function openProfile(id, activeTabId = "contact-tab") {
    const profileContent = document.getElementById("profileContent");
    profileContent.innerHTML = `
        <div class="flex items-center justify-center py-16">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-3 border-blue-600 mx-auto mb-4"></div>
                <span class="text-lg text-gray-600">Loading profile...</span>
            </div>
        </div>
    `;

    document.getElementById("profileModal").classList.replace("hidden", "flex");

    fetch(`/volunteers/${id}`, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error("Failed to fetch volunteer data");
            return response.json();
        })
        .then((data) => {
            window.ministriesList = data.ministries;
            renderEditableProfile(
                data.volunteer,
                id,
                activeTabId,
                data.ministries
            );
        })
        .catch((error) => {
            console.error("Error:", error);
            showProfileError(id);
        });
}

function showProfileError(id) {
    const profileContent = document.getElementById("profileContent");
    profileContent.innerHTML = `
        <div class="text-center py-20">
            <h2 class="text-xl font-semibold text-red-600 mb-4">Failed to load profile</h2>
            <p class="text-gray-500 mb-6">We couldn't retrieve the volunteer's data at the moment.</p>
            <button onclick="openProfile(${id})" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Retry
            </button>
        </div>
    `;
}

function renderEditableProfile(
    volunteer,
    id,
    activeTabId = "contact-tab",
    ministriesList = []
) {
    initializeChanges(volunteer);
    const profileContent = document.getElementById("profileContent");

    // Extract volunteer data
    const displayName =
        volunteer.nickname || volunteer.detail?.full_name || "No Name";
    const avatarSeed = displayName.split(" ")[0].toLowerCase();
    const imageUrl = volunteer.profile_picture
        ? `/storage/${volunteer.profile_picture}`
        : `https://api.dicebear.com/7.x/avataaars/svg?seed=${avatarSeed}`;

    const joinDateStr = volunteer.detail?.applied_month_year;
    const joinDate = joinDateStr
        ? new Date(joinDateStr + (joinDateStr.length === 7 ? "-01" : ""))
        : new Date();

    const activeTime = volunteer.active_for || "Duration unknown";
    const ministryId = volunteer.detail?.ministry?.id;
    let ministryName = "No Ministry Assigned";

    function findMinistryNameById(ministries, id) {
        for (const ministry of ministries) {
            if (ministry.id === id) return ministry.ministry_name;
            if (ministry.children) {
                const childMatch = findMinistryNameById(ministry.children, id);
                if (childMatch) return childMatch;
            }
        }
        return null;
    }

    if (ministryId) {
        const name = findMinistryNameById(ministriesList, ministryId);
        if (name) ministryName = name;
    }

    const volunteerStatus = volunteer.detail?.volunteer_status || "No Status";
    const statusClass =
        volunteerStatus === "Active"
            ? "bg-emerald-100 text-emerald-800 border-emerald-200"
            : volunteerStatus === "On-Leave"
            ? "bg-yellow-100 text-yellow-800 border-yellow-200"
            : "bg-red-100 text-red-800 border-red-200";

    const age = volunteer.date_of_birth
        ? calculateAge(volunteer.date_of_birth)
        : null;

    const html = `
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 -m-6 mb-8 p-8 rounded-t-lg border-b border-gray-100">
            <div class="flex items-start gap-6">
                <div class="relative group">
                    <img src="${imageUrl}" alt="${displayName}" class="w-24 h-24 rounded-full shadow-lg ring-4 ring-white object-cover">
                    <input type="file" id="profilePictureInput-${id}" class="hidden" accept="image/*" onchange="uploadProfilePicture(event, ${id})">
                    <button type="button" onclick="document.getElementById('profilePictureInput-${id}').click()" 
                        class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    ${
                        volunteerStatus === "Active"
                            ? `
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>`
                            : ""
                    }
                </div>
               
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="relative group flex items-center">
                            <h2 id="display-name-display" class="text-2xl font-bold text-gray-900">${displayName}</h2>
                            <input type="text" id="display-name-input" data-field="nickname" data-original="${
                                volunteer.nickname || ""
                            }" 
                                   value="${volunteer.nickname || ""}" 
                                   class="editable-input hidden text-2xl font-bold text-gray-900 bg-transparent border-b-2 border-blue-500 focus:outline-none focus:border-blue-700" 
                                   placeholder="Enter display name" />
                            <button onclick="toggleEditField('display-name', event)" class="ml-2 text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <!-- Status Section -->
                        <div class="flex items-center gap-1">
                            <div class="relative group flex items-center">
                                <span id="volunteer-status-display" class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border ${statusClass}">
                                    <svg class="w-2 h-2 mr-2" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    ${volunteerStatus}
                                </span>
                                <select id="volunteer-status-input" data-field="volunteer_status" data-original="${volunteerStatus}" 
                                        class="editable-input hidden px-3 py-1 text-sm font-medium rounded-full border border-gray-300 bg-white">
                                    <option value="Active" ${
                                        volunteerStatus === "Active"
                                            ? "selected"
                                            : ""
                                    }>Active</option>
                                    <option value="Inactive" ${
                                        volunteerStatus === "Inactive"
                                            ? "selected"
                                            : ""
                                    }>Inactive</option>
                                    <option value="On-Leave" ${
                                        volunteerStatus === "On-Leave"
                                            ? "selected"
                                            : ""
                                    }>On-Leave</option>
                                </select>
                                <button onclick="toggleEditField('volunteer-status', event)" class="ml-2 text-gray-400 hover:text-blue-600 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="relative group flex items-center">
                            <span id="ministry-display" class="inline-flex items-center px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-full border border-blue-200">
                                <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V8z" clip-rule="evenodd"></path>
                                </svg>
                                ${ministryName}
                            </span>
                            <select id="ministry-input" data-field="ministry_id" data-original="${
                                volunteer.detail?.ministry?.id || ""
                            }" 
                                    class="editable-input hidden px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-full border border-blue-200">
                                <option value="">No Ministry Assigned</option>
                                ${renderMinistryOptions(
                                    ministriesList,
                                    volunteer.detail?.ministry?.id
                                )}
                            </select>
                            <button onclick="toggleEditField('ministry', event)" class="ml-2 text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Joined ${joinDate.toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "long",
                            })}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Active for ${activeTime}
                        </div>
                    </div>
                    
                    <div class="flex items-center mt-2">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        ID: ${volunteer.volunteer_id || "N/A"}
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6">
            <nav class="flex space-x-8 border-b border-gray-200 overflow-x-auto">
                <button onclick="switchTab(event, 'contact-tab')" class="profile-tab active-tab py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                    Contact
                </button>
                <button onclick="switchTab(event, 'personal-tab')" class="profile-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    Personal
                </button>
                <button onclick="switchTab(event, 'spiritual-tab')" class="profile-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Spiritual
                </button>
                <button onclick="switchTab(event, 'timeline-tab')" class="profile-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    Timeline
                </button>
                <button onclick="switchTab(event, 'affiliations-tab')" class="profile-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M17 2a2 2 0 00-2-2H5a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2L17 18V2zM9 12a1 1 0 100-2 1 1 0 000 2zm4-3a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                    </svg>
                    Affiliations
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-8">
            <!-- Contact Information Tab -->
            <div id="contact-tab" class="tab-content">
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        ${generateEditableField(
                            "Email Address",
                            "email_address",
                            volunteer.email_address,
                            id,
                            "email"
                        )}
                        ${generateEditableField(
                            "Phone Number",
                            "mobile_number",
                            volunteer.mobile_number,
                            id,
                            "tel"
                        )}
                        ${
                            volunteer.address
                                ? `
                            <div class="col-span-full space-y-1">
                                ${generateEditableField(
                                    "Address",
                                    "address",
                                    volunteer.address,
                                    id,
                                    "textarea",
                                    true
                                )}
                            </div>`
                                : ""
                        }
                    </div>
                </div>
            </div>

            <!-- Personal Information Tab -->
            <div id="personal-tab" class="tab-content hidden">
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        ${generateEditableField(
                            "Full Name",
                            "full_name",
                            volunteer.detail?.full_name,
                            id
                        )}
                        ${generateEditableField(
                            "Gender",
                            "sex",
                            volunteer.sex,
                            id,
                            "select",
                            false,
                            ["Male", "Female"]
                        )}
                        ${generateEditableField(
                            "Civil Status",
                            "civil_status",
                            volunteer.civil_status,
                            id,
                            "select",
                            false,
                            [
                                "single",
                                "married",
                                "widowed",
                                "separated",
                                "others",
                            ]
                        )}
                        ${generateEditableField(
                            "Date of Birth",
                            "date_of_birth",
                            volunteer.date_of_birth
                                ? new Date(
                                      volunteer.date_of_birth
                                  ).toLocaleDateString()
                                : "",
                            id,
                            "date"
                        )}
                        ${generateEditableField(
                            "Age",
                            "age",
                            age ? `${age} years` : "Not provided",
                            id,
                            "text",
                            false
                        )}
                        ${generateEditableField(
                            "Occupation",
                            "occupation",
                            volunteer.occupation,
                            id
                        )}
                    </div>
                </div>
            </div>

            <!-- Spiritual Journey Tab -->
            <div id="spiritual-tab" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Sacraments Section -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Sacraments Received
                            </h3>
                            <button onclick="openSacramentEditor(${id})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                ${
                                    (volunteer.sacraments || []).length > 0
                                        ? "Edit Sacraments"
                                        : "Add Sacrament"
                                }
                            </button>
                        </div>
                        <div class="space-y-4" id="sacraments-display">
                            ${
                                (volunteer.sacraments || []).length > 0
                                    ? generateSacramentsDisplay(
                                          volunteer.sacraments
                                      )
                                    : '<p class="text-gray-500">No sacraments added yet.</p>'
                            }
                        </div>
                        ${generateSacramentEditor(volunteer.sacraments || [])}
                    </div>

                    <!-- Formations Section -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                                </svg>
                                Formations Received
                            </h3>
                            <button onclick="openFormationEditor(${id})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                ${
                                    (volunteer.formations || []).length > 0
                                        ? "Edit Formations"
                                        : "Add Formation"
                                }
                            </button>
                        </div>
                        <div class="space-y-4" id="formations-display">
                            ${
                                (volunteer.formations || []).length > 0
                                    ? generateFormationsDisplay(
                                          volunteer.formations
                                      )
                                    : '<p class="text-gray-500">No formations added yet.</p>'
                            }
                        </div>
                        ${generateFormationEditor(volunteer.formations || [])}
                    </div>
                </div>
            </div>

            <!-- Timeline Tab -->
            <div id="timeline-tab" class="tab-content hidden">
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Organization Timeline
                        </h3>
                        <button onclick="addNewTimelineEntry(${id})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Entry
                        </button>
                    </div>
                    <div class="space-y-4" id="timelines-display">
                        ${
                            (volunteer.timelines || []).length > 0
                                ? generateTimelinesDisplay(volunteer.timelines)
                                : '<p class="text-gray-500">No timeline entries added yet.</p>'
                        }
                    </div>
                </div>
            </div>

            <!-- Affiliations Tab -->
            <div id="affiliations-tab" class="tab-content hidden">
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M17 2a2 2 0 00-2-2H5a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2L17 18V2zM9 12a1 1 0 100-2 1 1 0 000 2zm4-3a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                            </svg>
                            Other Affiliations
                        </h3>
                        <button onclick="addNewAffiliation(${id})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Affiliation
                        </button>
                    </div>
                    <div class="space-y-4" id="affiliations-display">
                        ${
                            (volunteer.affiliations || []).length > 0
                                ? generateAffiliationsDisplay(
                                      volunteer.affiliations
                                  )
                                : '<p class="text-gray-500">No affiliations added yet.</p>'
                        }
                    </div>
                </div>
            </div>
        </div>
    `;

    profileContent.innerHTML = html;

    // Setup save button
    const editButton = document.getElementById("editProfile");
    if (editButton) {
        editButton.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            Save Changes
        `;
    }

    // Set active tab and wire up save buttons
    setTimeout(() => {
        const activeTabBtn = document.querySelector(
            `button[onclick*='${activeTabId}']`
        );
        if (activeTabBtn) activeTabBtn.click();
    }, 50);

    wireSaveButtons(id);
    attachYearCalculators();
}

// =============================================================================
// TAB MANAGEMENT
// =============================================================================

function switchTab(event, tabId) {
    currentActiveTabId = tabId;

    const tabs = document.querySelectorAll(".profile-tab");
    const tabContents = document.querySelectorAll(".tab-content");

    tabs.forEach((tab) => {
        tab.classList.remove("active-tab", "border-blue-500", "text-blue-600");
        tab.classList.add("border-transparent", "text-gray-500");
    });

    tabContents.forEach((content) => {
        content.classList.add("hidden");
    });

    event.target.classList.add(
        "active-tab",
        "border-blue-500",
        "text-blue-600"
    );
    event.target.classList.remove("border-transparent", "text-gray-500");

    document.getElementById(tabId).classList.remove("hidden");
}

// =============================================================================
// FIELD EDITING FUNCTIONS
// =============================================================================

function toggleEditField(fieldId, event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    const displayEl = document.getElementById(`${fieldId}-display`);
    const inputEl = document.getElementById(`${fieldId}-input`);

    if (displayEl && inputEl) {
        // Hide other editing fields
        document.querySelectorAll(".editable-input").forEach((el) => {
            if (el.id !== `${fieldId}-input`) {
                el.classList.add("hidden");
                const correspondingDisplay = document.getElementById(
                    el.id.replace("-input", "-display")
                );
                if (correspondingDisplay)
                    correspondingDisplay.classList.remove("hidden");
            }
        });

        displayEl.classList.toggle("hidden");
        inputEl.classList.toggle("hidden");

        if (!inputEl.classList.contains("hidden")) {
            setTimeout(() => {
                inputEl.focus();
                if (inputEl.type === "text") inputEl.select();
            }, 50);
        }
    }
}

function updateDisplayValue(inputEl) {
    const displayEl = document.getElementById(
        inputEl.id.replace("-input", "-display")
    );
    if (!displayEl) return;

    const field = inputEl.dataset.field;
    let newValue = inputEl.value || "";

    if (field === "volunteer_status") {
        const statusClass =
            newValue === "Active"
                ? "bg-emerald-100 text-emerald-800 border-emerald-200"
                : newValue === "On-Leave"
                ? "bg-yellow-100 text-yellow-800 border-yellow-200"
                : "bg-red-100 text-red-800 border-red-200";

        displayEl.className = `inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border ${statusClass}`;
        displayEl.innerHTML = `
            <svg class="w-2 h-2 mr-2" fill="currentColor" viewBox="0 0 8 8">
                <circle cx="4" cy="4" r="3"/>
            </svg>
            ${newValue}
        `;
    } else if (field === "ministry_id") {
        const selectedOption = inputEl.querySelector(
            `option[value="${newValue}"]`
        );
        const ministryName = selectedOption
            ? selectedOption.textContent.trim()
            : "No Ministry Assigned";
        displayEl.textContent = ministryName;
    } else {
        displayEl.textContent = newValue || "Not provided";
    }
}

// =============================================================================
// DISPLAY GENERATION FUNCTIONS
// =============================================================================

function generateTimelinesDisplay(timelines) {
    return timelines
        .map((t, index) => {
            const isActive = t.year_ended === "present";
            return `
            <div class="timeline-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
                <div class="display-mode">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-800">${
                                t.organization_name || "No Organization"
                            }</p>
                            <p class="text-sm text-gray-500">${
                                t.year_started || "?"
                            } - ${
                isActive ? "Present" : t.year_ended || "?"
            }</p>
                            ${
                                t.total_years
                                    ? `<p class="text-xs text-gray-400 mt-1">${
                                          t.total_years
                                      } year${t.total_years > 1 ? "s" : ""}</p>`
                                    : ""
                            }
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editTimelineEntry(this, ${index})" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteTimelineEntry(this, ${index})" class="text-gray-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                ${generateTimelineEditMode(t, index)}
            </div>
        `;
        })
        .join("");
}

function generateTimelineEditMode(timeline, index) {
    const isActive = timeline.year_ended === "present";
    return `
        <div class="edit-mode hidden mt-3 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                    <input type="text" class="form-input w-full" value="${
                        timeline.organization_name || ""
                    }" data-field="organization_name" data-index="${index}">
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                        <select class="form-select w-full year-select" data-field="year_started" data-index="${index}">
                            <option value="">Start Year</option>
                            ${generateYearOptions(timeline.year_started)}
                        </select>
                    </div>
                    <span class="flex items-end pb-2 text-sm">—</span>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                        <select class="form-select w-full year-select" data-field="year_ended" data-index="${index}">
                            <option value="">End Year</option>
                            <option value="present" ${
                                isActive ? "selected" : ""
                            }>Present</option>
                            ${generateYearOptions(timeline.year_ended)}
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Years</label>
                    <input type="number" class="form-input w-full total-years" value="${
                        timeline.total_years || ""
                    }" readonly>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button onclick="cancelEditTimeline(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button onclick="saveTimelineEdit(this, ${index})" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Done</button>
            </div>
        </div>
    `;
}

function generateAffiliationsDisplay(affiliations) {
    return affiliations
        .map((a, index) => {
            const isActive = a.year_ended === "present";
            return `
            <div class="affiliation-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
                <div class="display-mode">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-800">${
                                a.organization_name || "No Organization"
                            }</p>
                            <p class="text-sm text-gray-500">${
                                a.year_started || "?"
                            } - ${
                isActive ? "Present" : a.year_ended || "?"
            }</p>
                            ${
                                a.total_years
                                    ? `<p class="text-xs text-gray-400 mt-1">${
                                          a.total_years
                                      } year${a.total_years > 1 ? "s" : ""}</p>`
                                    : ""
                            }
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editAffiliation(this, ${index})" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteAffiliation(this, ${index})" class="text-gray-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                ${generateAffiliationEditMode(a, index)}
            </div>
        `;
        })
        .join("");
}

function generateAffiliationEditMode(affiliation, index) {
    const isActive = affiliation.year_ended === "present";
    return `
        <div class="edit-mode hidden mt-3 p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                    <input type="text" class="form-input w-full" value="${
                        affiliation.organization_name || ""
                    }" data-field="organization_name" data-index="${index}">
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                        <select class="form-select w-full year-select" data-field="year_started" data-index="${index}">
                            <option value="">Start Year</option>
                            ${generateYearOptions(affiliation.year_started)}
                        </select>
                    </div>
                    <span class="flex items-end pb-2 text-sm">—</span>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                        <select class="form-select w-full year-select" data-field="year_ended" data-index="${index}">
                            <option value="">End Year</option>
                            <option value="present" ${
                                isActive ? "selected" : ""
                            }>Present</option>
                            ${generateYearOptions(affiliation.year_ended)}
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Years</label>
                    <input type="number" class="form-input w-full total-years" value="${
                        affiliation.total_years || ""
                    }" readonly>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button onclick="cancelEditAffiliation(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button onclick="saveAffiliationEdit(this, ${index})" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Done</button>
            </div>
        </div>
    `;
}

function generateSacramentsDisplay(sacraments) {
    return sacraments
        .map(
            (sacrament, index) => `
        <div class="sacrament-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
            <div class="display-mode">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-800">${
                            sacrament.sacrament_name || sacrament
                        }</p>
                        ${
                            sacrament.year
                                ? `<p class="text-sm text-gray-500">${sacrament.year}</p>`
                                : ""
                        }
                    </div>
                </div>
            </div>
        </div>
    `
        )
        .join("");
}

function generateFormationsDisplay(formations) {
    return formations
        .map(
            (formation, index) => `
        <div class="formation-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
            <div class="display-mode">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-800">${
                            formation.formation_name || formation
                        }</p>
                        ${
                            formation.year
                                ? `<p class="text-sm text-gray-500">${formation.year}</p>`
                                : ""
                        }
                    </div>
                </div>
            </div>
        </div>
    `
        )
        .join("");
}

function generateSacramentEditor(sacraments) {
    return `
        <div id="sacraments-editor" class="hidden mt-4 border rounded-lg p-4 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" value="Baptism" class="sacrament-cb" ${
                        sacraments.some(
                            (s) => (s.sacrament_name || s) === "Baptism"
                        )
                            ? "checked"
                            : ""
                    }> Baptism
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" value="Marriage" class="sacrament-cb" ${
                        sacraments.some(
                            (s) => (s.sacrament_name || s) === "Marriage"
                        )
                            ? "checked"
                            : ""
                    }> Marriage
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" value="First Communion" class="sacrament-cb" ${
                        sacraments.some(
                            (s) => (s.sacrament_name || s) === "First Communion"
                        )
                            ? "checked"
                            : ""
                    }> First Communion
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" value="Confirmation" class="sacrament-cb" ${
                        sacraments.some(
                            (s) => (s.sacrament_name || s) === "Confirmation"
                        )
                            ? "checked"
                            : ""
                    }> Confirmation
                </label>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeSacramentEditor()" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="button" onclick="saveSacramentCheckboxes()" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            </div>
        </div>
    `;
}

function generateFormationEditor(formations) {
    return `
        <div id="formations-editor" class="hidden mt-4 border rounded-lg p-4 bg-gray-50">
            <div class="space-y-3">
                <!-- Standard formations -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="formation-cb" data-name="BOS" ${
                            formations.some(
                                (f) => (f.formation_name || f) === "BOS"
                            )
                                ? "checked"
                                : ""
                        }> Basic Orientation Seminar (BOS)
                    </label>
                    <select class="form-select formation-year" data-for="BOS" ${
                        formations.some(
                            (f) => (f.formation_name || f) === "BOS"
                        )
                            ? ""
                            : "disabled"
                    }>
                        <option value="">Select Year</option>
                        ${generateYearOptions(
                            formations.find(
                                (f) => (f.formation_name || f) === "BOS"
                            )?.year
                        )}
                    </select>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="formation-cb" data-name="Diocesan Basic Formation" ${
                            formations.some(
                                (f) =>
                                    (f.formation_name || f) ===
                                    "Diocesan Basic Formation"
                            )
                                ? "checked"
                                : ""
                        }> Diocesan Basic Formation
                    </label>
                    <select class="form-select formation-year" data-for="Diocesan Basic Formation" ${
                        formations.some(
                            (f) =>
                                (f.formation_name || f) ===
                                "Diocesan Basic Formation"
                        )
                            ? ""
                            : "disabled"
                    }>
                        <option value="">Select Year</option>
                        ${generateYearOptions(
                            formations.find(
                                (f) =>
                                    (f.formation_name || f) ===
                                    "Diocesan Basic Formation"
                            )?.year
                        )}
                    </select>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="formation-cb" data-name="Safeguarding Policy" ${
                            formations.some(
                                (f) =>
                                    (f.formation_name || f) ===
                                    "Safeguarding Policy"
                            )
                                ? "checked"
                                : ""
                        }> Safeguarding Policy
                    </label>
                    <select class="form-select formation-year" data-for="Safeguarding Policy" ${
                        formations.some(
                            (f) =>
                                (f.formation_name || f) ===
                                "Safeguarding Policy"
                        )
                            ? ""
                            : "disabled"
                    }>
                        <option value="">Select Year</option>
                        ${generateYearOptions(
                            formations.find(
                                (f) =>
                                    (f.formation_name || f) ===
                                    "Safeguarding Policy"
                            )?.year
                        )}
                    </select>
                </div>

                <!-- Other formations -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-700">Other Formation Received</p>
                        <button type="button" id="add-other-formation-row" class="text-blue-600 text-sm hover:underline">+ Add other</button>
                    </div>
                    <div id="other-formation-rows" class="space-y-2">
                        ${formations
                            .filter(
                                (f) =>
                                    ![
                                        "BOS",
                                        "Diocesan Basic Formation",
                                        "Safeguarding Policy",
                                    ].includes(f.formation_name || f)
                            )
                            .map(
                                (formation, index) => `
                                <div class="other-formation-row flex items-center gap-2">
                                    <input type="text" placeholder="Formation Name" class="w-48 border rounded px-2 py-1 text-sm other-name" value="${
                                        formation.formation_name || formation
                                    }">
                                    <select class="border rounded px-2 py-1 text-sm other-year">
                                        <option value="">Select Year</option>
                                        ${generateYearOptions(formation.year)}
                                    </select>
                                    <button type="button" class="text-red-600 hover:text-red-800 remove-other">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            `
                            )
                            .join("")}
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeFormationEditor()" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="button" onclick="saveFormationCheckboxes()" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            </div>
        </div>
    `;
}

// =============================================================================
// EVENT HANDLERS & YEAR CALCULATORS
// =============================================================================

function attachYearCalculators() {
    document.querySelectorAll(".year-select").forEach((select) => {
        select.addEventListener("change", function () {
            const entry =
                this.closest(".timeline-entry") ||
                this.closest(".affiliation-entry");
            const startSelect = entry.querySelector(
                'select[data-field="year_started"]'
            );
            const endSelect = entry.querySelector(
                'select[data-field="year_ended"]'
            );
            const totalInput = entry.querySelector(".total-years");

            if (startSelect && endSelect && totalInput) {
                const startYear = startSelect.value;
                const endYear = endSelect.value;
                totalInput.value = calculateTotalYears(startYear, endYear);
            }
        });
    });
}

// Keyboard event handlers
document.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
        const activeInput = document.querySelector(
            ".editable-input:not(.hidden)"
        );
        if (activeInput) {
            event.preventDefault();
            activeInput.classList.add("hidden");
            const correspondingDisplay = document.getElementById(
                activeInput.id.replace("-input", "-display")
            );
            if (correspondingDisplay) {
                correspondingDisplay.classList.remove("hidden");
                updateDisplayValue(activeInput);
            }
        }
    } else if (event.key === "Escape") {
        const activeInput = document.querySelector(
            ".editable-input:not(.hidden)"
        );
        if (activeInput) {
            event.preventDefault();
            activeInput.value = activeInput.dataset.original || "";
            activeInput.classList.add("hidden");
            const correspondingDisplay = document.getElementById(
                activeInput.id.replace("-input", "-display")
            );
            if (correspondingDisplay)
                correspondingDisplay.classList.remove("hidden");
        }
    }
});

// Modal close handler
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

// =============================================================================
// SAVE FUNCTIONALITY - UNIFIED SAVE SYSTEM
// =============================================================================

function saveAllChanges(volunteerId, btnEl) {
    // 1) Collect changed basic fields
    const changed = {};
    document.querySelectorAll(".editable-input").forEach((input) => {
        const field = input.dataset.field;
        if (!field) return;
        const original = (input.dataset.original || "").trim();
        const current = (input.value || "").trim();
        if (current !== original) changed[field] = current;
    });

    // 2) Prepare payload with all changes
    const payload = { ...changed };

    // Handle ministry_id conversion
    if (Object.prototype.hasOwnProperty.call(changed, "ministry_id")) {
        const mid =
            changed.ministry_id === "" ? null : Number(changed.ministry_id);
        payload.ministry_id = Number.isNaN(mid) ? null : mid;
        payload.ministry_ids =
            payload.ministry_id !== null ? [payload.ministry_id] : [];
    }

    // 3) Include all staged changes from different tabs
    payload.timelines = (volunteerChanges.timelines || []).map((t) => ({
        organization_name: (t.organization_name || "").trim(),
        year_started: (t.year_started || "").trim(),
        year_ended: (t.year_ended || "").trim(),
        total_years:
            t.total_years !== undefined && t.total_years !== null
                ? Number(t.total_years)
                : null,
        is_active: t.is_active ? 1 : 0,
        id: t.id ?? null,
    }));

    payload.affiliations = (volunteerChanges.affiliations || []).map((a) => ({
        organization_name: (a.organization_name || "").trim(),
        year_started: (a.year_started || "").trim(),
        year_ended: (a.year_ended || "").trim(),
        total_years:
            a.total_years !== undefined && a.total_years !== null
                ? Number(a.total_years)
                : null,
        id: a.id ?? null,
    }));

    // Handle sacraments - they can be strings or objects
    payload.sacraments = (volunteerChanges.sacraments || [])
        .map((s) => {
            if (typeof s === "string") return s.trim();
            if (s && s.sacrament_name) return s.sacrament_name.trim();
            return null;
        })
        .filter(Boolean);

    // Handle formations
    payload.formations = (volunteerChanges.formations || [])
        .map((f) => {
            if (typeof f === "string") {
                // Extract year if present (format: "Formation Name (Year)")
                let formationName = f;
                let year = null;

                if (f.includes("(") && f.includes(")")) {
                    const match = f.match(/(.*)\s\((\d{4})\)/);
                    if (match) {
                        formationName = match[1].trim();
                        year = match[2];
                    }
                }

                return {
                    formation_name: formationName,
                    year: year,
                };
            }
            return {
                formation_name: (f.formation_name || "").trim(),
                year: (f.year && String(f.year).trim()) || null,
                id: f.id ?? null,
            };
        })
        .filter((f) => f.formation_name);

    // 4) Check if there are any changes
    const hasScalars = Object.keys(changed).length > 0;
    const hasArrays =
        payload.timelines.length ||
        payload.affiliations.length ||
        payload.sacraments.length ||
        payload.formations.length;

    if (!hasScalars && !hasArrays) {
        toastr.info("No changes to save.");
        return;
    }

    // 5) Show loading state
    const saveButton = btnEl || document.getElementById("editProfile");
    let originalHTML = null;
    if (saveButton) {
        originalHTML = saveButton.innerHTML;
        saveButton.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                <span>Saving...</span>
            </div>`;
        saveButton.disabled = true;
    }

    // 6) Send the request
    fetch(`/volunteers/${volunteerId}/complete-update`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify(payload),
    })
        .then(async (res) => {
            const text = await res.text();
            let body = {};
            try {
                body = text ? JSON.parse(text) : {};
            } catch {
                body = { raw: text };
            }
            return { ok: res.ok, status: res.status, body };
        })
        .then(({ ok, status, body }) => {
            if (!ok) {
                const msg =
                    body?.message ||
                    body?.error ||
                    body?.raw ||
                    `Update failed with status ${status}`;
                throw new Error(msg);
            }

            toastr.success(body.message || "Profile updated successfully.");

            // Refresh the UI if API returns updated data
            if (body.volunteer) {
                volunteerChanges = {
                    basicInfo: {},
                    timelines: [...(body.volunteer.timelines || [])],
                    affiliations: [...(body.volunteer.affiliations || [])],
                    sacraments: [...(body.volunteer.sacraments || [])],
                    formations: [...(body.volunteer.formations || [])],
                };
                renderEditableProfile(
                    body.volunteer,
                    volunteerId,
                    currentActiveTabId,
                    window.ministriesList
                );
            }
        })
        .catch((err) => {
            console.error("Save failed:", err);
            toastr.error(
                err.message || "There was an error saving the profile."
            );
        })
        .finally(() => {
            if (saveButton) {
                saveButton.innerHTML = originalHTML || "Save Changes";
                saveButton.disabled = false;
            }
        });
}

function wireSaveButtons(volunteerId) {
    const saveButton = document.getElementById("editProfile");
    if (saveButton) {
        saveButton.onclick = (e) =>
            saveAllChanges(volunteerId, e.currentTarget);
        saveButton.setAttribute("data-volunteer-id", volunteerId);
    }
}

// =============================================================================
// PROFILE PICTURE UPLOAD
// =============================================================================

function uploadProfilePicture(event, volunteerId) {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("profile_picture", file);

    fetch(`/volunteers/${volunteerId}/picture`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: formData,
    })
        .then((res) => {
            if (!res.ok) throw new Error("Upload failed");
            return res.json();
        })
        .then(() => {
            toastr.success("Profile picture updated");
            openProfile(volunteerId, currentActiveTabId);
        })
        .catch(() => toastr.error("Failed to update profile picture."));
}

// =============================================================================
// TIMELINE MANAGEMENT
// =============================================================================

function addNewTimelineEntry(volunteerId) {
    const container = document.getElementById("timelines-display");
    const index = container.querySelectorAll(".timeline-entry").length;

    const entryHtml = `
        <div class="timeline-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
            <div class="edit-mode p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                        <input type="text" class="form-input w-full" placeholder="Organization" data-field="organization_name" value="" data-index="${index}">
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                            <select class="form-select w-full year-select" data-field="year_started" data-index="${index}">
                                <option value="">Start Year</option>
                                ${generateYearOptions()}
                            </select>
                        </div>
                        <span class="flex items-end pb-2 text-sm">—</span>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                            <select class="form-select w-full year-select" data-field="year_ended" data-index="${index}">
                                <option value="">End Year</option>
                                <option value="present">Present</option>
                                ${generateYearOptions()}
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Years</label>
                        <input type="number" class="form-input w-full total-years" placeholder="Total" readonly>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button onclick="cancelAddTimeline(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                    <button onclick="saveNewTimeline(this, ${volunteerId}, ${index})" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    `;

    // Remove "no timelines" message if it exists
    const noItemsMsg = container.querySelector("p.text-gray-500");
    if (noItemsMsg) noItemsMsg.remove();

    container.insertAdjacentHTML("beforeend", entryHtml);
    container.lastElementChild.querySelector("input").focus();
    attachYearCalculators();
}

function editTimelineEntry(button, index) {
    const entry = button.closest(".timeline-entry");
    entry.querySelector(".display-mode").classList.add("hidden");
    entry.querySelector(".edit-mode").classList.remove("hidden");
}

function cancelEditTimeline(button) {
    const entry = button.closest(".timeline-entry");
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");
}

function saveTimelineEdit(button, index) {
    const entry = button.closest(".timeline-entry");
    const inputs = entry.querySelectorAll(
        ".edit-mode input, .edit-mode select"
    );
    const data = {};

    inputs.forEach((input) => {
        data[input.dataset.field] = input.value;
    });

    // Handle "present" value and calculate total years
    data.is_active = data.year_ended === "present";
    data.total_years = calculateTotalYears(data.year_started, data.year_ended);

    // Stage into changes
    if (volunteerChanges.timelines[index]) {
        volunteerChanges.timelines[index] = {
            ...volunteerChanges.timelines[index],
            ...data,
        };
    } else {
        volunteerChanges.timelines[index] = { ...data };
    }

    // Update display
    updateTimelineDisplay(entry, data);
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");

    toastr.info('Changes staged. Click "Save Changes" to persist.');
}

function updateTimelineDisplay(entry, data) {
    const displayOrg = entry.querySelector(".display-mode p:first-child");
    const displayYears = entry.querySelector(".display-mode p:nth-child(2)");
    const displayTotal = entry.querySelector(".display-mode p:nth-child(3)");

    if (displayOrg)
        displayOrg.textContent = data.organization_name || "No Organization";
    if (displayYears) {
        displayYears.textContent = `${data.year_started || "?"} - ${
            data.is_active ? "Present" : data.year_ended || "?"
        }`;
    }
    if (displayTotal) {
        displayTotal.textContent = data.total_years
            ? `${data.total_years} year${data.total_years > 1 ? "s" : ""}`
            : "";
    }
}

function deleteTimelineEntry(button, index) {
    if (!confirm("Are you sure you want to delete this timeline entry?"))
        return;
    volunteerChanges.timelines.splice(index, 1);
    button.closest(".timeline-entry").remove();
    toastr.success(
        "Timeline entry removed (staged). Use Save Changes to persist."
    );
}

function saveNewTimeline(button, volunteerId, index) {
    const entry = button.closest(".timeline-entry");
    const inputs = entry.querySelectorAll("input, select");
    const data = {};

    inputs.forEach((input) => {
        data[input.dataset.field] = input.value;
    });

    if (!data.organization_name) {
        toastr.error("Organization name is required");
        return;
    }

    data.total_years = calculateTotalYears(data.year_started, data.year_ended);
    data.is_active = data.year_ended === "present";

    // Stage new timeline
    volunteerChanges.timelines[index] = { ...data };

    // Replace with display view
    entry.innerHTML = `
        <div class="display-mode">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-sm font-medium text-gray-800">${
                        data.organization_name
                    }</p>
                    <p class="text-sm text-gray-500">${
                        data.year_started || "?"
                    } - ${
        data.is_active ? "Present" : data.year_ended || "?"
    }</p>
                    ${
                        data.total_years
                            ? `<p class="text-xs text-gray-400 mt-1">${
                                  data.total_years
                              } year${data.total_years > 1 ? "s" : ""}</p>`
                            : ""
                    }
                </div>
                <div class="flex gap-2">
                    <button onclick="editTimelineEntry(this, ${index})" class="text-gray-400 hover:text-blue-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteTimelineEntry(this, ${index})" class="text-gray-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        ${generateTimelineEditMode(data, index)}
    `;

    toastr.success("Timeline entry staged. Use Save Changes to persist.");
}

function cancelAddTimeline(button) {
    const entry = button.closest(".timeline-entry");
    entry.remove();

    // Show "no timelines" message if container is empty
    const container = document.getElementById("timelines-display");
    if (container.querySelectorAll(".timeline-entry").length === 0) {
        container.innerHTML =
            '<p class="text-gray-500">No timeline entries added yet.</p>';
    }
}

// =============================================================================
// AFFILIATION MANAGEMENT
// =============================================================================

function addNewAffiliation(volunteerId) {
    const container = document.getElementById("affiliations-display");
    const index = container.querySelectorAll(".affiliation-entry").length;

    const entryHtml = `
        <div class="affiliation-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
            <div class="edit-mode p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                        <input type="text" class="form-input w-full" placeholder="Organization" data-field="organization_name" value="" data-index="${index}">
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                            <select class="form-select w-full year-select" data-field="year_started" data-index="${index}">
                                <option value="">Start Year</option>
                                ${generateYearOptions()}
                            </select>
                        </div>
                        <span class="flex items-end pb-2 text-sm">—</span>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                            <select class="form-select w-full year-select" data-field="year_ended" data-index="${index}">
                                <option value="">End Year</option>
                                <option value="present">Present</option>
                                ${generateYearOptions()}
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Years</label>
                        <input type="number" class="form-input w-full total-years" placeholder="Total" readonly>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button onclick="cancelAddAffiliation(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                    <button onclick="saveNewAffiliation(this, ${volunteerId}, ${index})" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    `;

    // Remove "no affiliations" message if it exists
    const noItemsMsg = container.querySelector("p.text-gray-500");
    if (noItemsMsg) noItemsMsg.remove();

    container.insertAdjacentHTML("beforeend", entryHtml);
    container.lastElementChild.querySelector("input").focus();
    attachYearCalculators();
}

function editAffiliation(button, index) {
    const entry = button.closest(".affiliation-entry");
    entry.querySelector(".display-mode").classList.add("hidden");
    entry.querySelector(".edit-mode").classList.remove("hidden");
}

function cancelEditAffiliation(button) {
    const entry = button.closest(".affiliation-entry");
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");
}

function saveAffiliationEdit(button, index) {
    const entry = button.closest(".affiliation-entry");
    const inputs = entry.querySelectorAll(
        ".edit-mode input, .edit-mode select"
    );
    const data = {};

    inputs.forEach((input) => {
        data[input.dataset.field] = input.value;
    });

    // Handle "present" value and calculate total years
    data.is_active = data.year_ended === "present";
    data.total_years = calculateTotalYears(data.year_started, data.year_ended);

    // Stage
    if (volunteerChanges.affiliations[index]) {
        volunteerChanges.affiliations[index] = {
            ...volunteerChanges.affiliations[index],
            ...data,
        };
    } else {
        volunteerChanges.affiliations[index] = { ...data };
    }

    // Update display
    updateAffiliationDisplay(entry, data);
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");

    toastr.info("Affiliation staged. Use Save Changes to persist.");
}

function updateAffiliationDisplay(entry, data) {
    const displayOrg = entry.querySelector(".display-mode p:first-child");
    const displayYears = entry.querySelector(".display-mode p:nth-child(2)");
    const displayTotal = entry.querySelector(".display-mode p:nth-child(3)");

    if (displayOrg)
        displayOrg.textContent = data.organization_name || "No Organization";
    if (displayYears) {
        displayYears.textContent = `${data.year_started || "?"} - ${
            data.is_active ? "Present" : data.year_ended || "?"
        }`;
    }
    if (displayTotal) {
        displayTotal.textContent = data.total_years
            ? `${data.total_years} year${data.total_years > 1 ? "s" : ""}`
            : "";
    }
}

function deleteAffiliation(button, index) {
    if (!confirm("Are you sure you want to delete this affiliation?")) return;
    volunteerChanges.affiliations.splice(index, 1);
    button.closest(".affiliation-entry").remove();
    toastr.success(
        "Affiliation removed (staged). Use Save Changes to persist."
    );
}

function saveNewAffiliation(button, volunteerId, index) {
    const entry = button.closest(".affiliation-entry");
    const inputs = entry.querySelectorAll("input, select");
    const data = {};

    inputs.forEach((input) => {
        data[input.dataset.field] = input.value;
    });

    if (!data.organization_name) {
        toastr.error("Organization name is required");
        return;
    }

    data.total_years = calculateTotalYears(data.year_started, data.year_ended);
    data.is_active = data.year_ended === "present";
    volunteerChanges.affiliations[index] = { ...data };

    // Replace with display view
    entry.innerHTML = `
        <div class="display-mode">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-sm font-medium text-gray-800">${
                        data.organization_name
                    }</p>
                    <p class="text-sm text-gray-500">${
                        data.year_started || "?"
                    } - ${
        data.is_active ? "Present" : data.year_ended || "?"
    }</p>
                    ${
                        data.total_years
                            ? `<p class="text-xs text-gray-400 mt-1">${
                                  data.total_years
                              } year${data.total_years > 1 ? "s" : ""}</p>`
                            : ""
                    }
                </div>
                <div class="flex gap-2">
                    <button onclick="editAffiliation(this, ${index})" class="text-gray-400 hover:text-blue-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteAffiliation(this, ${index})" class="text-gray-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        ${generateAffiliationEditMode(data, index)}
    `;

    toastr.success("Affiliation staged. Use Save Changes to persist.");
}

function cancelAddAffiliation(button) {
    const entry = button.closest(".affiliation-entry");
    entry.remove();

    // Show "no affiliations" message if container is empty
    const container = document.getElementById("affiliations-display");
    if (container.querySelectorAll(".affiliation-entry").length === 0) {
        container.innerHTML =
            '<p class="text-gray-500">No affiliations added yet.</p>';
    }
}

// =============================================================================
// SACRAMENT MANAGEMENT
// =============================================================================

function openSacramentEditor() {
    document.getElementById("sacraments-editor")?.classList.remove("hidden");
}

function closeSacramentEditor() {
    document.getElementById("sacraments-editor")?.classList.add("hidden");
}

function saveSacramentCheckboxes() {
    const checked = Array.from(
        document.querySelectorAll("#sacraments-editor .sacrament-cb:checked")
    ).map((cb) => cb.value);

    if (checked.length === 0) {
        toastr.warning("Please select at least one sacrament.");
        return;
    }

    // Ensure volunteerChanges.sacraments is initialized
    if (!window.volunteerChanges.sacraments) {
        window.volunteerChanges.sacraments = [];
    }

    // Stage the selected sacraments as an array of strings
    window.volunteerChanges.sacraments = [...checked];
    closeSacramentEditor();

    // Re-render display
    const container = document.getElementById("sacraments-display");
    container.innerHTML = checked
        .map(
            (name, idx) => `
        <div class="sacrament-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${idx}">
            <div class="display-mode">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-800">${name}</p>
                        <p class="text-sm text-gray-500">Year not set</p>
                    </div>
                </div>
            </div>
        </div>
    `
        )
        .join("");

    toastr.success("Sacraments staged. Use Save Changes to persist.");
}

// =============================================================================
// FORMATION MANAGEMENT
// =============================================================================

function openFormationEditor(volunteerId) {
    const ed = document.getElementById("formations-editor");
    if (!ed) return;
    ed.classList.remove("hidden");

    // Enable/disable paired year selects when checkbox toggles
    ed.querySelectorAll(".formation-cb").forEach((cb) => {
        cb.addEventListener("change", () => {
            const yearSel = ed.querySelector(
                `.formation-year[data-for="${cb.dataset.name}"]`
            );
            if (yearSel) {
                yearSel.disabled = !cb.checked;
                if (!cb.checked) yearSel.value = "";
            }
        });
    });

    // Setup other formations functionality
    const rows = document.getElementById("other-formation-rows");
    const addBtn = document.getElementById("add-other-formation-row");
    if (addBtn && rows) {
        addBtn.onclick = () => {
            const div = document.createElement("div");
            div.className = "other-formation-row flex items-center gap-2";
            div.innerHTML = `
                <input type="text" placeholder="Formation Name" class="w-48 border rounded px-2 py-1 text-sm other-name">
                <select class="border rounded px-2 py-1 text-sm other-year">
                    <option value="">Select Year</option>
                    ${generateYearOptions()}
                </select>
                <button type="button" class="text-red-600 hover:text-red-800 remove-other">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            rows.appendChild(div);
            div.querySelector(".remove-other").onclick = () => div.remove();
        };
    }

    // Setup remove buttons for existing other formations
    rows?.querySelectorAll(".remove-other").forEach((btn) => {
        btn.onclick = () => btn.closest(".other-formation-row").remove();
    });
}

function closeFormationEditor() {
    document.getElementById("formations-editor")?.classList.add("hidden");
}

function saveFormationCheckboxes() {
    const ed = document.getElementById("formations-editor");
    if (!ed) return;

    const result = [];

    // Standard formations
    ed.querySelectorAll(".formation-cb").forEach((cb) => {
        if (cb.checked) {
            const name = cb.dataset.name;
            const yearSel = ed.querySelector(
                `.formation-year[data-for="${name}"]`
            );
            const year = yearSel?.value || "";
            result.push({ formation_name: name, year: year || null });
        }
    });

    // Other formations
    ed.querySelectorAll("#other-formation-rows .other-formation-row").forEach(
        (row) => {
            const nm = row.querySelector(".other-name")?.value?.trim() || "";
            const yr = row.querySelector(".other-year")?.value || "";
            if (nm) result.push({ formation_name: nm, year: yr || null });
        }
    );

    if (result.length === 0) {
        toastr.warning("Please select or add at least one formation.");
        return;
    }

    // Stage formations
    window.volunteerChanges.formations = result;
    closeFormationEditor();

    // Refresh display
    const container = document.getElementById("formations-display");
    container.innerHTML = result
        .map(
            (f, idx) => `
        <div class="formation-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${idx}">
            <div class="display-mode">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-800">${
                            f.formation_name
                        }</p>
                        <p class="text-sm text-gray-500">${
                            f.year || "Year not set"
                        }</p>
                    </div>
                </div>
            </div>
        </div>
    `
        )
        .join("");

    toastr.success("Formations staged. Use Save Changes to persist.");
}
