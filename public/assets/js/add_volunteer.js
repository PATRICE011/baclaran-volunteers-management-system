// Utility: Toggle modal visibility
function toggleModal(id, show = true) {
    const modal = document.getElementById(id);
    modal.classList.replace(show ? "hidden" : "flex", show ? "flex" : "hidden");
}

// Show registration modal
document.getElementById("addVolunteerBtn").addEventListener("click", () => {
    toggleModal("registrationModal", true);
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
document.getElementById("profilePictureInput").addEventListener("change", function (e) {
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
document.getElementById("removeProfilePicture").addEventListener("click", () => {
    document.getElementById("profilePictureInput").value = "";
    document.getElementById("profilePreview").src =
        "data:image/svg+xml,%3csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100' height='100' fill='%23f3f4f6'/%3e%3ctext x='50%25' y='50%25' font-size='14' text-anchor='middle' alignment-baseline='middle' fill='%23374151'%3eNo Image%3c/text%3e%3c/svg%3e";
    document.getElementById("removeProfilePicture").classList.add("hidden");
});

// Compute and show duration since applied date
document.getElementById("reg-applied-date").addEventListener("change", function () {
    const appliedDate = new Date(this.value + "-01");
    const now = new Date();

    if (!isNaN(appliedDate)) {
        const totalMonths =
            (now.getFullYear() - appliedDate.getFullYear()) * 12 +
            now.getMonth() - appliedDate.getMonth();

        const years = Math.floor(totalMonths / 12);
        const months = totalMonths % 12;

        let result = [];
        if (years > 0) result.push(`${years} yr${years > 1 ? "s" : ""}`);
        if (months > 0) result.push(`${months} mo${months > 1 ? "s" : ""}`);

        document.getElementById("reg-regular-duration").value = result.join(" ") || "0";
    }
});
// Handle "Next" button click from Basic Info to Info Sheet
document.getElementById("nextToSheet")?.addEventListener("click", () => {
    // Define required input field names
    const requiredFields = ["nickname", "dob", "sex", "address", "phone", "email", "occupation"];
    let valid = true;

    // Check if required fields are filled; highlight invalid ones
    requiredFields.forEach((name) => {
        const field = document.querySelector(`[name="${name}"]`);
        if (!field || !field.value) {
            if (field) field.classList.add("border-red-500");
            valid = false;
        } else {
            field.classList.remove("border-red-500");
        }
    });

    // Additional checks for required radio button groups
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

    // Hide "Next", show "Register" button
    document.getElementById("nextToSheet").classList.add("hidden");
    document.getElementById("submitRegistration").classList.remove("hidden");
});

// Submit registration form
document.getElementById("submitRegistration").addEventListener("click", () => {
    const formData = new FormData();

    // Profile Picture
    const profilePicture = document.querySelector('[name="profile_picture"]');
    if (profilePicture.files[0]) {
        formData.append("profile_picture", profilePicture.files[0]);
    }

    // Fields
    const fields = [
        "nickname", "dob", "address", "phone", "email",
        "occupation", "ministry_id", "applied_date", "regular_duration",
        "last_name", "first_name", "middle_initial"
    ];
    fields.forEach(name =>
        formData.append(name, document.querySelector(`[name="${name}"]`).value)
    );

    formData.append("sex", document.querySelector('[name="sex"]:checked')?.value || "");

    const civilStatus = document.querySelector('[name="civil_status"]:checked')?.value || "";
    formData.append("civil_status", civilStatus);
    if (civilStatus === "others") {
        formData.append("civil_status_other", document.querySelector('[name="civil_status_other"]').value);
    }

    // Checkbox groups
    ["sacraments[]", "formations[]"].forEach(name =>
        document.querySelectorAll(`input[name="${name}"]:checked`)
            .forEach(cb => formData.append(name, cb.value))
    );

    // Timeline
    ["timeline_org", "timeline_start_year", "timeline_end_year", "timeline_total", "timeline_active"].forEach(name =>
        document.querySelectorAll(`[name="${name}[]"]`).forEach((el, i) => {
            formData.append(`${name}[${i}]`, el.value);
        })
    );

    // Affiliations
    ["affil_org", "affil_start_year", "affil_end_year", "affil_active"].forEach(name =>
        document.querySelectorAll(`[name="${name}[]"]`).forEach((el, i) => {
            formData.append(`${name}[${i}]`, el.value);
        })
    );

    fetch("/volunteers/register", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData,
    })
        .then(res => res.json().then(body => ({ ok: res.ok, status: res.status, body })))
        .then(({ ok, status, body }) => {
            if (ok) {
                toastr.success(body.message);
                toggleModal("registrationModal", false);
                resetVolunteerForm();
                switchView(localStorage.getItem("volunteerViewType") || "grid");
            } else {
                const msg = body.message || "An error occurred during registration.";
                if (status === 409) toastr.warning(msg);
                else if (status === 422) toastr.error(body.errors?.profile_picture?.[0] || msg);
                else toastr.error(msg);
            }
        })
        .catch(err => {
            toastr.error("An error occurred while registering the volunteer.");
            console.error(err);
        });
});
// Allow switching between tabs when they are not locked
document.querySelectorAll(".reg-tab").forEach((tab) => {
    tab.addEventListener("click", function () {
        if (this.classList.contains("tab-locked")) return;

        // Update active tab styles
        document.querySelectorAll(".reg-tab").forEach((t) =>
            t.classList.replace("border-blue-600", "border-transparent")
        );
        this.classList.replace("border-transparent", "border-blue-600");

        // Show corresponding tab content
        document.querySelectorAll(".reg-content").forEach((c) =>
            c.classList.add("hidden")
        );
        document.getElementById("tab-" + this.dataset.tab).classList.remove("hidden");
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
            '#registrationModal textarea, ' +
            '#registrationModal select'
        )
        .forEach((el) => (el.value = ""));

    // Uncheck all checkboxes and radio buttons
    document
        .querySelectorAll('#registrationModal input[type="checkbox"], #registrationModal input[type="radio"]')
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

    // Hide the "other" input for civil status if shown
    const otherInput = document.getElementById("civilOtherInput");
    if (otherInput) otherInput.classList.add("hidden");

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
    document.querySelector("#registrationModal .overflow-y-auto")?.scrollTo(0, 0);
}
