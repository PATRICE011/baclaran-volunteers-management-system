$(document).ready(function() {
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
      $(document).on('click', '#closeEmailOtpModal', function() {
        // Hide the OTP modal
        $('#emailOtpModal').addClass('hidden');
    });

    // Email Update
    $("#saveEmailBtn").on("click", function() {
        const newEmail = $("#InputEmail").val().trim();

        // Validation
        if (!newEmail) {
            toastr.error("Please enter a valid email address");
            return;
        }

        // Check if email is actually changing
        const currentEmail = $("#current_email").text().trim();
        if (newEmail === currentEmail) {
            toastr.error("This is already your current email address");
            return;
        }

        // Show loading state
        showButtonLoading("#saveEmailBtn");

        // Request OTP for email change
        $.ajax({
            url: "/settings/account/email-change/request-otp", // Endpoint to request OTP
            type: "POST",
            data: {
                email: newEmail,
            },
            success: function(response) {
                hideButtonLoading("#saveEmailBtn");

                if (response.success) {
                    toastr.success(response.message);
                    showEmailOtpModal(); // Show the OTP modal after requesting OTP
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                hideButtonLoading("#saveEmailBtn");

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

    // Show Email OTP Modal
    function showEmailOtpModal() {
        const currentEmail = $("#current_email").text().trim(); // Get the current email
        const $emailOtpModal = $("#emailOtpModal"); // Use the newly defined modal
        $emailOtpModal.find("#otp_email_email").text(currentEmail); // Set the email
        $emailOtpModal.removeClass("hidden"); // Show the modal
        
        setupEmailOtpModalHandlers($emailOtpModal, currentEmail); // Pass current email
    }

    function setupEmailOtpModalHandlers($modal, currentEmail) {
        $modal.find("#cancelEmailOtpBtn").on("click", function() {
            $modal.addClass("hidden");
        });

        $modal.find("#otpCodeEmail").on("input", function() {
            let value = $(this).val().replace(/\D/g, "");
            if (value.length > 6) value = value.substring(0, 6);
            $(this).val(value);
        });

        // Verify OTP
        $modal.find("#verifyEmailOtpBtn").on("click", function() {
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
                    email: currentEmail,
                },
                success: function(response) {
                    hideVerifyLoading($modal);

                    if (response.success) {
                        toastr.success(response.message);
                        $modal.addClass("hidden");
                         $("#current_email").text(newEmail);
                        $("#user_email").text(newEmail);
                        $("#InputEmail").val(newEmail);
                            
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    hideVerifyLoading($modal);
                    toastr.error(xhr.responseJSON?.message || "Verification failed");
                },
            });
        });

        // Resend OTP
        $modal.find("#resendOtpBtnEmail").on("click", function() {
            if ($(this).prop("disabled")) return;

            $.ajax({
                url: "/settings/account/resend-otp", // Endpoint to resend OTP
                type: "POST",
                data: {
                    purpose: "email_change",
                    email: currentEmail,
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        resetResendTimer($modal);
                        $modal.find("#otpCodeEmail").val("").focus();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || "Failed to resend OTP");
                },
            });
        });

        resetResendTimer($modal); // Start the timer for resending OTP
    }

    // Helper functions for UI feedback
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

    function showVerifyLoading($modal) {
        const $btn = $modal.find("#verifyEmailOtpBtn");
        $btn.prop("disabled", true);
        $btn.find(".verify-text").addClass("hidden");
        $btn.find(".verify-loading").removeClass("hidden");
    }

    function hideVerifyLoading($modal) {
        const $btn = $modal.find("#verifyEmailOtpBtn");
        $btn.prop("disabled", false);
        $btn.find(".verify-text").removeClass("hidden");
        $btn.find(".verify-loading").addClass("hidden");
    }

    function resetResendTimer($modal) {
        let resendTimeLeft = 60;
        const $resendBtn = $modal.find("#resendOtpBtnEmail");
        $resendBtn.prop("disabled", true);
        $modal.find(".resend-text").addClass("hidden");
        $modal.find(".resend-timer").removeClass("hidden");
        $modal.find("#timerCountEmail").text(resendTimeLeft);

        const timer = setInterval(function() {
            resendTimeLeft--;
            $modal.find("#timerCountEmail").text(resendTimeLeft);

            if (resendTimeLeft <= 0) {
                clearInterval(timer);
                $resendBtn.prop("disabled", false);
                $modal.find(".resend-text").removeClass("hidden");
                $modal.find(".resend-timer").addClass("hidden");
            }
        }, 1000);
    }
});
