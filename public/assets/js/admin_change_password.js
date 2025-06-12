$(document).ready(function() {
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Password Update
    $("#savePassBtn").on("click", function() {
        const currentPassword = $("#current_password").val().trim();
        const newPassword = $("#new_password").val().trim();
        const confirmPassword = $("#confirm_password").val().trim();

        // Validation
        if (!currentPassword || !newPassword || !confirmPassword) {
            toastr.error("Please fill in all password fields");
            return;
        }

        if (newPassword !== confirmPassword) {
            toastr.error("New password and confirmation do not match");
            return;
        }

        if (newPassword.length < 8) {
            toastr.error("Password must be at least 8 characters long");
            return;
        }

        // Show loading state
        showButtonLoading("#savePassBtn");

        // Send password change request
        $.ajax({
            url: "/settings/account/change-password",
            type: "POST",
            data: {
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: confirmPassword,
            },
            success: function(response) {
                hideButtonLoading("#savePassBtn");

                if (response.success) {
                    toastr.success(response.message);
                    
                    // Clear password fields
                    $("#current_password").val("");
                    $("#new_password").val("");
                    $("#confirm_password").val("");
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                hideButtonLoading("#savePassBtn");

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach((key) => {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error(
                        xhr.responseJSON?.message || "An error occurred"
                    );
                }
            },
        });
    });

    // Helper functions
    function showButtonLoading(selector) {
        const $btn = $(selector);
        $btn.prop("disabled", true);
        $btn.find(".btn-text").addClass("hidden");
        $btn.find(".btn-loading").removeClass("hidden");
    }

    function hideButtonLoading(selector) {
        const $btn = $(selector);
        $btn.prop("disabled", false);
        $btn.find(".btn-text").removeClass("hidden");
        $btn.find(".btn-loading").addClass("hidden");
    }
});