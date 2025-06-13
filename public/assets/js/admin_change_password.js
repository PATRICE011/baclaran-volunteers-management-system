$(document).ready(function () {
    // Handle closing the OTP modal
    $(document).on(
        "click",
        "#closePasswordOtpModal, #cancelPasswordOtpBtn",
        function () {
            // Hide the OTP modal
            $("#passwordOtpModal").addClass("hidden");
            // Clear OTP input
            $("#otpCodePassword").val("");
        }
    );

    // Password Change - Request OTP
    $("#savePassBtn").on("click", function () {
        const currentPassword = $("#current_password").val().trim();
        const newPassword = $("#new_password").val().trim();
        const confirmPassword = $("#confirm_password").val().trim();

        // Validate if current password is provided
        if (!currentPassword) {
            displayError("Current password is required.");
            return;
        }

        // Validate if new password and confirm password match
        if (!newPassword || newPassword !== confirmPassword) {
            displayError("Passwords do not match.");
            return;
        }

        // Validate password rules (must have uppercase and symbol)
        const passwordPattern =
            /^(?=.*[A-Z])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!passwordPattern.test(newPassword)) {
            displayError(
                "Password must be at least 8 characters long, contain at least one uppercase letter and one symbol."
            );
            return;
        }

        // Show loading state
        showButtonLoading("#savePassBtn");

        // Define request data
        const requestData = {
            current_password: currentPassword,
            password: newPassword,
            password_confirmation: confirmPassword, // Laravel expects this for 'confirmed' rule
        };

        // Request OTP for password change using fetch
        fetch("/settings/account/password-change/request-otp", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: JSON.stringify(requestData), // Send requestData here
        })
            .then((response) => response.json())
            .then((data) => {
                hideButtonLoading("#savePassBtn");

                if (data.success) {
                    toastr.success(data.message);
                    showPasswordOtpModal();
                    // Start resend OTP timer after OTP request
                    resetResendTimer($("#passwordOtpModal"));
                } else {
                    toastr.error(data.message);
                }
            })
            .catch((error) => {
                hideButtonLoading("#savePassBtn");
                toastr.error("An error occurred: " + error.message);
            });
    });

    // Helper function to show errors using Toastr
    function displayError(message) {
        toastr.error(message); // Show error toast
    }

    // Show Password OTP Modal
    function showPasswordOtpModal() {
        const $passwordOtpModal = $("#passwordOtpModal");
        $passwordOtpModal.removeClass("hidden");
        // Focus on OTP input
        $("#otpCodePassword").focus();
    }

    // Verify OTP for password change
    $("#verifyPasswordOtpBtn").on("click", function () {
        const otpCode = $("#otpCodePassword").val().trim();
        const newPassword = $("#new_password").val().trim();

        if (otpCode.length !== 6) {
            toastr.error("Please enter a valid 6-digit OTP");
            return;
        }

        if (!newPassword) {
            toastr.error("Password is required");
            return;
        }

        showVerifyLoading();

        // Define request data for verifying OTP
        const requestData = {
            otp: otpCode,
            password: newPassword,
        };

        // Send the verification request
        fetch("/settings/account/password-change/verify-otp", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: JSON.stringify(requestData), // Send OTP code and new password
        })
            .then((response) => response.json())
            .then((data) => {
                hideVerifyLoading();

                if (data.success) {
                    toastr.success(data.message);
                    // Logout user and redirect to login page after 2 seconds
                    setTimeout(function () {
                        toastr.info(
                            "You have been logged out. Please log in again."
                        );
                        window.location.href = "/";
                    }, 2000);
                } else {
                    toastr.error(data.message);
                }
            })
            .catch((error) => {
                hideVerifyLoading();
                toastr.error("An error occurred: " + error.message);
            });
    });

    // Auto-format OTP input (optional enhancement)
    $("#otpCodePassword").on("input", function () {
        let value = $(this).val().replace(/\D/g, ""); // Remove non-digits
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        $(this).val(value);
    });

    // Helper functions (unchanged)
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

    let resendTimers = {}; // Store timers for different modals

    // Reset Resend Timer for a specific modal (purpose)
    function resetResendTimer($modal, purpose) {
        let resendTimeLeft = 60;
        const $resendBtn = $modal.find(`#resendOtpBtn${purpose}`);
        const $timerCount = $modal.find(`#timerCount${purpose}`);

        // Disable resend button while timer is running
        $resendBtn.prop("disabled", true);
        $modal.find(".resend-text").addClass("hidden");
        $modal.find(".resend-timer").removeClass("hidden");
        $timerCount.text(resendTimeLeft);

        // Clear any existing timer for this modal to avoid conflicts
        clearResendTimer(purpose);

        resendTimers[purpose] = setInterval(function () {
            resendTimeLeft--;
            $timerCount.text(resendTimeLeft);

            if (resendTimeLeft <= 0) {
                clearResendTimer(purpose);
                $resendBtn.prop("disabled", false); // Enable resend button once timer expires
                $modal.find(".resend-text").removeClass("hidden");
                $modal.find(".resend-timer").addClass("hidden");
            }
        }, 1000);
    }

    // Clear timer for specific modal (purpose)
    function clearResendTimer(purpose) {
        if (resendTimers[purpose]) {
            clearInterval(resendTimers[purpose]);
            delete resendTimers[purpose];
        }
    }

    // Resend OTP for Password Change
    $("#resendOtpBtnPassword").on("click", function () {
        const otpRequestData = {
            purpose: "password_change", // purpose of OTP request
        };

        // Disable the button to prevent multiple submissions while the request is in progress
        $(this).prop("disabled", true);
        $(this).text("Resending...");

        // Request OTP resend
        fetch("/settings/account/password-change/request-otp", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            body: JSON.stringify(otpRequestData),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    toastr.success(data.message);
                    resetResendTimer($("#passwordOtpModal"), "Password"); // Reset OTP timer for password
                } else {
                    toastr.error(data.message);
                }
            })
            .catch((error) => {
                toastr.error(
                    "An error occurred: " +
                        (error.message || "Please try again later.")
                );
            })
            .finally(() => {
                // Re-enable the button after the request completes
                $("#resendOtpBtnPassword").prop("disabled", false);
                $("#resendOtpBtnPassword").text("Resend OTP");
            });
    });

    // Toggle password visibility (unchanged)
    function togglePasswordVisibility(inputId, toggleButtonId) {
        const input = document.getElementById(inputId);
        const toggleButton = document.getElementById(toggleButtonId);
        const icon = toggleButton.querySelector("svg");

        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>`;
        } else {
            input.type = "password";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88">`;
        }
    }

    // Attach event listeners (unchanged)
    document
        .getElementById("toggleCurrentPassword")
        ?.addEventListener("click", function () {
            togglePasswordVisibility(
                "current_password",
                "toggleCurrentPassword"
            );
        });

    document
        .getElementById("toggleNewPassword")
        ?.addEventListener("click", function () {
            togglePasswordVisibility("new_password", "toggleNewPassword");
        });

    document
        .getElementById("toggleConfirmPassword")
        ?.addEventListener("click", function () {
            togglePasswordVisibility(
                "confirm_password",
                "toggleConfirmPassword"
            );
        });
});
