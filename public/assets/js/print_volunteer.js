// Handle print profile button click
document.addEventListener("click", function (e) {
    if (e.target.id === "printProfile" || e.target.closest("#printProfile")) {
        e.preventDefault();

        // Get volunteer ID from the profile content or active profile
        const volunteerId = getCurrentVolunteerId();

        if (volunteerId) {
            // Open print view in new window
            const printUrl = `/volunteers/${volunteerId}/print`;
            const printWindow = window.open(
                printUrl,
                "_blank",
                "width=800,height=600,scrollbars=yes"
            );

            // Focus the new window
            if (printWindow) {
                printWindow.focus();
            }
        } else {
            console.error("No volunteer ID found for printing");
            alert("Cannot print: No volunteer profile is currently open.");
        }
    }
});

// Function to get current volunteer ID from profile content
function getCurrentVolunteerId() {
    // Try to get from the profile content (check if we're in edit mode)
    const profileContent = document.getElementById("profileContent");
    if (profileContent) {
        // Look for any element with data-volunteer-id attribute
        const volunteerElement = profileContent.querySelector(
            "[data-volunteer-id]"
        );
        if (volunteerElement) {
            return volunteerElement.dataset.volunteerId;
        }

        // Alternative: Check if the save button has the volunteer ID
        const saveButton = document.getElementById("editProfile");
        if (saveButton && saveButton.dataset.volunteerId) {
            return saveButton.dataset.volunteerId;
        }
    }

    // Try to get from URL parameters as fallback
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get("volunteer") || urlParams.get("id");
}
