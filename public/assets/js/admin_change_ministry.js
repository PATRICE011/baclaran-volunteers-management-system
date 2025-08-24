document.addEventListener("DOMContentLoaded", function () {
    const ministryForm = document.getElementById("ministryForm");
    const ministrySelect = document.getElementById("ministry_id");
    const saveMinistryBtn = document.getElementById("saveMinistryBtn");
    const ministryModal = document.getElementById("ministryModal");
    const ministryName = document.getElementById("ministryName");
    const closeMinistryModal = document.getElementById("closeMinistryModal");
    const cancelMinistryBtn = document.getElementById("cancelMinistryBtn");
    const confirmMinistryBtn = document.getElementById("confirmMinistryBtn");

    // Get ministry name from select option text
    function getMinistryName(ministryId) {
        const option = ministrySelect.querySelector(
            `option[value="${ministryId}"]`
        );
        return option ? option.textContent.trim() : "";
    }

    // Show ministry confirmation modal
    saveMinistryBtn.addEventListener("click", function () {
        const ministryId = ministrySelect.value;
        const ministryNameText = getMinistryName(ministryId);

        if (!ministryId) {
            toastr.error("Please select a ministry");
            return;
        }

        ministryName.textContent = ministryNameText;
        ministryModal.classList.remove("hidden");
    });

    // Close modal handlers
    closeMinistryModal.addEventListener("click", function () {
        ministryModal.classList.add("hidden");
    });

    cancelMinistryBtn.addEventListener("click", function () {
        ministryModal.classList.add("hidden");
    });

    // Confirm ministry change
    confirmMinistryBtn.addEventListener("click", function () {
        const ministryId = ministrySelect.value;
        updateMinistry(ministryId);
    });

    // Update ministry via AJAX
    function updateMinistry(ministryId) {
        // Show loading state
        const confirmText = confirmMinistryBtn.querySelector(".confirm-text");
        const confirmLoading =
            confirmMinistryBtn.querySelector(".confirm-loading");
        const saveText = saveMinistryBtn.querySelector(".btn-text");
        const saveLoading = saveMinistryBtn.querySelector(".btn-loading");

        confirmText.classList.add("hidden");
        confirmLoading.classList.remove("hidden");
        saveText.classList.add("hidden");
        saveLoading.classList.remove("hidden");

        // Make AJAX request
        fetch("/settings/account/update-ministry", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({
                ministry_id: ministryId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    toastr.success("Ministry updated successfully!");
                    ministryModal.classList.add("hidden");
                } else {
                    toastr.error("Error: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                toastr.error("An error occurred. Please try again.");
            })
            .finally(() => {
                // Hide loading state
                confirmText.classList.remove("hidden");
                confirmLoading.classList.add("hidden");
                saveText.classList.remove("hidden");
                saveLoading.classList.add("hidden");
            });
    }
});
