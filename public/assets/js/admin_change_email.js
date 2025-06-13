$(document).ready(function () {
    // Handle closing the OTP modal
    $(document).on("click", "#closeEmailOtpModal", function () {
        // Hide the OTP modal
        $("#emailOtpModal").addClass("hidden");
    });

    // Handle Email Update Request
    $("#saveEmailBtn").on("click", function () {
        const newEmail = $("#InputEmail").val().trim(); // Ensure this line is present

        // Validation
        if (!newEmail) {
            toastr.error("Please enter a valid email address");
            return;
        }

        const currentEmail = $("#current_email").text().trim();
        if (newEmail === currentEmail) {
            toastr.error("This is already your current email address");
            return;
        }

        // Show loading state for button
        showButtonLoading("#saveEmailBtn");

        // Request OTP for email change
        $.ajax({
            url: "/settings/account/email-change/request-otp", // Endpoint to request OTP
            type: "POST",
            data: { email: newEmail },
            success: function (response) {
                hideButtonLoading("#saveEmailBtn");

                if (response.success) {
                    toastr.success(response.message);
                    showEmailOtpModal(newEmail); // Pass newEmail to modal
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                hideButtonLoading("#saveEmailBtn");
                handleError(xhr);
            },
        });
    });

    // Show Email OTP Modal with the new email
    function showEmailOtpModal(newEmail) {
        const currentEmail = $("#current_email").text().trim(); // Get the current email
        const $emailOtpModal = $("#emailOtpModal"); // Use the newly defined modal
        $emailOtpModal.find("#otp_email_email").text(currentEmail); // Set the email
        $emailOtpModal.removeClass("hidden"); // Show the modal

        setupEmailOtpModalHandlers($emailOtpModal, newEmail); // Pass newEmail to handlers
    }

    // Email OTP Modal Handlers
    function setupEmailOtpModalHandlers($modal, newEmail) {
        $modal.find("#cancelEmailOtpBtn").on("click", function () {
            $modal.addClass("hidden");
        });

        $modal.find("#otpCodeEmail").on("input", function () {
            let value = $(this).val().replace(/\D/g, "");
            if (value.length > 6) value = value.substring(0, 6);
            $(this).val(value);
        });

        // Verify OTP
        $modal.find("#verifyEmailOtpBtn").on("click", function () {
            const otpCode = $modal.find("#otpCodeEmail").val().trim();

            if (otpCode.length !== 6) {
                toastr.error("Please enter a valid 6-digit OTP");
                return;
            }

            showVerifyLoading($modal);

            $.ajax({
                url: "/settings/account/email-change/verify-otp", // Endpoint to verify OTP
                type: "POST",
                data: {
                    otp: otpCode,
                    email: newEmail, // Pass the new email to the request
                },
                success: function (response) {
                    hideVerifyLoading($modal);

                    if (response.success) {
                        toastr.success(response.message);
                        $modal.addClass("hidden");

                        // Update the UI with the new email
                        $("#current_email").text(newEmail);
                        $("#user_email").text(newEmail);
                        $("#InputEmail").val(newEmail);

                        // Logout user and redirect to login page after 2 seconds
                        setTimeout(function () {
                            toastr.info(
                                "You have been logged out. Please log in again."
                            );
                            window.location.href = "/"; // Redirect to login page
                        }, 2000);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    hideVerifyLoading($modal);
                    toastr.error(
                        xhr.responseJSON?.message || "Verification failed"
                    );
                },
            });
        });

        // Resend OTP for Email Change
        $("#resendOtpBtnEmail").on("click", function () {
            const otpRequestData = {
                purpose: "email_change",
                email: newEmail, // Pass the new email for resend
            };

            $(this).prop("disabled", true);
            $(this).text("Resending...");

            $.ajax({
                url: "/settings/account/resend-otp", // Endpoint to resend OTP
                type: "POST",
                data: otpRequestData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        resetResendTimer($("#emailOtpModal"), "Email");
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error(
                        xhr.responseJSON?.message || "Failed to resend OTP"
                    );
                },
                complete: function () {
                    // Re-enable the button after the request completes
                    $("#resendOtpBtnEmail").prop("disabled", false);
                    $("#resendOtpBtnEmail").text("Resend OTP");
                },
            });
        });

        resetResendTimer($modal); // Start the timer for resending OTP
    }

   

    // Helper function to show loading state on button
    function showButtonLoading(selector) {
        const $btn = $(selector);
        $btn.prop("disabled", true);
        $btn.find(".btn-text").addClass("hidden");
        $btn.find(".btn-loading").removeClass("hidden");
    }

    // Hide loading state on button
    function hideButtonLoading(selector) {
        const $btn = $(selector);
        $btn.prop("disabled", false);
        $btn.find(".btn-text").removeClass("hidden");
        $btn.find(".btn-loading").addClass("hidden");
    }

    // Show loading state for OTP verification button
    function showVerifyLoading($modal) {
        const $btn = $modal.find("#verifyEmailOtpBtn");
        $btn.prop("disabled", true);
        $btn.find(".verify-text").addClass("hidden");
        $btn.find(".verify-loading").removeClass("hidden");
    }

    // Hide loading state for OTP verification button
    function hideVerifyLoading($modal) {
        const $btn = $modal.find("#verifyEmailOtpBtn");
        $btn.prop("disabled", false);
        $btn.find(".verify-text").removeClass("hidden");
        $btn.find(".verify-loading").addClass("hidden");
    }

    // Centralized error handler for AJAX responses
    function handleError(xhr) {
        if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors;
            Object.keys(errors).forEach((key) => {
                toastr.error(errors[key][0]);
            });
        } else {
            toastr.error(xhr.responseJSON?.message || "An error occurred");
        }
    }
});
