let volunteerChanges = {
    basicInfo: {},
    timelines: [],
    affiliations: [],
    sacraments: [],
    formations: [],
};

// Initialize the changes object when opening a profile
function initializeChanges(volunteerData) {
    volunteerChanges = {
        basicInfo: {},
        timelines: [...(volunteerData.timelines || [])],
        affiliations: [...(volunteerData.affiliations || [])],
        sacraments: [...(volunteerData.sacraments_received || [])],
        formations: [...(volunteerData.formations_received || [])],
    };
}
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

function renderEditableProfile(
    volunteer,
    id,
    activeTabId = "contact-tab",
    ministriesList = []
) {
    initializeChanges(volunteer);
    const profileContent = document.getElementById("profileContent");
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

    const status = volunteer.detail?.volunteer_status;
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
            : "bg-red-100 text-red-800 border-red-200";

    const sacraments = Array.isArray(volunteer.sacraments_received)
        ? volunteer.sacraments_received
        : (volunteer.sacraments_received || "")
              .split(",")
              .map((s) => s.trim())
              .filter((s) => s);

    const formations = Array.isArray(volunteer.formations_received)
        ? volunteer.formations_received
        : (volunteer.formations_received || "")
              .split(",")
              .map((f) => f.trim())
              .filter((f) => f);

    const standardSacraments = [
        "baptism",
        "first_communion",
        "confirmation",
        "marriage",
    ];

    const standardFormations = [
        "BOS",
        "Diocesan Basic Formation",
        "Safeguarding Policy",
    ];

    const timelines = volunteer.timelines || [];
    const affiliations = volunteer.affiliations || [];
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <div class="flex items-center gap-1">
                            <div class="relative group flex items-center">
                                <span id="volunteer-status-display" class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border ${statusClass}">
                                    <span class="w-2 h-2 bg-current rounded-full mr-2"></span>
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
                                </select>
                                <button onclick="toggleEditField('volunteer-status', event)" class="ml-2 text-gray-400 hover:text-blue-600 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="relative group flex items-center">
                            <span id="ministry-display" class="inline-flex items-center px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-full border border-blue-200">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Joined ${joinDate.toLocaleDateString("en-US", {
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
                ${
                    sacraments.length > 0 || formations.length > 0
                        ? `
                <button onclick="switchTab(event, 'spiritual-tab')" class="profile-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Spiritual
                </button>`
                        : ""
                }
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
            ${
                sacraments.length > 0 || formations.length > 0
                    ? `
            <div id="spiritual-tab" class="tab-content hidden">
                <div class="space-y-6">
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Sacraments Received
                            </h3>
                            <button onclick="addNewSacrament(${id})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Sacrament
                            </button>
                        </div>
                        <div class="space-y-4" id="sacraments-display">
                            ${
                                sacraments.length > 0
                                    ? generateSacramentsDisplay(sacraments)
                                    : `<p class="text-gray-500">No sacraments added yet.</p>`
                            }
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                                </svg>
                                Formations Received
                            </h3>
                            <button onclick="addNewFormation(${id})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Formation
                            </button>
                        </div>
                        <div class="space-y-4" id="formations-display">
                            ${
                                formations.length > 0
                                    ? generateFormationsDisplay(formations)
                                    : `<p class="text-gray-500">No formations added yet.</p>`
                            }
                        </div>
                    </div>
                </div>
            </div>`
                    : ""
            }

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
                            timelines.length > 0
                                ? generateTimelinesDisplay(timelines)
                                : `<p class="text-gray-500">No timeline entries added yet.</p>`
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
                        affiliations.length > 0
                            ? generateAffiliationsDisplay(affiliations)
                            : `<p class="text-gray-500">No affiliations added yet.</p>`
                    }
                    </div>

                </div>
            </div>
        </div>
    `;

    profileContent.innerHTML = html;
    const editButton = document.getElementById("editProfile");
    if (editButton) {
        editButton.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            Save Changes
        `;
        editButton.setAttribute("data-volunteer-id", id);
        editButton.onclick = () => saveAllChanges(id);
    }

    setTimeout(() => {
        const activeTabBtn = document.querySelector(
            `button[onclick*='${activeTabId}']`
        );
        if (activeTabBtn) activeTabBtn.click();
    }, 50);
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

function toggleEditField(fieldId, event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    const displayEl = document.getElementById(`${fieldId}-display`);
    const inputEl = document.getElementById(`${fieldId}-input`);

    if (displayEl && inputEl) {
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
                <div class="edit-mode hidden mt-3 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                            <input type="text" class="form-input w-full" value="${
                                t.organization_name || ""
                            }" data-field="organization_name" data-index="${index}">
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                                <select class="form-select w-full year-select" data-field="year_started" data-index="${index}">
                                    <option value="">Start Year</option>
                                    ${generateYearOptions(t.year_started)}
                                </select>
                            </div>
                            <span class="flex items-end pb-2 text-sm">–</span>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                                <select class="form-select w-full year-select" data-field="year_ended" data-index="${index}">
                                    <option value="">End Year</option>
                                    <option value="present" ${
                                        isActive ? "selected" : ""
                                    }>Present</option>
                                    ${generateYearOptions(t.year_ended)}
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Years</label>
                            <input type="number" class="form-input w-full total-years" value="${
                                t.total_years || ""
                            }" readonly>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button onclick="cancelEditTimeline(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                        <button onclick="cancelEditTimeline(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Done</button>
                    </div>
                </div>
            </div>
        `;
        })
        .join("");
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
                                <p class="text-sm text-gray-500">
                                ${a.year_started || "?"} - ${
                isActive ? "Present" : a.year_ended || "?"
            }
                                </p>
                                ${
                                    a.total_years
                                        ? `<p class="text-xs text-gray-400 mt-1">${
                                              a.total_years
                                          } year${
                                              a.total_years > 1 ? "s" : ""
                                          }</p>`
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
                <div class="edit-mode hidden mt-3 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                            <input type="text" class="form-input w-full" value="${
                                a.organization_name || ""
                            }" data-field="organization_name" data-index="${index}">
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                                <select class="form-select w-full year-select" data-field="year_started" data-index="${index}">
                                    <option value="">Start Year</option>
                                    ${generateYearOptions(a.year_started)}
                                </select>
                            </div>
                            <span class="flex items-end pb-2 text-sm">–</span>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                                <select class="form-select w-full year-select" data-field="year_ended" data-index="${index}">
                                    <option value="">End Year</option>
                                    <option value="present" ${
                                        isActive ? "selected" : ""
                                    }>Present</option>
                                    ${generateYearOptions(a.year_ended)}
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Years</label>
                            <input type="number" class="form-input w-full total-years" value="${
                                a.total_years || ""
                            }" readonly>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button onclick="cancelEditAffiliation(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                       <button onclick="cancelEditAffiliation(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Done</button>
                    </div>
                </div>
            </div>
        `;
        })
        .join("");
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

function generateSacramentsDisplay(sacraments) {
    return sacraments
        .map((sacrament, index) => {
            const sacramentParts = sacrament.split(" (");
            const sacramentName = sacramentParts[0];
            const sacramentYear = sacramentParts[1]
                ? sacramentParts[1].replace(")", "")
                : "";

            return `
            <div class="sacrament-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
                <div class="display-mode">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-800">${sacramentName}</p>
                            <p class="text-sm text-gray-500">${
                                sacramentYear ? sacramentYear : "Year not set"
                            }</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editSacrament(this, ${index})" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteSacrament(this, ${index})" class="text-gray-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="edit-mode hidden mt-3 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sacrament Name</label>
                            <input type="text" class="form-input w-full" value="${sacramentName}" data-index="${index}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year Received</label>
                            <select class="form-select w-full" data-index="${index}">
                                <option value="">Select Year</option>
                                ${generateYearOptions(sacramentYear)}
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button onclick="cancelEditSacrament(this, ${index})" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                        <button onclick="cancelEditSacrament(this, ${index})" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Done</button>
                    </div>
                </div>
            </div>
        `;
        })
        .join("");
}

function generateFormationsDisplay(formations) {
    return formations
        .map((formation, index) => {
            const formationParts = formation.split(" (");
            const formationName = formationParts[0];
            const formationYear = formationParts[1]
                ? formationParts[1].replace(")", "")
                : "";

            return `
            <div class="formation-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
                <div class="display-mode">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-800">${formationName}</p>
                            <p class="text-sm text-gray-500">${
                                formationYear ? formationYear : "Year not set"
                            }</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editFormation(this, ${index})" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteFormation(this, ${index})" class="text-gray-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="edit-mode hidden mt-3 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Formation Name</label>
                            <input type="text" class="form-input w-full" value="${formationName}" data-index="${index}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year Received</label>
                            <select class="form-select w-full" data-index="${index}">
                                <option value="">Select Year</option>
                                ${generateYearOptions(formationYear)}
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button onclick="cancelEditFormation(this, ${index})" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                        <button onclick="cancelEditFormation(this, ${index})" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Done</button>
                    </div>
                </div>
            </div>
        `;
        })
        .join("");
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

let currentActiveTabId = "contact-tab";

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

function saveAllChanges(volunteerId) {
    // Collect basic info changes (existing functionality)
    const data = {};
    const editableInputs = document.querySelectorAll(".editable-input");

    editableInputs.forEach((input) => {
        const field = input.dataset.field;
        if (!field) return;

        const original = (input.dataset.original || "").trim();
        let current = (input.value || "").trim();

        if (current !== original) {
            data[field] = current;
        }
    });

    // Add the section changes
    data.timelines = volunteerChanges.timelines;
    data.affiliations = volunteerChanges.affiliations;
    data.sacraments = volunteerChanges.sacraments;
    data.formations = volunteerChanges.formations;

    if (Object.keys(data).length === 0) {
        toastr.info("No changes to save.");
        return;
    }

    // Show loading state
    const saveButton = document.getElementById("saveChanges");
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = `
    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>
    Saving...
  `;
    saveButton.disabled = true;

    // Send all changes in one request
    fetch(`/volunteers/${volunteerId}/complete-update`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify(data),
    })
        .then((res) => {
            if (!res.ok) throw new Error("Update failed");
            return res.json();
        })
        .then(() => {
            toastr.success("All changes saved successfully");
            // Refresh the profile view
            openProfile(volunteerId, currentActiveTabId);
        })
        .catch((err) => {
            console.error("Save failed:", err);
            toastr.error("There was an error saving the profile.");
        })
        .finally(() => {
            // Restore button state
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        });
}

// Timeline CRUD functions
function addNewTimelineEntry(volunteerId) {
    const container = document.getElementById("timelines-display");
    const index = container.querySelectorAll(".timeline-entry").length;

    const entryHtml = `
        <div class="timeline-entry border-l-2 border-blue-200 pl-4 relative group space-y-2" data-index="${index}">
            <div class="edit-mode grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                <input type="text" class="form-input w-full" placeholder="Organization" data-field="organization_name" value="" data-index="${index}">
                <div class="flex gap-2">
                    <select class="form-select w-full year-select" data-field="year_started" data-index="${index}">
                        <option value="">Start Year</option>
                        ${generateYearOptions()}
                    </select>
                    <span class="flex items-center text-sm">–</span>
                    <select class="form-select w-full year-select" data-field="year_ended" data-index="${index}">
                        <option value="">End Year</option>
                        <option value="present">Present</option>
                        ${generateYearOptions()}
                    </select>
                </div>
                <input type="number" class="form-input w-full total-years" placeholder="Total" readonly>
                <div class="col-span-3 flex justify-end gap-2 mt-2">
                    <button onclick="cancelAddTimeline(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                    <button onclick="saveNewTimeline(this, ${volunteerId}, ${index})" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    `;

    // Remove the "no timelines" message if it exists
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

    // Update the changes object
    if (volunteerChanges.timelines[index]) {
        // Update existing entry
        volunteerChanges.timelines[index] = {
            ...volunteerChanges.timelines[index],
            ...data,
        };
    } else {
        // Add new entry
        volunteerChanges.timelines.push(data);
    }

    // Update UI only
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");
    toastr.info(
        "Changes staged. Click 'Save Changes' to save all modifications."
    );
}

function deleteTimelineEntry(button, index) {
    if (confirm("Are you sure you want to delete this timeline entry?")) {
        // Here you would typically make an API call to delete the entry
        button.closest(".timeline-entry").remove();
        toastr.success("Timeline entry deleted");

        // Show "no timelines" message if container is empty
        const container = document.getElementById("timelines-display");
        if (container.querySelectorAll(".timeline-entry").length === 0) {
            container.innerHTML =
                '<p class="text-gray-500">No timeline entries added yet.</p>';
        }
    }
}

function saveNewTimeline(button, volunteerId, index) {
    const entry = button.closest(".timeline-entry");
    const inputs = entry.querySelectorAll("input");
    const data = {};

    inputs.forEach((input) => {
        data[input.dataset.field] = input.value;
    });

    if (!data.organization_name) {
        toastr.error("Organization name is required");
        return;
    }

    // Here you would typically make an API call to save the new timeline
    // For now, we'll just update the display
    entry.innerHTML = `
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-600 font-medium">${
                    data.organization_name || "No Title"
                }</p>
                <input type="text" class="form-input w-full hidden" data-field="organization_name" value="${
                    data.organization_name || ""
                }" data-index="${index}">
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
        
        <div class="display-mode">
            <p class="text-sm text-gray-500">${data.year_started || "?"} - ${
        data.year_ended || "Present"
    }</p>
            ${
                data.total_years
                    ? `<p class="text-xs text-gray-400 mt-1">${
                          data.total_years
                      } year${data.total_years > 1 ? "s" : ""}</p>`
                    : ""
            }
        </div>
        
        <div class="edit-mode hidden grid grid-cols-2 gap-2">
            <div>
                <label class="text-xs text-gray-500">Start Year</label>
                <input type="text" class="form-input w-full" placeholder="Start Year" data-field="year_started" value="${
                    data.year_started || ""
                }" data-index="${index}">
            </div>
            <div>
                <label class="text-xs text-gray-500">End Year</label>
                <input type="text" class="form-input w-full" placeholder="End Year" data-field="year_ended" value="${
                    data.year_ended || ""
                }" data-index="${index}">
            </div>
            <div class="col-span-2">
                <label class="text-xs text-gray-500">Total Years</label>
                <input type="text" class="form-input w-full" placeholder="Total Years" data-field="total_years" value="${
                    data.total_years || ""
                }" data-index="${index}">
            </div>
            <div class="col-span-2 flex justify-end gap-2 mt-2">
                <button onclick="cancelEditTimeline(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button onclick="saveTimelineEdit(this, ${index})" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Save</button>
            </div>
        </div>
    `;

    toastr.success("Timeline entry added");
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

// Affiliation CRUD functions
function addNewAffiliation(volunteerId) {
    const container = document.getElementById("affiliations-display");
    const index = container.querySelectorAll(".affiliation-entry").length;

    const entryHtml = `
    <div class="affiliation-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
        <div class="edit-mode mt-3 p-4 bg-gray-50 rounded-lg">
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
                    <span class="flex items-end pb-2 text-sm">–</span>
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

    // Remove the "no affiliations" message if it exists
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

    // Calculate total years if both start and end years are provided
    if (data.year_started && data.year_ended) {
        const startYear = parseInt(data.year_started);
        const endYear =
            data.year_ended === "present"
                ? new Date().getFullYear()
                : parseInt(data.year_ended);
        data.total_years = endYear - startYear + 1;
        entry.querySelector(".total-years").value = data.total_years;
    }

    // Update the display
    entry.querySelector(".display-mode p:nth-child(1)").textContent =
        data.organization_name || "No Title";
    entry.querySelector(".display-mode p:nth-child(2)").textContent = `${
        data.year_started || "?"
    } - ${data.year_ended || "Present"}`;

    // Switch back to display mode
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");

    // Make API call to save changes
    saveAffiliationToServer(entry.dataset.index, data);
    toastr.info(
        "Changes staged. Click 'Save Changes' to save all modifications."
    );
}

function saveTimelineToServer(index, data) {
    const volunteerId =
        document.getElementById("editProfile")?.dataset.volunteerId;
    if (!volunteerId) return;

    fetch(`/volunteers/${volunteerId}/timeline`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({
            index: index,
            data: data,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                toastr.success("Timeline entry saved successfully");
            } else {
                toastr.error("Failed to save timeline entry");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            toastr.error("An error occurred while saving the timeline entry");
        });
}

function saveAffiliationToServer(index, data) {
    const volunteerId =
        document.getElementById("editProfile")?.dataset.volunteerId;
    if (!volunteerId) return;

    fetch(`/volunteers/${volunteerId}/affiliation`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({
            index: index,
            data: data,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                toastr.success("Affiliation saved successfully");
            } else {
                toastr.error("Failed to save affiliation");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            toastr.error("An error occurred while saving the affiliation");
        });
}

function deleteAffiliation(button, index) {
    if (confirm("Are you sure you want to delete this affiliation?")) {
        // Here you would typically make an API call to delete the entry
        button.closest(".affiliation-entry").remove();
        toastr.success("Affiliation deleted");

        // Show "no affiliations" message if container is empty
        const container = document.getElementById("affiliations-display");
        if (container.querySelectorAll(".affiliation-entry").length === 0) {
            container.innerHTML =
                '<p class="text-gray-500 col-span-full">No affiliations added yet.</p>';
        }
    }
}

function saveNewAffiliation(button, volunteerId, index) {
    const entry = button.closest(".affiliation-entry");
    const inputs = entry.querySelectorAll("input");
    const data = {};

    inputs.forEach((input) => {
        data[input.dataset.field] = input.value;
    });

    if (!data.organization_name) {
        toastr.error("Organization name is required");
        return;
    }

    // Here you would typically make an API call to save the new affiliation
    // For now, we'll just update the display
    entry.innerHTML = `
        <div class="flex justify-between items-start">
            <div>
                <div class="font-medium text-gray-800">${
                    data.organization_name || "Unnamed Organization"
                }</div>
                <input type="text" class="form-input w-full hidden" data-field="organization_name" value="${
                    data.organization_name || ""
                }" data-index="${index}">
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
        
        <div class="display-mode">
        <p class="text-sm text-gray-500">
            ${data.year_started || "?"} - ${data.year_ended || "Present"}
        </p>
        ${
            data.total_years
                ? `<p class="text-xs text-gray-400 mt-1">${
                      data.total_years
                  } year${data.total_years > 1 ? "s" : ""}</p>`
                : ""
        }
        </div>

        
        <div class="edit-mode hidden grid grid-cols-2 gap-2">
            <div>
                <label class="text-xs text-gray-500">Start Year</label>
                <input type="text" class="form-input w-full" placeholder="Start Year" data-field="year_started" value="${
                    data.year_started || ""
                }" data-index="${index}">
            </div>
            <div>
                <label class="text-xs text-gray-500">End Year</label>
                <input type="text" class="form-input w-full" placeholder="End Year" data-field="year_ended" value="${
                    data.year_ended || ""
                }" data-index="${index}">
            </div>
            <div class="col-span-2 flex justify-end gap-2 mt-2">
                <button onclick="cancelEditAffiliation(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button onclick="saveAffiliationEdit(this, ${index})" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Save</button>
            </div>
        </div>
    `;

    toastr.success("Affiliation added");
}

function cancelAddAffiliation(button) {
    const entry = button.closest(".affiliation-entry");
    entry.remove();

    // Show "no affiliations" message if container is empty
    const container = document.getElementById("affiliations-display");
    if (container.querySelectorAll(".affiliation-entry").length === 0) {
        container.innerHTML =
            '<p class="text-gray-500 col-span-full">No affiliations added yet.</p>';
    }
}

function addNewSacrament(volunteerId) {
    const container = document.getElementById("sacraments-display");
    const index = container.querySelectorAll(".sacrament-entry").length;

    const entryHtml = `
        <div class="sacrament-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
            <div class="edit-mode p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sacrament Name</label>
                        <input type="text" class="form-input w-full" data-index="${index}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year Received</label>
                        <select class="form-select w-full" data-index="${index}">
                            <option value="">Select Year</option>
                            ${generateYearOptions()}
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button onclick="cancelAddSacrament(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                    <button onclick="saveNewSacrament(this, ${volunteerId}, ${index})" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    `;

    // Remove the "no sacraments" message if it exists
    const noItemsMsg = container.querySelector("p.text-gray-500");
    if (noItemsMsg) noItemsMsg.remove();

    container.insertAdjacentHTML("beforeend", entryHtml);
    container.lastElementChild.querySelector("input").focus();
}

