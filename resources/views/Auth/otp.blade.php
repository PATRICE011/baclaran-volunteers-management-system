@extends('components.layout')
@section('title', 'One-Time Password')
@section('content')

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="rounded-xl border bg-card text-card-foreground shadow w-full max-w-md">
        <div class="p-6 space-y-1 text-center">
            <div class="flex justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold tracking-tight">Enter OTP</h3>
            <p class="text-sm text-muted-foreground">We've sent a 6-digit code to your email</p>
        </div>

        <div class="p-6 pt-0">
            @if (session('status'))
            <div class="mb-4 text-sm text-green-600">
                {{ session('status') }}
            </div>
            @endif

            <form action="{{ route('password.verify') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-2">
                    <label for="otp" class="block text-sm font-medium">Enter 6-Digit OTP</label>
                    <div class="otp-container flex justify-center space-x-2">
                        <input type="text" name="otp_1" id="otp_1" maxlength="1"
                            class="otp-input w-12 h-12 text-center text-lg font-bold border rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 ease-in-out transform"
                            oninput="moveToNext(this, 'otp_2')" onkeydown="handleBackspace(this, event)">
                        <input type="text" name="otp_2" id="otp_2" maxlength="1"
                            class="otp-input w-12 h-12 text-center text-lg font-bold border rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 ease-in-out transform"
                            oninput="moveToNext(this, 'otp_3')" onkeydown="handleBackspace(this, event, 'otp_1')">
                        <input type="text" name="otp_3" id="otp_3" maxlength="1"
                            class="otp-input w-12 h-12 text-center text-lg font-bold border rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 ease-in-out transform"
                            oninput="moveToNext(this, 'otp_4')" onkeydown="handleBackspace(this, event, 'otp_2')">
                        <input type="text" name="otp_4" id="otp_4" maxlength="1"
                            class="otp-input w-12 h-12 text-center text-lg font-bold border rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 ease-in-out transform"
                            oninput="moveToNext(this, 'otp_5')" onkeydown="handleBackspace(this, event, 'otp_3')">
                        <input type="text" name="otp_5" id="otp_5" maxlength="1"
                            class="otp-input w-12 h-12 text-center text-lg font-bold border rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 ease-in-out transform"
                            oninput="moveToNext(this, 'otp_6')" onkeydown="handleBackspace(this, event, 'otp_4')">
                        <input type="text" name="otp_6" id="otp_6" maxlength="1"
                            class="otp-input w-12 h-12 text-center text-lg font-bold border rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 ease-in-out transform"
                            onkeydown="handleBackspace(this, event, 'otp_5')">
                    </div>
                    <input type="hidden" name="otp" id="otp_combined">
                    @error('otp')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg shadow-blue-500/20 hover:from-blue-600 hover:to-blue-700 hover:shadow-blue-600/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 ease-in-out">
                    Verify OTP
                </button>
            </form>

            <div class="mt-4 text-center space-y-2">
                <p class="text-sm text-muted-foreground">
                    Didn't receive the code?
                    <button id="resendButton" onclick="resendOTP()"
                        class="text-primary hover:font-bold transition-all duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                        Resend OTP
                    </button>
                    <span id="countdownText" class="text-sm text-muted-foreground">(wait <span id="countdown">60</span> seconds)</span>
                </p>
                <a href="{{ url('/sign-in') }}"
                    class="inline-flex items-center text-sm text-primary hover:font-bold transition-all duration-300 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes digitEnter {
        0% {
            transform: scale(0.8) rotateY(-90deg);
            opacity: 0;
        }

        50% {
            transform: scale(1.1) rotateY(0deg);
            opacity: 0.7;
        }

        100% {
            transform: scale(1) rotateY(0deg);
            opacity: 1;
        }
    }

    @keyframes digitPulse {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }
    }

    @keyframes digitShake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-2px);
        }

        75% {
            transform: translateX(2px);
        }
    }

    .otp-input {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .otp-input:focus {
        animation: digitPulse 0.6s ease-in-out;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .otp-input.filled {
        animation: digitEnter 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: linear-gradient(145deg, #dbeafe, #bfdbfe);
        border-color: #3b82f6;
        color: #1e40af;
    }

    .otp-input.error {
        animation: digitShake 0.5s ease-in-out;
        border-color: #ef4444;
        background: linear-gradient(145deg, #fef2f2, #fee2e2);
    }

    .otp-container {
        perspective: 1000px;
    }
</style>

<script>
    // Countdown variables
    let countdown = 60;
    let countdownElement = null;
    let countdownText = null;
    let resendButton = null;
    let timer = null;

    function startCountdown() {
        if (resendButton && countdownText && countdownElement) {
            resendButton.disabled = true;
            countdownText.style.display = 'inline';
            countdownElement.textContent = countdown;

            clearInterval(timer);
            timer = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(timer);
                    resendButton.disabled = false;
                    countdownText.style.display = 'none';
                    countdown = 60; // reset for next time
                }
            }, 1000);
        }
    }

    function moveToNext(current, nextId) {
        // Add filled animation
        current.classList.add('filled');

        if (current.value.length === 1) {
            const nextInput = document.getElementById(nextId);
            if (nextInput) {
                setTimeout(() => {
                    nextInput.focus();
                }, 200);
            }
        }
        combineOTP();
    }

    function handleBackspace(current, event, prevId) {
        if (event.key === 'Backspace') {
            current.classList.remove('filled');

            if (current.value === '' && prevId) {
                const prevInput = document.getElementById(prevId);
                if (prevInput) {
                    setTimeout(() => {
                        prevInput.focus();
                        prevInput.classList.remove('filled');
                    }, 100);
                }
            }
        }
        setTimeout(combineOTP, 10);
    }

    function combineOTP() {
        const otp1 = document.getElementById('otp_1').value;
        const otp2 = document.getElementById('otp_2').value;
        const otp3 = document.getElementById('otp_3').value;
        const otp4 = document.getElementById('otp_4').value;
        const otp5 = document.getElementById('otp_5').value;
        const otp6 = document.getElementById('otp_6').value;

        document.getElementById('otp_combined').value = otp1 + otp2 + otp3 + otp4 + otp5 + otp6;
    }

    function resendOTP() {
        // Disable the button immediately
        if (resendButton) resendButton.disabled = true;
        if (countdownText) countdownText.style.display = 'inline';

        fetch('{{ route('password.otp.resend') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success toastr notification
                    toastr.success(data.message);
                    // Restart countdown
                    countdown = 60;
                    startCountdown();
                } else {
                    // Show error toastr
                    toastr.error(data.message);
                    // Enable button on error
                    if (resendButton) resendButton.disabled = false;
                    if (countdownText) countdownText.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred. Please try again.');
                if (resendButton) resendButton.disabled = false;
                if (countdownText) countdownText.style.display = 'none';
            });
    }

    function showError() {
        document.querySelectorAll('.otp-input').forEach(input => {
            input.classList.add('error');
            setTimeout(() => {
                input.classList.remove('error');
            }, 500);
        });
    }

    // Allow only numbers and handle animations
    document.querySelectorAll('[name^="otp_"]').forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value) {
                this.classList.add('filled');
            } else {
                this.classList.remove('filled');
            }
        });

        // Add focus animations
        input.addEventListener('focus', function() {
            this.classList.remove('error');
        });

        // Add paste support
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/[^0-9]/g, '').slice(0, 6);

            for (let i = 0; i < digits.length && i < 6; i++) {
                const targetInput = document.getElementById(`otp_${i + 1}`);
                if (targetInput) {
                    targetInput.value = digits[i];
                    targetInput.classList.add('filled');
                    setTimeout(() => {
                        targetInput.dispatchEvent(new Event('input'));
                    }, i * 100);
                }
            }

            const lastFilledIndex = Math.min(digits.length, 6);
            const nextInput = document.getElementById(`otp_${lastFilledIndex + 1}`);
            if (nextInput) {
                setTimeout(() => nextInput.focus(), lastFilledIndex * 100);
            }
        });
    });

    // Initialize first input focus
    document.addEventListener('DOMContentLoaded', function() {
        // Get countdown elements
        countdownElement = document.getElementById('countdown');
        countdownText = document.getElementById('countdownText');
        resendButton = document.getElementById('resendButton');

        // Initialize countdown
        startCountdown();
        document.getElementById('otp_1').focus();
    });
</script>
@endsection