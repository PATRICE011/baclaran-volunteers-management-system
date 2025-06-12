$(document).ready(function () {
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Change Password - Request OTP
    $("#savePassBtn").on("click", function () {
        const currentPassword = $("#current_password").val().trim();
        const newPassword = $("#new_password").val().trim();
        const confirmPassword = $("#confirm_password").val().trim();

        // Validation
        if (!newPassword || newPassword !== confirmPassword) {
            toastr.error("Passwords do not match or are invalid.");
            return;
        }

        // Show loading state
        showButtonLoading("#savePassBtn");

        // Request OTP for password change
        $.ajax({
            url: "/settings/account/password-change/request-otp", // Endpoint to request OTP
            type: "POST",
            data: {
                password: newPassword,
            },
            success: function (response) {
                hideButtonLoading("#savePassBtn");

                if (response.success) {
                    toastr.success(response.message);
                    showPasswordOtpModal(); // Show the OTP modal after requesting OTP
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                hideButtonLoading("#savePassBtn");
                toastr.error(xhr.responseJSON?.message || "An error occurred");
            },
        });
    });

    // Show Password OTP Modal
    function showPasswordOtpModal() {
        const $passwordOtpModal = $("#passwordOtpModal"); // Use the newly defined modal
        $passwordOtpModal.removeClass("hidden"); // Show the modal
    }

    // Verify OTP for password change
    $("#verifyPasswordOtpBtn").on("click", function () {
        const otpCode = $("#otpCodePassword").val().trim();
        const newPassword = $("#new_password").val().trim();

        if (otpCode.length !== 6) {
            toastr.error("Please enter a valid 6-digit OTP");
            return;
        }

        showVerifyLoading();

        $.ajax({
            url: "/settings/account/password-change/verify-otp", // Endpoint to verify OTP
            type: "POST",
            data: {
                otp: otpCode,
                password: newPassword,
            },
            success: function (response) {
                hideVerifyLoading();

                if (response.success) {
                    toastr.success(response.message);
                    $("#passwordOtpModal").addClass("hidden"); // Hide modal after successful change
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                hideVerifyLoading();
                toastr.error(
                    xhr.responseJSON?.message || "Verification failed"
                );
            },
        });
    });

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

    function showVerifyLoading() {
        $("#verifyPasswordOtpBtn").prop("disabled", true);
        $("#verifyPasswordOtpBtn .verify-text").addClass("hidden");
        $("#verifyPasswordOtpBtn .verify-loading").removeClass("hidden");
    }

    function hideVerifyLoading() {
        $("#verifyPasswordOtpBtn").prop("disabled", false);
        $("#verifyPasswordOtpBtn .verify-text").removeClass("hidden");
        $("#verifyPasswordOtpBtn .verify-loading").addClass("hidden");
    }
    function togglePasswordVisibility(inputId, toggleButtonId) {
        const input = document.getElementById(inputId);
        const toggleButton = document.getElementById(toggleButtonId);

        if (input.type === "password") {
            input.type = "text";
            toggleButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l18 0M3 12l9-9m0 18l9-9m-9-9L3 12" />
                </svg>
            `;
        } else {
            input.type = "password";
            toggleButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l18 0M3 12l9-9m0 18l9-9m-9-9L3 12" />
                </svg>
            `;
        }
    }

    // Attach event listeners to toggle password visibility
    // Toggle visibility of password fields
    function togglePasswordVisibility(inputId, toggleButtonId) {
        const input = document.getElementById(inputId);
        const toggleButton = document.getElementById(toggleButtonId);
        const icon = toggleButton.querySelector("svg");

        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = `
    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>`;
        } else {
            input.type = "password";
            icon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"> `;
        }
    }

    // Attach event listeners to toggle password visibility
    document
        .getElementById("toggleCurrentPassword")
        .addEventListener("click", function () {
            togglePasswordVisibility(
                "current_password",
                "toggleCurrentPassword"
            );
        });

    document
        .getElementById("toggleNewPassword")
        .addEventListener("click", function () {
            togglePasswordVisibility("new_password", "toggleNewPassword");
        });

    document
        .getElementById("toggleConfirmPassword")
        .addEventListener("click", function () {
            togglePasswordVisibility(
                "confirm_password",
                "toggleConfirmPassword"
            );
        });
});
