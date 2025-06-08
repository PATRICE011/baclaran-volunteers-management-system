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
            renderEditableProfile(volunteer, id);
        })
        .catch((error) => {
            console.error("Error fetching volunteer data:", error);
            showProfileError(id);
        });
}

function renderEditableProfile(volunteer, id) {
    const profileContent = document.getElementById("profileContent");

    // Generate avatar seed from name
    const displayName =
        volunteer.nickname || volunteer.detail?.full_name || "No Name";
    const avatarSeed = displayName.replace(/\s/g, "").toLowerCase();
    const imageUrl = volunteer.profile_picture
        ? `/storage/${volunteer.profile_picture}`
        : `https://api.dicebear.com/7.x/avataaars/svg?seed=${avatarSeed}`;

    // Format join date
    const joinDateStr = volunteer.detail?.applied_month_year;
    const joinDate = joinDateStr
        ? new Date(joinDateStr + (joinDateStr.length === 7 ? "-01" : ""))
        : new Date();

    const status = volunteer.detail?.volunteer_status;
    const activeTime = volunteer.active_for || "Duration unknown";

    // Get ministry information
    const ministryName =
        volunteer.detail?.ministry?.ministry_name || "No Ministry Assigned";

    // Get profile completion status
    const volunteerStatus = volunteer.detail?.volunteer_status || "No Status";
    const statusClass =
        volunteerStatus === "Active"
            ? "bg-emerald-100 text-emerald-800 border-emerald-200"
            : "bg-red-100 text-red-800 border-red-200";

    // Format sacraments and formations
    const sacraments = Array.isArray(volunteer.sacraments_received)
        ? volunteer.sacraments_received
        : volunteer.sacraments_received
        ? volunteer.sacraments_received
              .split(",")
              .map((s) => s.trim())
              .filter((s) => s)
        : [];

    const formations = Array.isArray(volunteer.formations_received)
        ? volunteer.formations_received
        : volunteer.formations_received
        ? volunteer.formations_received
              .split(",")
              .map((f) => f.trim())
              .filter((f) => f)
        : [];

    // Get timelines and affiliations
    const timelines = volunteer.timelines || [];
    const affiliations = volunteer.affiliations || [];

    // Build the profile HTML with inline editing
    const html = `
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 -m-6 mb-8 p-8 rounded-t-lg border-b border-gray-100">
            <div class="flex items-start gap-6">
                <div class="relative group">
                    <img src="${imageUrl}" alt="${displayName}" class="w-24 h-24 rounded-full shadow-lg ring-4 ring-white object-cover">
                    <button onclick="editProfilePicture(${id})" class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
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
                    <div class="flex items-center gap-2 mb-2">
                        <h2 class="text-2xl font-bold text-gray-900">${displayName}</h2>
                        <button onclick="editField('nickname', '${
                            volunteer.nickname || ""
                        }', ${id})" class="text-gray-400 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <div class="flex items-center gap-1">
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border ${statusClass}">
                                <span class="w-2 h-2 bg-current rounded-full mr-2"></span>
                                ${volunteerStatus}
                            </span>
                            <button onclick="editStatus(${id}, '${volunteerStatus}')" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-full border border-blue-200">
                            ${ministryName}
                        </span>
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
                </button>
                `
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
                            "Occupation",
                            "occupation",
                            volunteer.occupation,
                            id
                        )}
                    </div>
                    ${
                        volunteer.address
                            ? `
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        ${generateEditableField(
                            "Address",
                            "address",
                            volunteer.address,
                            id,
                            "textarea",
                            true
                        )}
                    </div>
                    `
                            : ""
                    }
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
                            ["male", "female"]
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
                    </div>
                </div>
            </div>

            <!-- Spiritual Journey Tab -->
            ${
                sacraments.length > 0 || formations.length > 0
                    ? `
            <div id="spiritual-tab" class="tab-content hidden">
                <div class="space-y-6">
                    ${
                        sacraments.length > 0
                            ? `
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Sacraments Received
                            </h3>
                            <button onclick="editSacraments(${id}, ${JSON.stringify(
                                  sacraments
                              ).replace(
                                  /"/g,
                                  "&quot;"
                              )})" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-3" id="sacraments-display">
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

                    ${
                        formations.length > 0
                            ? `
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                                </svg>
                                Formations Received
                            </h3>
                            <button onclick="editFormations(${id}, ${JSON.stringify(
                                  formations
                              ).replace(
                                  /"/g,
                                  "&quot;"
                              )})" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-3" id="formations-display">
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
                </div>
            </div>
            `
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
                <button onclick="editTimelines(${id}, ${JSON.stringify(timelines).replace(/"/g, '&quot;')})" class="text-gray-400 hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
                </div>
                <div class="space-y-4" id="timelines-display">
                ${timelines.length > 0 ? generateTimelinesDisplay(timelines) : `<p class="text-gray-500">No timeline entries added yet.</p>`}
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
                        <button onclick="editAffiliations(${id}, ${JSON.stringify(affiliations).replace(/"/g, '&quot;')})" class="text-gray-400 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="affiliations-display">
                        ${affiliations.length > 0 
                            ? generateAffiliationsDisplay(affiliations) 
                            : `<p class="text-gray-500 col-span-full">No affiliations added yet.</p>`}
                    </div>
                </div>
            </div>

        </div>
    `;

    profileContent.innerHTML = html;

    // Update the modal footer button
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
}
// Helper function to generate editable fields
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
    const fieldId = `field-${fieldName}`;
    const inputHtml = (() => {
        if (options && Array.isArray(options)) {
            return `
                <select id="${fieldId}-input" class="form-input hidden w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    ${options
                        .map(
                            (opt) =>
                                `<option value="${opt}" ${
                                    opt === value ? "selected" : ""
                                }>${
                                    opt.charAt(0).toUpperCase() + opt.slice(1)
                                }</option>`
                        )
                        .join("")}
                </select>
            `;
        }

        if (inputType === "textarea") {
            return `
                <textarea id="${fieldId}-input" class="form-textarea hidden w-full mt-1 border-gray-300 rounded-md shadow-sm" rows="3">${
                value || ""
            }</textarea>
            `;
        }

        return `
            <input type="${inputType}" id="${fieldId}-input" value="${
            value || ""
        }" class="form-input hidden w-full mt-1 border-gray-300 rounded-md shadow-sm" />
        `;
    })();

    return `
        <div class="${fullWidth ? "col-span-full" : ""} space-y-1">
            <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">${label}</label>
            <div class="relative group">
                <div id="${fieldId}-display" class="text-gray-800">${displayValue}</div>
                ${inputHtml}
                <button onclick="toggleEditField('${fieldId}')" class="absolute top-0 right-0 mt-1 text-gray-400 hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
}