function editSacrament(button, index) {
    const entry = button.closest(".sacrament-entry");
    entry.querySelector(".display-mode").classList.add("hidden");
    entry.querySelector(".edit-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode input").focus();
}

function cancelEditSacrament(button, index) {
    const entry = button.closest(".sacrament-entry");
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");
}

function saveSacramentEdit(button, index) {
    const entry = button.closest(".sacrament-entry");
    const nameInput = entry.querySelector('input[type="text"]');
    const yearSelect = entry.querySelector("select");

    const name = nameInput.value.trim();
    const year = yearSelect.value;

    if (!name) {
        toastr.error("Sacrament name is required");
        return;
    }

    const sacramentValue = year ? `${name} (${year})` : name;

    // Update display
    entry.querySelector(".display-mode span").textContent = sacramentValue;

    // Switch back to display mode
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");

    // Save to server
    saveSacramentToServer(index, sacramentValue);
    toastr.info(
        "Changes staged. Click 'Save Changes' to save all modifications."
    );
}

function saveSacramentToServer(index, value) {
    const volunteerId =
        document.getElementById("editProfile")?.dataset.volunteerId;
    if (!volunteerId) return;

    // Get current sacraments
    const sacramentsContainer = document.getElementById("sacraments-display");
    const sacraments = Array.from(
        sacramentsContainer.querySelectorAll(".sacrament-entry")
    ).map((entry) => entry.querySelector(".display-mode span").textContent);

    // Update the specific sacrament
    sacraments[index] = value;

    // Send to server
    fetch(`/volunteers/${volunteerId}/sacraments`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ sacraments: sacraments }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                toastr.success("Sacrament updated successfully");
            } else {
                toastr.error("Failed to update sacrament");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            toastr.error("An error occurred while updating the sacrament");
        });
}

