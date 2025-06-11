{{-- resources/views/account.blade.php --}}
@extends('components.layout')

@section('title', 'Account Settings')

@section('styles')
<style>
    .modal-bg {
        background: rgba(0, 0, 0, 0.5);
    }
</style>
@endsection

@section('content')
@include('components.navs')

<main class="flex-1 overflow-auto p-4 sm:p-6">
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{
            tab: 'profile',
            showEmailModal: false,
            showPasswordModal: false
        }">

        {{-- User header --}}
        <div class="flex items-center gap-4 mb-8">
            <div class="relative">
                <span class="relative flex shrink-0 overflow-hidden rounded-full h-16 w-16 border-4 border-primary/10">
                    <img id="profile_picture_display"
                        src="https://api.dicebear.com/7.x/avataaars/svg?seed=admin"
                        alt="Profile Picture"
                        class="h-full w-full object-cover">
                </span>
                <!-- Edit overlay -->
                <button type="button"
                    class="absolute -bottom-1 -right-1 bg-primary text-primary-foreground rounded-full p-1.5 shadow-lg hover:bg-primary/90 transition-colors"
                    onclick="document.getElementById('profile_picture_input').click()">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </button>
                <!-- Hidden file input -->
                <input type="file"
                    id="profile_picture_input"
                    accept="image/*"
                    style="display: none;"
                    onchange="handleProfilePictureChange(this)">
            </div>
            <div>
                <h1 id="user_full_name" class="text-lg font-semibold">Loading...</h1>
                <p id="user_email" class="text-sm text-muted-foreground">Loading...</p>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="h-9 grid grid-cols-3 gap-1 rounded-lg bg-gray-100 p-1 mb-8 text-muted-foreground">
            <button @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-white text-foreground shadow' : ''"
                class="flex items-center justify-center gap-2 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                role="tab">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m8-12a4 4 0 110-8 4 4 0 010 8z" />
                </svg>
                <span>Profile</span>
            </button>
            <button @click="tab = 'email'" :class="tab === 'email' ? 'bg-white text-foreground shadow' : ''"
                class="flex items-center justify-center gap-2 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                role="tab">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <rect x="2" y="4" width="20" height="16" rx="2" ry="2" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 7l-10 6-10-6" />
                </svg>
                <span>Email</span>
            </button>
            <button @click="tab = 'password'" :class="tab === 'password' ? 'bg-white text-foreground shadow' : ''"
                class="flex items-center justify-center gap-2 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                role="tab">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11V7a5 5 0 0110 0v4" />
                </svg>
                <span>Password</span>
            </button>
        </div>


        <!-- Profile Tab -->
        <div x-show="tab === 'profile'" x-cloak role="tabpanel" tabindex="0"
            class="ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 mb-8">
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="p-6 space-y-1.5">
                    <h3 class="text-lg font-semibold">Profile Information</h3>
                    <p class="text-sm text-muted-foreground">Update your personal information here.</p>
                </div>
                <div class="p-6 pt-0">
                    <form id="profileForm" class="space-y-4">
                        <div class="space-y-4">
                            <!-- Current User Info Display -->
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Current Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Full Name:</span>
                                        <span id="current_full_name" class="font-medium ml-2">Loading...</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Role:</span>
                                        <span id="current_role" class="font-medium ml-2">Loading...</span>
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="text-gray-500">Email:</span>
                                        <span id="current_email" class="font-medium ml-2">Loading...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Editable Name Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium">First Name</label>
                                    <input id="first_name" name="first_name" type="text"
                                        class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2"
                                        placeholder="Enter your first name">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium">Last Name</label>
                                    <input id="last_name" name="last_name" type="text"
                                        class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2"
                                        placeholder="Enter your last name">
                                </div>
                            </div>

                            <!-- Info Notice -->
                            <div class="flex items-start space-x-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">Email Verification Required</p>
                                    <p>A verification code will be sent to your registered email address to confirm any name changes.</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end p-6 pt-0">
                    <button type="button" id="saveProfileBtn"
                        class="text-sm rounded-md bg-blue-600 px-4 py-2 font-medium text-white shadow
               transition-colors duration-200 hover:bg-blue-500 focus:outline-none focus:ring-1 focus:ring-ring">
                        <span class="btn-text">Save Changes</span>
                        <span class="btn-loading hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- OTP Verification Modal -->
        <div id="otpModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Verify Your Identity</h3>
                    <button id="closeOtpModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">
                        We've sent a 6-digit verification code to <strong id="otp_email">your registered email</strong>. Please enter it below to confirm your identity and save your name changes.
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label for="otpCode" class="block text-sm font-medium text-gray-700">Verification Code</label>
                            <input type="text" id="otpCode" maxlength="6"
                                class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 text-center text-lg font-mono tracking-widest focus:border-blue-500 focus:ring-blue-500"
                                placeholder="000000">
                        </div>

                        <div class="text-center">
                            <button id="resendOtpBtn" class="text-sm text-blue-600 hover:text-blue-800 disabled:text-gray-400 disabled:cursor-not-allowed">
                                <span class="resend-text">Didn't receive the code? Resend</span>
                                <span class="resend-timer hidden">Resend in <span id="timerCount">60</span>s</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button id="cancelOtpBtn" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button id="verifyOtpBtn" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500 disabled:bg-blue-300">
                        <span class="verify-text">Verify</span>
                        <span class="verify-loading hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verifying...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Email Panel --}}
        <!-- <div x-show="tab === 'email'" x-cloak class="mb-8">
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="p-6 space-y-1.5">
                    <h3 class="text-lg font-semibold">Update Email</h3>
                    <p class="text-sm text-muted-foreground">Change your email address below.</p>
                </div>
                <div class="p-6 pt-0">
                    <form class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium">Email Address</label>
                            <input id="email" type="email" value="admin@churchvolunteers.com"
                                class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2">
                        </div>
                        <div>
                            <label for="role2" class="block text-sm font-medium">Role</label>
                            <input id="role2" value="Administrator" disabled
                                class="mt-1 block w-full rounded-md border-input bg-muted px-3 py-2 text-sm shadow-sm">
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="showEmailModal = true"
                                class="text-sm rounded-md bg-blue-600 px-4 py-2 font-medium text-white shadow
                                           transition-colors duration-200 hover:bg-blue-500 focus:outline-none focus:ring-1 focus:ring-ring">
                                Change Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div> -->

        {{-- Password Panel --}}
        <!-- <div x-show="tab === 'password'" x-cloak>
            <div class="rounded-xl border bg-card text-card-foreground shadow">
                <div class="p-6 space-y-1.5">
                    <h3 class="text-lg font-semibold">Change Password</h3>
                    <p class="text-sm text-muted-foreground">Update your password below.</p>
                </div>
                <div class="p-6 pt-0">
                    <form class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium">Current Password</label>
                            <input id="current_password" type="password" placeholder="Enter Current Password"
                                class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2">
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium">New Password</label>
                            <input id="new_password" type="password" placeholder="Enter New Password"
                                class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium">Confirm Password</label>
                            <input id="confirm_password" type="password" placeholder="Confirm Password"
                                class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2">
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="showPasswordModal = true"
                                class="text-sm rounded-md bg-blue-600 px-4 py-2 font-medium text-white shadow
                                           transition-colors duration-200 hover:bg-blue-500 focus:outline-none focus:ring-1 focus:ring-ring">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div> -->

        {{-- Verification Required Modal (for Email) --}}
        <!-- <div x-show="showEmailModal" x-cloak x-transition.opacity @keydown.escape.window="showEmailModal = false"
            class="fixed inset-0 flex items-center justify-center z-50 modal-bg">
            <div x-show="showEmailModal" x-cloak x-transition @click.away="showEmailModal = false" role="dialog"
                aria-modal="true" aria-labelledby="verify-email-title" aria-describedby="verify-email-desc"
                class="relative grid w-full max-w-lg gap-4 bg-white border p-6 shadow-lg rounded-lg"
                style="transform: translate(-50%, -50%); top:50%; left:50%; position:absolute;">
                <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                    <h2 id="verify-email-title" class="text-lg font-semibold">Verification Required</h2>
                    <p id="verify-email-desc" class="text-sm text-muted-foreground">
                        Please enter the one-time password sent to your email to confirm this change.
                    </p>
                </div>

                <form class="space-y-4 py-4">
                    <div class="grid gap-2">
                        <label for="otp-email" class="text-sm font-medium">One-Time Password</label>
                        <input id="otp-email" type="text" placeholder="Enter OTP"
                            class="w-full h-9 px-3 py-1 text-sm rounded-md border border-input bg-transparent shadow-sm focus:outline-none focus:ring-1 focus:ring-ring transition-colors">
                    </div>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                        <button type="button"
                            class="rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm
                                       hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring"
                            @click="showEmailModal = false">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow
                                       transition-colors duration-200 hover:bg-blue-500 focus:outline-none focus:ring-1 focus:ring-ring">
                            Verify
                        </button>
                    </div>
                </form>

                <button type="button"
                    class="absolute top-4 right-4 opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    @click="showEmailModal = false">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                        <path d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385
                                       10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391
                                       3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557
                                       3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193
                                       12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816
                                       4.03157Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
            </div>
        </div> -->

        {{-- Verification Required Modal (for Password) --}}
        <!-- <div x-show="showPasswordModal" x-cloak x-transition.opacity
            @keydown.escape.window="showPasswordModal = false"
            class="fixed inset-0 flex items-center justify-center z-50 modal-bg">
            <div x-show="showPasswordModal" x-cloak x-transition @click.away="showPasswordModal = false"
                role="dialog" aria-modal="true" aria-labelledby="verify-password-title"
                aria-describedby="verify-password-desc"
                class="relative grid w-full max-w-lg gap-4 bg-white border p-6 shadow-lg rounded-lg"
                style="transform: translate(-50%, -50%); top:50%; left:50%; position:absolute;">
                <div class="flex flex-col space-y-1.5 text-center sm:text-left">
                    <h2 id="verify-password-title" class="text-lg font-semibold">Verification Required</h2>
                    <p id="verify-password-desc" class="text-sm text-muted-foreground">
                        Please enter the one-time password sent to your email to confirm your password change.
                    </p>
                </div>

                <form class="space-y-4 py-4">
                    <div class="grid gap-2">
                        <label for="otp-pass" class="text-sm font-medium">One-Time Password</label>
                        <input id="otp-pass" type="text" placeholder="Enter OTP"
                            class="w-full h-9 px-3 py-1 text-sm rounded-md border border-input bg-transparent shadow-sm focus:outline-none focus:ring-1 focus:ring-ring transition-colors">
                    </div>
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                        <button type="button"
                            class="rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm
                                       hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring"
                            @click="showPasswordModal = false">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow
                                       transition-colors duration-200 hover:bg-blue-500 focus:outline-none focus:ring-1 focus:ring-ring">
                            Verify
                        </button>
                    </div>
                </form>

                <button type="button"
                    class="absolute top-4 right-4 opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    @click="showPasswordModal = false">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                        <path d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385
                                       10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391
                                       3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557
                                       3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193
                                       12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816
                                       4.03157Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
            </div>
        </div> -->

    </div>
</main>
@endsection

@section('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="{{ asset('assets/js/change_name.js') }}"></script>
<script src="{{ asset('assets/js/admin_details.js') }}"></script>
@endsection