// Toggle visibility of display/input fields
function toggleEditField(fieldId) {
    const displayEl = document.getElementById(`${fieldId}-display`);
    const inputEl = document.getElementById(`${fieldId}-input`);
    if (displayEl && inputEl) {
        displayEl.classList.toggle("hidden");
        inputEl.classList.toggle("hidden");
    }
}
function editAffiliations(id, affiliations = []) {
    const container = document.getElementById("affiliations-display");
    if (!affiliations.length) {
        affiliations.push({ organization_name: "", year_started: "", year_ended: "", is_active: false });
    }

    container.innerHTML = affiliations.map((a, index) => `
        <div class="affiliation-entry mb-4 space-y-2" data-index="${index}">
            <!-- Your input fields -->
            <input type="text" class="form-input w-full" placeholder="Organization" value="${a.organization_name || ''}" data-field="organization_name" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="Start Year" value="${a.year_started || ''}" data-field="year_started" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="End Year" value="${a.year_ended || ''}" data-field="year_ended" data-index="${index}">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" data-field="is_active" data-index="${index}" ${a.is_active ? 'checked' : ''}>
                Active
            </label>

            <!-- ⛔ Add this button -->
            <button type="button" onclick="deleteEntry(this)" class="text-red-600 text-sm hover:underline">Remove</button>
        </div>

    `).join('') + `
        <div class="pt-4">
            <button onclick="addAffiliation(${id})" class="w-full px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">Add Affiliation</button>
        </div>
    `;
}


function editTimelines(id, timelines = []) {
    const container = document.getElementById("timelines-display");
    if (!timelines.length) {
        timelines.push({ organization_name: "", year_started: "", year_ended: "", total_years: "", is_active: false });
    }

    container.innerHTML = timelines.map((t, index) => `
        <div class="timeline-entry mb-4 space-y-2" data-index="${index}">
            <!-- Your input fields -->
            <input type="text" class="form-input w-full" placeholder="Organization" value="${t.organization_name || ''}" data-field="organization_name" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="Start Year" value="${t.year_started || ''}" data-field="year_started" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="End Year" value="${t.year_ended || ''}" data-field="year_ended" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="Total Years" value="${t.total_years || ''}" data-field="total_years" data-index="${index}">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" data-field="is_active" data-index="${index}" ${t.is_active ? 'checked' : ''}>
                Active
            </label>

            <!-- ⛔ Add this button -->
            <button type="button" onclick="deleteEntry(this)" class="text-red-600 text-sm hover:underline">Remove</button>
        </div>

    `).join('') + `
        <div class="pt-4">
            <button onclick="addTimeline(${id})" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Timeline</button>
        </div>
    `;
}