function deleteSacrament(button, index) {
    if (confirm("Are you sure you want to delete this sacrament?")) {
        // Here you would typically make an API call to delete the entry
        button.closest(".sacrament-entry").remove();
        toastr.success("Sacrament deleted");

        // Show "no sacraments" message if container is empty
        const container = document.getElementById("sacraments-display");
        if (container.querySelectorAll(".sacrament-entry").length === 0) {
            container.innerHTML =
                '<p class="text-gray-500">No sacraments added yet.</p>';
        }
    }
}

function saveNewSacrament(button, volunteerId, index) {
    const entry = button.closest(".sacrament-entry");
    const nameInput = entry.querySelector('input[type="text"]');
    const yearSelect = entry.querySelector("select");

    const name = nameInput.value.trim();
    const year = yearSelect.value;

    if (!name) {
        toastr.error("Sacrament name is required");
        return;
    }

    const sacramentValue = year ? `${name} (${year})` : name;

    // Create display mode HTML
    const displayHtml = `
    <div class="display-mode">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-800">${sacramentValue.replace(
                    /\s+\(\d{4}\)$/,
                    ""
                )}</p>
                <p class="text-sm text-gray-500">${
                    year ? year : "Year not set"
                }</p>
            </div>
            <div class="flex gap-2">
                <button onclick="editSacrament(this, ${index})" class="text-gray-400 hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
                <button onclick="deleteSacrament(this, ${index})" class="text-gray-400 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="edit-mode hidden mt-3 p-4 bg-gray-50 rounded-lg"></div>
`;

    entry.innerHTML = displayHtml;

    // Save to server
    saveSacramentToServer(index, sacramentValue);
}

