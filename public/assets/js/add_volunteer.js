document.addEventListener("DOMContentLoaded", function () {
    // Toggle modal visibility
    function toggleModal(id, show = true) {
        const modal = document.getElementById(id);
        modal.classList.replace(
            show ? "hidden" : "flex",
            show ? "flex" : "hidden"
        );
    }

    // Show registration modal
    document.getElementById("addVolunteerBtn").addEventListener("click", () => {
        toggleModal("registrationModal", true);
        // Generate a random volunteer ID if field is empty
        if (!document.querySelector('[name="volunteer_id"]').value) {
            document.querySelector('[name="volunteer_id"]').value =
                "VOL-" + Math.random().toString(36).substr(2, 6).toUpperCase();
        }
    });

    // Close/cancel registration modal and reset state
    ["closeRegistration", "cancelRegistration"].forEach((id) =>
        document.getElementById(id).addEventListener("click", () => {
            toggleModal("registrationModal", false);
            resetVolunteerForm();
            resetModalTabs();
        })
    );

    // Preview profile picture on file input change
    document
        .getElementById("profilePictureInput")
        .addEventListener("change", function (e) {
            const file = e.target.files[0];
            const preview = document.getElementById("profilePreview");
            const removeBtn = document.getElementById("removeProfilePicture");

            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert("File size must be less than 2MB");
                    e.target.value = "";
                    return;
                }

                if (!file.type.startsWith("image/")) {
                    alert("Please select a valid image file");
                    e.target.value = "";
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    removeBtn.classList.remove("hidden");
                };
                reader.readAsDataURL(file);
            }
        });

    // Reset profile picture input and UI
    document
        .getElementById("removeProfilePicture")
        .addEventListener("click", () => {
            document.getElementById("profilePictureInput").value = "";
            document.getElementById("profilePreview").src =
                "data:image/svg+xml,%3csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100' height='100' fill='%23f3f4f6'/%3e%3ctext x='50%25' y='50%25' font-size='14' text-anchor='middle' alignment-baseline='middle' fill='%23374151'%3eNo Image%3c/text%3e%3c/svg%3e";
            document
                .getElementById("removeProfilePicture")
                .classList.add("hidden");
        });

    // Compute and show duration since applied date
    document
        .getElementById("reg-applied-date")
        .addEventListener("change", function () {
            const appliedDate = new Date(this.value + "-01");
            const now = new Date();

            if (!isNaN(appliedDate)) {
                const totalMonths =
                    (now.getFullYear() - appliedDate.getFullYear()) * 12 +
                    now.getMonth() -
                    appliedDate.getMonth();

                const years = Math.floor(totalMonths / 12);
                const months = totalMonths % 12;

                let result = [];
                if (years > 0)
                    result.push(`${years} yr${years > 1 ? "s" : ""}`);
                if (months > 0)
                    result.push(`${months} mo${months > 1 ? "s" : ""}`);

                document.getElementById("reg-regular-duration").value =
                    result.join(" ") || "0";
            }
        });

    // Formation checkbox handlers
    function setupFormationHandlers() {
        // BOS
        const bosCheckbox = document.querySelector(
            'input[name="formations[]"][value="BOS"]'
        );
        const bosYear = document.querySelector('select[name="bos_year"]');

        bosCheckbox.addEventListener("change", function () {
            bosYear.disabled = !this.checked;
            if (!this.checked) bosYear.value = "";
        });

        // Diocesan Basic Formation
        const diocesanCheckbox = document.querySelector(
            'input[name="formations[]"][value="Diocesan Basic Formation"]'
        );
        const diocesanYear = document.querySelector(
            'select[name="diocesan_year"]'
        );

        diocesanCheckbox.addEventListener("change", function () {
            diocesanYear.disabled = !this.checked;
            if (!this.checked) diocesanYear.value = "";
        });

        // Safeguarding Policy
        const safeguardingCheckbox = document.querySelector(
            'input[name="formations[]"][value="Safeguarding Policy"]'
        );
        const safeguardingYear = document.querySelector(
            'select[name="safeguarding_year"]'
        );

        safeguardingCheckbox.addEventListener("change", function () {
            safeguardingYear.disabled = !this.checked;
            if (!this.checked) safeguardingYear.value = "";
        });

        // Other Formation
        const otherFormationCheckbox = document.getElementById(
            "other_formation_check"
        );
        const otherFormationInput = document.getElementById(
            "other_formation_input"
        );
        const otherFormationYear = document.getElementById(
            "other_formation_year"
        );

        otherFormationCheckbox.addEventListener("change", function () {
            otherFormationInput.disabled = !this.checked;
            otherFormationYear.disabled = !this.checked;
            if (!this.checked) {
                otherFormationInput.value = "";
                otherFormationYear.value = "";
            }
        });
    }

    // Add timeline entry
    document
        .getElementById("add-timeline")
        .addEventListener("click", function () {
            const container = document.getElementById("timeline-container");
            const template = container
                .querySelector(".timeline-entry")
                .cloneNode(true);
            template
                .querySelectorAll("input, select")
                .forEach((el) => (el.value = ""));
            container.appendChild(template);
            attachYearCalculators();
        });

    // Add affiliation entry
    document
        .getElementById("add-affiliation")
        .addEventListener("click", function () {
            const container = document.getElementById("affiliations-container");
            const template = container
                .querySelector(".affiliation-entry")
                .cloneNode(true);
            template
                .querySelectorAll("input, select")
                .forEach((el) => (el.value = ""));
            container.appendChild(template);
            attachYearCalculators();
        });

    // Calculate years between start and end dates
    function calculateYears(startYear, endYear) {
        if (!startYear || !endYear) return null;

        if (endYear === "present") {
            const currentYear = new Date().getFullYear();
            return currentYear - parseInt(startYear) + 1;
        }

        return parseInt(endYear) - parseInt(startYear) + 1;
    }

    // Attach event listeners for year calculations
    function attachYearCalculators() {
        document.querySelectorAll(".year-select").forEach((select) => {
            select.removeEventListener("change", handleYearChange);
            select.addEventListener("change", handleYearChange);
        });
    }

    function handleYearChange() {
        const row = this.closest(".grid");
        const startYear = row.querySelector(
            'select[name*="start_year"]'
        )?.value;
        const endYear = row.querySelector('select[name*="end_year"]')?.value;
        const totalInput = row.querySelector(".total-years");

        if (startYear && endYear) {
            const total = calculateYears(startYear, endYear);
            totalInput.value = total > 0 ? total : "";
        } else {
            totalInput.value = "";
        }
    }

    // Initial attachment of year calculators
    attachYearCalculators();
    setupFormationHandlers();

    // Handle "Next" button click from Basic Info to Volunteer's Info
    document.getElementById("nextToSheet")?.addEventListener("click", () => {
        const requiredFields = [
            "volunteer_id",
            "last_name",
            "first_name",
            "nickname",
            "dob",
            "sex",
            "address",
            "phone",
            "email",
            "occupation",
            "civil_status",
        ];

        let valid = true;

        // Check required fields
        requiredFields.forEach((name) => {
            const field = document.querySelector(`[name="${name}"]`);
            if (
                !field ||
                (field.type === "radio" &&
                    !document.querySelector(`[name="${name}"]:checked`)) ||
                (field.type !== "radio" && !field.value)
            ) {
                if (field) field.classList.add("border-red-500");
                valid = false;
            } else {
                if (field) field.classList.remove("border-red-500");
            }
        });

        if (!valid) {
            toastr.warning("Please fill out all required fields.");
            return;
        }

        // Unlock and switch to Volunteer's Info tab
        const infoTab = document.querySelector('.reg-tab[data-tab="sheet"]');
        infoTab.classList.remove("tab-locked");
        infoTab.click();

        // Hide "Next", show "Register" button
        document.getElementById("nextToSheet").classList.add("hidden");
        document
            .getElementById("submitRegistration")
            .classList.remove("hidden");
    });

    // Allow switching between tabs when they are not locked
    document.querySelectorAll(".reg-tab").forEach((tab) => {
        tab.addEventListener("click", function () {
            if (this.classList.contains("tab-locked")) return;

            // Update active tab styles
            document
                .querySelectorAll(".reg-tab")
                .forEach((t) =>
                    t.classList.replace("border-blue-600", "border-transparent")
                );
            this.classList.replace("border-transparent", "border-blue-600");

            // Show corresponding tab content
            document
                .querySelectorAll(".reg-content")
                .forEach((c) => c.classList.add("hidden"));
            document
                .getElementById("tab-" + this.dataset.tab)
                .classList.remove("hidden");
        });
    });

    // Submit registration form
    // In your add_volunteer.js file, replace the formation handling section in submitRegistration with this:

    // Submit registration form
    document
        .getElementById("submitRegistration")
        .addEventListener("click", () => {
            const formData = new FormData();

            // Profile Picture
            const profilePicture = document.querySelector(
                '[name="profile_picture"]'
            );
            if (profilePicture?.files?.[0]) {
                formData.append("profile_picture", profilePicture.files[0]);
            }

            // Basic Fields
            const fields = [
                "volunteer_id",
                "nickname",
                "dob",
                "address",
                "phone",
                "email",
                "occupation",
                "ministry_id",
                "applied_date",
                "regular_duration",
                "last_name",
                "first_name",
                "middle_initial",
                "bos_year",
                "diocesan_year",
                "safeguarding_year",
            ];

            fields.forEach((name) => {
                const el = document.querySelector(`[name="${name}"]`);
                if (el) formData.append(name, el.value);
            });

            // Handle Other Formation properly
            const otherFormationCheckbox = document.getElementById(
                "other_formation_check"
            );
            const otherFormationInput = document.getElementById(
                "other_formation_input"
            );
            const otherFormationYear = document.getElementById(
                "other_formation_year"
            );

            // Add other formation checkbox status
            formData.append(
                "other_formation_check",
                otherFormationCheckbox.checked ? "1" : "0"
            );

            // Only add other formation data if checkbox is checked
            if (otherFormationCheckbox.checked) {
                formData.append(
                    "other_formation",
                    otherFormationInput.value || ""
                );
                formData.append(
                    "other_formation_year",
                    otherFormationYear.value || ""
                );
            }

            formData.append(
                "sex",
                document.querySelector('[name="sex"]:checked')?.value || ""
            );
            formData.append(
                "civil_status",
                document.querySelector('[name="civil_status"]:checked')
                    ?.value || ""
            );

            // Sacraments
            document
                .querySelectorAll('input[name="sacraments[]"]:checked')
                .forEach((cb) => {
                    formData.append("sacraments[]", cb.value);
                });

            // Formations - Fixed to properly handle all formation types
            const selectedFormations = [];

            // Check standard formations
            document
                .querySelectorAll('input[name="formations[]"]:checked')
                .forEach((cb) => {
                    selectedFormations.push(cb.value);
                });

            // Add other formation if checked and has value
            if (
                otherFormationCheckbox.checked &&
                otherFormationInput.value.trim()
            ) {
                selectedFormations.push("Other Formation"); // This will be processed server-side
            }

            // Send all formations
            selectedFormations.forEach((formation) => {
                formData.append("formations[]", formation);
            });

            // Timeline
            [
                "timeline_org",
                "timeline_start_year",
                "timeline_end_year",
                "timeline_total",
            ].forEach((name) => {
                document
                    .querySelectorAll(`[name="${name}[]"]`)
                    .forEach((el) => {
                        formData.append(`${name}[]`, el.value);
                    });
            });

            // Affiliations
            [
                "affil_org",
                "affil_start_year",
                "affil_end_year",
                "affil_total",
            ].forEach((name) => {
                document
                    .querySelectorAll(`[name="${name}[]"]`)
                    .forEach((el) => {
                        formData.append(`${name}[]`, el.value);
                    });
            });

            // Debug: Log form data to console
            console.log("Form data being sent:");
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }

            fetch("/volunteers/register", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: formData,
            })
                .then((res) =>
                    res.json().then((body) => ({
                        ok: res.ok,
                        status: res.status,
                        body,
                    }))
                )
                .then(({ ok, status, body }) => {
                    if (ok) {
                        toastr.success(body.message);
                        toggleModal("registrationModal", false);
                        resetVolunteerForm();
                        switchView(
                            localStorage.getItem("volunteerViewType") || "grid"
                        );
                    } else {
                        const msg =
                            body.message ||
                            "An error occurred during registration.";
                        if (status === 409) toastr.warning(msg);
                        else if (status === 422)
                            toastr.error(
                                body.errors?.profile_picture?.[0] || msg
                            );
                        else toastr.error(msg);
                    }
                })
                .catch((err) => {
                    toastr.error(
                        "An error occurred while registering the volunteer."
                    );
                    console.error(err);
                });
        });
    // Resets the entire volunteer registration form to its default state
    function resetVolunteerForm() {
        // Clear all form inputs (text, email, tel, date, file, textarea, select)
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

        // Uncheck all checkboxes and radio buttons
        document
            .querySelectorAll(
                '#registrationModal input[type="checkbox"], #registrationModal input[type="radio"]'
            )
            .forEach((el) => (el.checked = false));

        // Reset profile picture preview and hide remove button
        const profilePreview = document.getElementById("profilePreview");
        const removeBtn = document.getElementById("removeProfilePicture");
        if (profilePreview) {
            profilePreview.src =
                "data:image/svg+xml,%3csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100' height='100' fill='%23f3f4f6'/%3e%3ctext x='50%25' y='50%25' font-size='14' text-anchor='middle' alignment-baseline='middle' fill='%23374151'%3eNo Image%3c/text%3e%3c/svg%3e";
        }
        if (removeBtn) {
            removeBtn.classList.add("hidden");
        }

        // Reset formation year selects
        document.querySelectorAll(".formation-year").forEach((select) => {
            select.disabled = true;
            select.value = "";
        });

        // Reset other formation input
        document.getElementById("other_formation_check").checked = false;
        document.getElementById("other_formation_input").disabled = true;
        document.getElementById("other_formation_input").value = "";
        document.getElementById("other_formation_year").disabled = true;
        document.getElementById("other_formation_year").value = "";

        // Reset timeline and affiliations to one entry each
        const timelineContainer = document.getElementById("timeline-container");
        const affiliationContainer = document.getElementById(
            "affiliations-container"
        );

        while (timelineContainer.children.length > 1) {
            timelineContainer.removeChild(timelineContainer.lastChild);
        }

        while (affiliationContainer.children.length > 1) {
            affiliationContainer.removeChild(affiliationContainer.lastChild);
        }

        // Reset tab styles: activate the first tab and lock the others
        document.querySelectorAll(".reg-tab").forEach((tab, i) => {
            if (i === 0) {
                tab.classList.replace("border-transparent", "border-blue-600");
                tab.classList.remove("tab-locked");
            } else {
                tab.classList.replace("border-blue-600", "border-transparent");
                tab.classList.add("tab-locked");
            }
        });

        // Show only the first tab content section
        document.querySelectorAll(".reg-content").forEach((content, i) => {
            if (i === 0) {
                content.classList.remove("hidden");
            } else {
                content.classList.add("hidden");
            }
        });

        // Ensure the "Next" button is visible and "Register" is hidden
        document.getElementById("nextToSheet")?.classList.remove("hidden");
        document.getElementById("submitRegistration")?.classList.add("hidden");

        // Scroll the form modal to the top for better UX
        document
            .querySelector("#registrationModal .overflow-y-auto")
            ?.scrollTo(0, 0);
    }

    function resetModalTabs() {
        // Reset tabs to initial state
        document.querySelectorAll(".reg-tab").forEach((tab, i) => {
            if (i === 0) {
                tab.classList.replace("border-transparent", "border-blue-600");
                tab.classList.remove("tab-locked");
            } else {
                tab.classList.replace("border-blue-600", "border-transparent");
                tab.classList.add("tab-locked");
            }
        });

        // Show only the first tab content
        document.querySelectorAll(".reg-content").forEach((content, i) => {
            if (i === 0) {
                content.classList.remove("hidden");
            } else {
                content.classList.add("hidden");
            }
        });

        // Reset buttons
        document.getElementById("nextToSheet").classList.remove("hidden");
        document.getElementById("submitRegistration").classList.add("hidden");
    }
});