function addAffiliation(id) {
    const container = document.getElementById("affiliations-display");
    const index = container.querySelectorAll("[data-index]").length / 4;
    container.insertAdjacentHTML("beforeend", `
        <div class="mb-4 space-y-2 border p-4 rounded-lg bg-gray-50">
            <input type="text" class="form-input w-full" placeholder="Organization" data-field="organization_name" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="Start Year" data-field="year_started" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="End Year" data-field="year_ended" data-index="${index}">
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" class="form-checkbox" data-field="is_active" data-index="${index}">
                <span>Active</span>
            </label>
        </div>
    `);
}



function generateTimelinesDisplay(timelines) {
    return timelines.map((t, index) => `
        <div class="timeline-entry border-l-2 border-blue-200 pl-4 relative group space-y-2" data-index="${index}">
            <p class="text-sm text-gray-600 font-medium">${t.organization_name || "No Title"}</p>
            <input type="text" class="form-input w-full hidden" data-field="organization_name" value="${t.organization_name || ''}" data-index="${index}">

            <p class="text-sm text-gray-500">${t.year_started || "?"} - ${t.year_ended || "Present"}</p>
            <div class="flex gap-2 hidden">
                <input type="text" class="form-input w-full" placeholder="Start Year" data-field="year_started" value="${t.year_started || ''}" data-index="${index}">
                <input type="text" class="form-input w-full" placeholder="End Year" data-field="year_ended" value="${t.year_ended || ''}" data-index="${index}">
            </div>

            <p class="text-xs text-gray-400 mt-1">
                ${t.total_years ? `${t.total_years} year${t.total_years > 1 ? 's' : ''}` : "No Duration"}
                ${t.is_active 
                    ? `<span class="inline-block ml-2 text-green-600 text-xs font-semibold">Active</span>` 
                    : `<span class="inline-block ml-2 text-gray-500 text-xs">Inactive</span>`}
            </p>

            <div class="hidden">
                <input type="text" class="form-input w-full" placeholder="Total Years" data-field="total_years" value="${t.total_years || ''}" data-index="${index}">
                <label class="inline-flex items-center mt-1">
                    <input type="checkbox" class="form-checkbox" data-field="is_active" data-index="${index}" ${t.is_active ? 'checked' : ''}>
                    <span class="ml-2 text-sm">Active</span>
                </label>
            </div>
        </div>
    `).join('');
}


function generateAffiliationsDisplay(affiliations) {
    return affiliations.map((a, index) => `
        <div class="affiliation-entry bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm relative space-y-2" data-index="${index}">
            <!-- Display Mode -->
            <div class="font-medium text-gray-800">${a.organization_name || 'Unnamed Organization'}</div>
            <input type="text" class="form-input w-full hidden" data-field="organization_name" value="${a.organization_name || ''}" data-index="${index}">

            <div class="text-sm text-gray-500">
                ${a.year_started ? `From ${a.year_started}` : ""}
                ${a.year_ended ? ` to ${a.year_ended}` : ""}
            </div>
            <div class="flex gap-2 hidden">
                <input type="text" class="form-input w-full" placeholder="Start Year" data-field="year_started" value="${a.year_started || ''}" data-index="${index}">
                <input type="text" class="form-input w-full" placeholder="End Year" data-field="year_ended" value="${a.year_ended || ''}" data-index="${index}">
            </div>

            <div class="text-xs ${a.is_active ? 'text-green-700' : 'text-gray-500'} font-semibold">
                ${a.is_active ? 'Active' : 'Inactive'}
            </div>
            <div class="hidden">
                <label class="inline-flex items-center mt-1">
                    <input type="checkbox" class="form-checkbox" data-field="is_active" data-index="${index}" ${a.is_active ? 'checked' : ''}>
                    <span class="ml-2 text-sm">Active</span>
                </label>
            </div>
        </div>
    `).join('');
}