function cancelAddSacrament(button) {
    const entry = button.closest(".sacrament-entry");
    entry.remove();

    // Show "no sacraments" message if container is empty
    const container = document.getElementById("sacraments-display");
    if (container.querySelectorAll(".sacrament-entry").length === 0) {
        container.innerHTML =
            '<p class="text-gray-500">No sacraments added yet.</p>';
    }
}

// Formation CRUD functions
function addNewFormation(volunteerId) {
    const container = document.getElementById("formations-display");
    const index = container.querySelectorAll(".formation-entry").length;

    const entryHtml = `
        <div class="formation-entry bg-white border border-gray-200 rounded-lg p-4 mb-3" data-index="${index}">
            <div class="edit-mode p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Formation Name</label>
                        <input type="text" class="form-input w-full" data-index="${index}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year Received</label>
                        <select class="form-select w-full" data-index="${index}">
                            <option value="">Select Year</option>
                            ${generateYearOptions()}
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button onclick="cancelAddFormation(this)" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                    <button onclick="saveNewFormation(this, ${volunteerId}, ${index})" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>
    `;

    // Remove the "no formations" message if it exists
    const noItemsMsg = container.querySelector("p.text-gray-500");
    if (noItemsMsg) noItemsMsg.remove();

    container.insertAdjacentHTML("beforeend", entryHtml);
    container.lastElementChild.querySelector("input").focus();
}

