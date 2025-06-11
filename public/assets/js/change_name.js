$(document).ready(function () {
    let resendTimer;
    let resendTimeLeft = 60;

    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Load user data on page load
    loadUserData();

    /**
     * Load current user data and populate both display and input fields
     */
   function loadUserData() {
    console.log('AJAX request started'); // This helps check if the function is triggered
    $.ajax({
        url: '/settings/account/user-data', // Ensure this matches your route
        type: 'GET',
        success: function(response) {
            console.log(response);  // Log the response to check data
            if (response.success) {
                const userData = response.data;

                // Populate current information display
                $('#current_full_name').text(userData.full_name || 'Not available');
                $('#current_role').text(userData.role || 'Not available');
                $('#current_email').text(userData.email || 'Not available');
            } else {
                toastr.error('Failed to load user data');
            }
        },
        error: function(xhr) {
            toastr.error('Failed to load user data');
            console.error('Error loading user data:', xhr);
        }
    });
}


    /**
     * Save Profile Button Click Handler
     */
    $("#saveProfileBtn").on("click", function () {
        const firstName = $("#first_name").val().trim();
        const lastName = $("#last_name").val().trim();

        // Validation
        if (!firstName || !lastName) {
            toastr.error("Please fill in both first name and last name");
            return;
        }

        // Show loading state
        showButtonLoading("#saveProfileBtn");

        // Request OTP
        $.ajax({
            url: "settings/account/name-change/request-otp",
            type: "POST",
            data: {
                first_name: firstName,
                last_name: lastName,
            },
            success: function (response) {
                hideButtonLoading("#saveProfileBtn");

                if (response.success) {
                    toastr.success(response.message);
                    showOtpModal();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                hideButtonLoading("#saveProfileBtn");

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

    /**
     * Show OTP Modal
     */
    function showOtpModal() {
        $("#otpModal").removeClass("hidden");
        $("#otpCode").val("").focus();
        resetResendTimer();
    }

    /**
     * Hide OTP Modal
     */
    function hideOtpModal() {
        $("#otpModal").addClass("hidden");
        $("#otpCode").val("");
        clearResendTimer();
    }

    /**
     * Close OTP Modal handlers
     */
    $("#closeOtpModal, #cancelOtpBtn").on("click", function () {
        hideOtpModal();
    });

    /**
     * OTP Code input handler - auto-format and limit to 6 digits
     */
    $("#otpCode").on("input", function () {
        let value = $(this).val().replace(/\D/g, ""); // Remove non-digits
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        $(this).val(value);

        // Auto-submit if 6 digits entered
        if (value.length === 6) {
            $("#verifyOtpBtn").focus();
        }
    });

    /**
     * Verify OTP Button Click Handler
     */
    $("#verifyOtpBtn").on("click", function () {
        const otpCode = $("#otpCode").val().trim();

        if (otpCode.length !== 6) {
            toastr.error("Please enter a valid 6-digit OTP");
            return;
        }

        // Show loading state
        showVerifyLoading();

        $.ajax({
            url: "settings/account/name-change/verify-otp",
            type: "POST",
            data: {
                otp: otpCode,
            },
            success: function (response) {
                hideVerifyLoading();

                if (response.success) {
                    toastr.success(response.message);
                    hideOtpModal();

                    // Update both the display and form fields with new data
                    if (response.data) {
                        $("#current_full_name").text(response.data.full_name);
                        $("#first_name").val(response.data.first_name);
                        $("#last_name").val(response.data.last_name);
                    }
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

    /**
     * Resend OTP Button Click Handler
     */
    $("#resendOtpBtn").on("click", function () {
        if ($(this).prop("disabled")) return;

        $.ajax({
            url: "settings/account/resend-otp",
            type: "POST",
            data: {
                purpose: "name_change",
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    resetResendTimer();
                    $("#otpCode").val("").focus();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error(
                    xhr.responseJSON?.message || "Failed to resend OTP"
                );
            },
        });
    });

    /**
     * Enter key handler for OTP input
     */
    $("#otpCode").on("keypress", function (e) {
        if (e.which === 13) {
            // Enter key
            $("#verifyOtpBtn").click();
        }
    });

    /**
     * Show button loading state
     */
    function showButtonLoading(selector) {
        const $btn = $(selector);
        $btn.prop("disabled", true);
        $btn.find(".btn-text").addClass("hidden");
        $btn.find(".btn-loading").removeClass("hidden");
    }

    /**
     * Hide button loading state
     */
    function hideButtonLoading(selector) {
        const $btn = $(selector);
        $btn.prop("disabled", false);
        $btn.find(".btn-text").removeClass("hidden");
        $btn.find(".btn-loading").addClass("hidden");
    }

    /**
     * Show verify button loading state
     */
    function showVerifyLoading() {
        const $btn = $("#verifyOtpBtn");
        $btn.prop("disabled", true);
        $btn.find(".verify-text").addClass("hidden");
        $btn.find(".verify-loading").removeClass("hidden");
    }

    /**
     * Hide verify button loading state
     */
    function hideVerifyLoading() {
        const $btn = $("#verifyOtpBtn");
        $btn.prop("disabled", false);
        $btn.find(".verify-text").removeClass("hidden");
        $btn.find(".verify-loading").addClass("hidden");
    }

    /**
     * Reset resend timer
     */
    function resetResendTimer() {
        clearResendTimer();
        resendTimeLeft = 60;
        $("#resendOtpBtn").prop("disabled", true);
        $(".resend-text").addClass("hidden");
        $(".resend-timer").removeClass("hidden");
        $("#timerCount").text(resendTimeLeft);

        resendTimer = setInterval(function () {
            resendTimeLeft--;
            $("#timerCount").text(resendTimeLeft);

            if (resendTimeLeft <= 0) {
                clearResendTimer();
                $("#resendOtpBtn").prop("disabled", false);
                $(".resend-text").removeClass("hidden");
                $(".resend-timer").addClass("hidden");
            }
        }, 1000);
    }

    /**
     * Clear resend timer
     */
    function clearResendTimer() {
        if (resendTimer) {
            clearInterval(resendTimer);
            resendTimer = null;
        }
    }

    /**
     * Close modal when clicking outside
     */
    $("#otpModal").on("click", function (e) {
        if (e.target === this) {
            hideOtpModal();
        }
    });

    /**
     * Prevent modal close when clicking inside modal content
     */
    $("#otpModal .bg-white").on("click", function (e) {
        e.stopPropagation();
    });

    /**
     * Format OTP input for better UX
     */
    $("#otpCode").on("paste", function (e) {
        e.preventDefault();
        const paste = (
            e.originalEvent.clipboardData || window.clipboardData
        ).getData("text");
        const digits = paste.replace(/\D/g, "").substring(0, 6);
        $(this).val(digits);

        if (digits.length === 6) {
            $("#verifyOtpBtn").focus();
        }
    });

    /**
     * Prevent non-numeric input
     */
    $("#otpCode").on("keydown", function (e) {
        // Allow: backspace, delete, tab, escape, enter, home, end, left, right
        if (
            $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 35, 36, 37, 39]) !==
                -1 ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)
        ) {
            return;
        }
        // Ensure that it is a number and stop the keypress
        if (
            (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
            (e.keyCode < 96 || e.keyCode > 105)
        ) {
            e.preventDefault();
        }
    });

    /**
     * Cleanup when page unloads
     */
    $(window).on("beforeunload", function () {
        clearResendTimer();
    });
});