function addTimeline(id) {
    const container = document.getElementById("timelines-display");
    const index = container.querySelectorAll("[data-index]").length / 5;
    container.insertAdjacentHTML("beforeend", `
        <div class="mb-4 space-y-2 border p-4 rounded-lg bg-gray-50">
            <input type="text" class="form-input w-full" placeholder="Organization" data-field="organization_name" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="Start Year" data-field="year_started" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="End Year" data-field="year_ended" data-index="${index}">
            <input type="text" class="form-input w-full" placeholder="Total Years" data-field="total_years" data-index="${index}">
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" class="form-checkbox" data-field="is_active" data-index="${index}">
                <span>Active</span>
            </label>
        </div>
    `);
}
function deleteEntry(button) {
    const wrapper = button.closest('.timeline-entry, .affiliation-entry');
    if (wrapper) wrapper.remove();
}
document.querySelectorAll(".affiliation-entry").forEach(entry => {
    entry.querySelectorAll("input, div.flex, div.hidden").forEach(el => el.classList.remove("hidden"));
    if (!entry.querySelector('.remove-button')) {
        const btn = document.createElement("button");
        btn.textContent = "Remove";
        btn.className = "remove-button text-red-600 text-sm hover:underline";
        btn.onclick = () => entry.remove();
        entry.appendChild(btn);
    }
});

// Function to collect all edited fields and send update request
function saveAllChanges(volunteerId) {
    const data = {};

    // Collect standard editable fields
    const fields = document.querySelectorAll("[id^='field-']");
    fields.forEach((container) => {
        const input = container.querySelector("input, select, textarea");
        if (input && !input.classList.contains("hidden")) {
            const field = input.id.replace("field-", "").replace("-input", "");
            data[field] = input.value;
        }
    });

    // Collect current timelines
    const timelineBlocks = document.querySelectorAll(".timeline-entry");
    const timelines = [];
    timelineBlocks.forEach((block) => {
        const index = block.dataset.index;
        const entry = {
            organization_name: block.querySelector(`[data-field="organization_name"]`)?.value || '',
            year_started: block.querySelector(`[data-field="year_started"]`)?.value || '',
            year_ended: block.querySelector(`[data-field="year_ended"]`)?.value || '',
            total_years: block.querySelector(`[data-field="total_years"]`)?.value || '',
            is_active: block.querySelector(`[data-field="is_active"]`)?.checked || false,
        };
        timelines.push(entry);
    });
    data.timelines = timelines;

    // Collect current affiliations
    const affiliationBlocks = document.querySelectorAll(".affiliation-entry");
    const affiliations = [];
    affiliationBlocks.forEach((block) => {
        const index = block.dataset.index;
        const entry = {
            organization_name: block.querySelector(`[data-field="organization_name"]`)?.value || '',
            year_started: block.querySelector(`[data-field="year_started"]`)?.value || '',
            year_ended: block.querySelector(`[data-field="year_ended"]`)?.value || '',
            is_active: block.querySelector(`[data-field="is_active"]`)?.checked || false,
        };
        affiliations.push(entry);
    });
    data.affiliations = affiliations;

    // Submit to backend
    fetch(`/volunteers/${volunteerId}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(data),
    })
        .then((response) => {
            if (!response.ok) throw new Error("Failed to save changes");
            return response.json();
        })
        .then(() => openProfile(volunteerId))
        .catch((err) => {
            console.error("Save failed:", err);
            alert("There was an error saving the profile.");
        });
}

function showProfileError(id) {
    const profileContent = document.getElementById("profileContent");
    profileContent.innerHTML = `
        <div class="text-center py-20">
            <h2 class="text-xl font-semibold text-red-600 mb-4">Failed to load profile</h2>
            <p class="text-gray-500 mb-6">We couldn’t retrieve the volunteer’s data at the moment.</p>
            <button onclick="openProfile(${id})" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Retry
            </button>
        </div>
    `;
}
function switchTab(event, tabId) {
    // Remove active class from all tabs
    const tabs = document.querySelectorAll(".profile-tab");
    const tabContents = document.querySelectorAll(".tab-content");

    tabs.forEach((tab) => {
        tab.classList.remove("active-tab", "border-blue-500", "text-blue-600");
        tab.classList.add("border-transparent", "text-gray-500");
    });

    tabContents.forEach((content) => {
        content.classList.add("hidden");
    });

    // Add active class to clicked tab
    event.target.classList.add(
        "active-tab",
        "border-blue-500",
        "text-blue-600"
    );
    event.target.classList.remove("border-transparent", "text-gray-500");

    // Show selected tab content
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
