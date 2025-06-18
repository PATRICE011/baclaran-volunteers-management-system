@extends('components.layout')
@section('title', 'New Password')
@section('content')

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl border bg-card text-card-foreground shadow w-full max-w-md">
            <div class="p-6 space-y-1 text-center">
                <div class="flex justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold tracking-tight">Reset Password</h3>
                <p class="text-sm text-muted-foreground">Enter your new password</p>
            </div>

            <div class="p-6 pt-0">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ url()->current() }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium">New Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required placeholder="Enter new password"
                                class="w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-primary pr-10">
                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Password strength indicator -->
                        <div id="passwordStrength" class="hidden">
                            <div class="flex space-x-1 mt-2">
                                <div id="strength1" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                                <div id="strength2" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                                <div id="strength3" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                                <div id="strength4" class="h-1 w-1/4 bg-gray-200 rounded"></div>
                            </div>
                            <p id="strengthText" class="text-xs mt-1"></p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                placeholder="Confirm new password"
                                class="w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-primary pr-10">
                            <button type="button" id="togglePasswordConfirm"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg id="eyeIconConfirm" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Password match indicator -->
                        <div id="passwordMatch" class="hidden">
                            <p id="matchText" class="text-xs"></p>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" disabled
                        class="w-full inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg shadow-blue-500/20 hover:from-blue-600 hover:to-blue-700 hover:shadow-blue-600/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 ease-in-out">
                        <svg id="loadingSpinner" class="hidden animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span id="buttonText">Reset Password</span>
                    </button>
                </form>

                <div class="mt-4 text-center">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('submitBtn');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordMatch = document.getElementById('passwordMatch');
            const matchText = document.getElementById('matchText');
            const strengthText = document.getElementById('strengthText');
            const togglePassword = document.getElementById('togglePassword');
            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeIconConfirm = document.getElementById('eyeIconConfirm');

            // Password visibility toggle
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    eyeIcon.innerHTML =
                        '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
                } else {
                    eyeIcon.innerHTML =
                        '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
                }
            });

            togglePasswordConfirm.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);

                if (type === 'text') {
                    eyeIconConfirm.innerHTML =
                        '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
                } else {
                    eyeIconConfirm.innerHTML =
                        '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
                }
            });

            // Password strength checker
            function checkPasswordStrength(password) {
                let score = 0;
                let feedback = [];

                if (password.length >= 8) score++;
                else feedback.push('at least 8 characters');

                if (/[a-z]/.test(password)) score++;
                else feedback.push('lowercase letter');

                if (/[A-Z]/.test(password)) score++;
                else feedback.push('uppercase letter');

                if (/[0-9]/.test(password)) score++;
                else feedback.push('number');

                if (/[^A-Za-z0-9]/.test(password)) score++;
                else feedback.push('special character');

                return {
                    score,
                    feedback
                };
            }

            function updatePasswordStrength(password) {
                const {
                    score,
                    feedback
                } = checkPasswordStrength(password);
                const strengthBars = [
                    document.getElementById('strength1'),
                    document.getElementById('strength2'),
                    document.getElementById('strength3'),
                    document.getElementById('strength4')
                ];

                // Reset all bars
                strengthBars.forEach(bar => {
                    bar.className = 'h-1 w-1/4 bg-gray-200 rounded';
                });

                if (password.length > 0) {
                    passwordStrength.classList.remove('hidden');

                    // Update strength bars based on score
                    for (let i = 0; i < Math.min(score, 4); i++) {
                        if (score <= 2) {
                            strengthBars[i].className = 'h-1 w-1/4 bg-red-500 rounded';
                        } else if (score <= 3) {
                            strengthBars[i].className = 'h-1 w-1/4 bg-yellow-500 rounded';
                        } else {
                            strengthBars[i].className = 'h-1 w-1/4 bg-green-500 rounded';
                        }
                    }

                    // Update strength text
                    if (score <= 2) {
                        strengthText.textContent = 'Weak password';
                        strengthText.className = 'text-xs mt-1 text-red-600';
                    } else if (score <= 3) {
                        strengthText.textContent = 'Medium password';
                        strengthText.className = 'text-xs mt-1 text-yellow-600';
                    } else {
                        strengthText.textContent = 'Strong password';
                        strengthText.className = 'text-xs mt-1 text-green-600';
                    }
                } else {
                    passwordStrength.classList.add('hidden');
                }

                return score;
            }

            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword.length > 0) {
                    passwordMatch.classList.remove('hidden');

                    if (password === confirmPassword) {
                        matchText.textContent = '✓ Passwords match';
                        matchText.className = 'text-xs text-green-600';
                        confirmPasswordInput.classList.remove('border-red-300');
                        confirmPasswordInput.classList.add('border-green-300');
                        return true;
                    } else {
                        matchText.textContent = '✗ Passwords do not match';
                        matchText.className = 'text-xs text-red-600';
                        confirmPasswordInput.classList.remove('border-green-300');
                        confirmPasswordInput.classList.add('border-red-300');
                        return false;
                    }
                } else {
                    passwordMatch.classList.add('hidden');
                    confirmPasswordInput.classList.remove('border-red-300', 'border-green-300');
                    return false;
                }
            }

            function updateSubmitButton() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                const strengthScore = updatePasswordStrength(password);
                const passwordsMatch = checkPasswordMatch();

                const isValid = password.length >= 8 &&
                    strengthScore >= 3 &&
                    passwordsMatch &&
                    confirmPassword.length > 0;

                if (isValid) {
                    submitBtn.disabled = false;
                    submitBtn.className =
                        'w-full inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg shadow-blue-500/20 hover:from-blue-600 hover:to-blue-700 hover:shadow-blue-600/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 ease-in-out';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.className =
                        'w-full inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium text-white bg-gray-400 cursor-not-allowed transition-all duration-300 ease-in-out';
                }
            }

            // Event listeners
            passwordInput.addEventListener('input', updateSubmitButton);
            confirmPasswordInput.addEventListener('input', updateSubmitButton);

            // Form submission with loading state
            document.querySelector('form').addEventListener('submit', function(e) {
                const loadingSpinner = document.getElementById('loadingSpinner');
                const buttonText = document.getElementById('buttonText');

                loadingSpinner.classList.remove('hidden');
                buttonText.textContent = 'Resetting...';
                submitBtn.disabled = true;
            });
        });
    </script>
@endsection