function editFormation(button, index) {
    const entry = button.closest(".formation-entry");
    entry.querySelector(".display-mode").classList.add("hidden");
    entry.querySelector(".edit-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode input").focus();
}

function cancelEditFormation(button, index) {
    const entry = button.closest(".formation-entry");
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");
}

function saveFormationEdit(button, index) {
    const entry = button.closest(".formation-entry");
    const nameInput = entry.querySelector('input[type="text"]');
    const yearSelect = entry.querySelector("select");

    const name = nameInput.value.trim();
    const year = yearSelect.value;

    if (!name) {
        toastr.error("Formation name is required");
        return;
    }

    const formationValue = year ? `${name} (${year})` : name;

    // Update display
    entry.querySelector(".display-mode span").textContent = formationValue;

    // Switch back to display mode
    entry.querySelector(".display-mode").classList.remove("hidden");
    entry.querySelector(".edit-mode").classList.add("hidden");

    // Save to server
    saveFormationToServer(index, formationValue);
    toastr.info(
        "Changes staged. Click 'Save Changes' to save all modifications."
    );
}

function saveFormationToServer(index, value) {
    const volunteerId =
        document.getElementById("editProfile")?.dataset.volunteerId;
    if (!volunteerId) return;

    // Get current formations
    const formationsContainer = document.getElementById("formations-display");
    const formations = Array.from(
        formationsContainer.querySelectorAll(".formation-entry")
    ).map((entry) => entry.querySelector(".display-mode span").textContent);

    // Update the specific formation
    formations[index] = value;

    // Send to server
    fetch(`/volunteers/${volunteerId}/formations`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ formations: formations }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                toastr.success("Formation updated successfully");
            } else {
                toastr.error("Failed to update formation");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            toastr.error("An error occurred while updating the formation");
        });
}

function deleteFormation(button, index) {
    if (confirm("Are you sure you want to delete this formation?")) {
        // Here you would typically make an API call to delete the entry
        button.closest(".formation-entry").remove();
        toastr.success("Formation deleted");

        // Show "no formations" message if container is empty
        const container = document.getElementById("formations-display");
        if (container.querySelectorAll(".formation-entry").length === 0) {
            container.innerHTML =
                '<p class="text-gray-500">No formations added yet.</p>';
        }
    }
}

function saveNewFormation(button, volunteerId, index) {
    const entry = button.closest(".formation-entry");
    const nameInput = entry.querySelector('input[type="text"]');
    const yearSelect = entry.querySelector("select");

    const name = nameInput.value.trim();
    const year = yearSelect.value;

    if (!name) {
        toastr.error("Formation name is required");
        return;
    }

    const formationValue = year ? `${name} (${year})` : name;

    // Create display mode HTML
    const displayHtml = `
    <div class="display-mode">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-gray-800">${formationValue.replace(
                    /\s+\(\d{4}\)$/,
                    ""
                )}</p>
                <p class="text-sm text-gray-500">${
                    year ? year : "Year not set"
                }</p>
            </div>
            <div class="flex gap-2">
                <button onclick="editFormation(this, ${index})" class="text-gray-400 hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
                <button onclick="deleteFormation(this, ${index})" class="text-gray-400 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="edit-mode hidden mt-3 p-4 bg-gray-50 rounded-lg"></div>
`;

    entry.innerHTML = displayHtml;

    // Save to server
    saveFormationToServer(index, formationValue);
}

function cancelAddFormation(button) {
    const entry = button.closest(".formation-entry");
    entry.remove();

    // Show "no formations" message if container is empty
    const container = document.getElementById("formations-display");
    if (container.querySelectorAll(".formation-entry").length === 0) {
        container.innerHTML =
            '<p class="text-gray-500">No formations added yet.</p>';
    }
}

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
                : "bg-red-100 text-red-800 border-red-200";

        displayEl.className = `inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border ${statusClass}`;
        displayEl.innerHTML = `
            <span class="w-2 h-2 bg-current rounded-full mr-2"></span>
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
