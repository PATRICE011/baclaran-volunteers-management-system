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
@include('components.settings_nav')

<div class="md:ml-64">
    <main class="flex-1 overflow-auto p-4 sm:p-6">
        <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{
            tab: 'ministry', // Changed from 'profile' to 'ministry'
            showEmailModal: false,
            showPasswordModal: false
        }">


              {{-- User header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="relative">
                    <span class="relative flex shrink-0 overflow-hidden rounded-full h-16 w-16 border-4 border-primary/10">
                        <img id="profile_picture_display"
                            src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $user->email }}"
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
                    <h1 class="text-lg font-semibold">{{ $user->email }}</h1>
                    <p class="text-xs sm:text-sm text-muted-foreground">
                        @if($user->role == 'admin')
                        Administrator
                        @elseif($user->role == 'staff')
                        Authorized Member
                        @else
                        {{ $user->role }}
                        @endif
                    </p>
                </div>
            </div>

              {{-- Tabs --}}
            <div class="h-9 grid grid-cols-3 gap-1 rounded-lg bg-gray-100 p-1 mb-8 text-muted-foreground">
                <button @click="tab = 'ministry'" :class="tab === 'ministry' ? 'bg-white text-foreground shadow' : ''"
                    class="flex items-center justify-center gap-2 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                    role="tab">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0H9m4 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v12m4 0V9" />
                    </svg>
                    <span>Ministry</span>
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

            <!-- Ministry Tab -->
            <div x-show="tab === 'ministry'" x-cloak role="tabpanel" tabindex="0"
                class="ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 mb-8">
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 space-y-1.5">
                        <h3 class="text-lg font-semibold">Ministry Assignment</h3>
                        <p class="text-sm text-muted-foreground">Update your ministry assignment here.</p>
                    </div>
                    <div class="p-6 pt-0">
                          <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700">Current Ministry:</p>
                        <p class="text-sm text-gray-600 mt-1">
                            @if($user->ministry)
                                {{ $user->ministry->ministry_name }}
                            @else
                                No ministry assigned
                            @endif
                        </p>
                    </div>
                        <form id="ministryForm" class="space-y-4">
                            <div>
                                <label for="ministry_id" class="block text-sm font-medium">Current Ministry</label>
                                <div class="relative mt-1">
                                    <select id="ministry_id" name="ministry_id" class="w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2">
                                        <option value="">-- Select Ministry --</option>
                                        @foreach ($ministries as $main)
                                            <optgroup label="{{ $main->ministry_name }}">
                                                @foreach ($main->children as $ministry)
                                                    <option value="{{ $ministry->id }}" {{ $user->ministry_id == $ministry->id ? 'selected' : '' }}>
                                                        {{ $ministry->ministry_name }}
                                                    </option>
                                                    {{-- Render sub-groups --}}
                                                    @if ($ministry->children->count())
                                                        @foreach ($ministry->children as $sub)
                                                            <option value="{{ $sub->id }}" {{ $user->ministry_id == $sub->id ? 'selected' : '' }}>
                                                                &nbsp;&nbsp;&nbsp;→ {{ $sub->ministry_name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="flex justify-end p-6 pt-0">
                        <button type="button" id="saveMinistryBtn"
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

            <!-- Ministry Confirmation Modal -->
            <div id="ministryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Confirm Ministry Change</h3>
                        <button id="closeMinistryModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-4">
                            Are you sure you want to change your ministry assignment to <strong id="ministryName"></strong>?
                        </p>
                    </div>

                    <div class="flex space-x-3">
                        <button id="cancelMinistryBtn" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button id="confirmMinistryBtn" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500">
                            <span class="confirm-text">Confirm</span>
                            <span class="confirm-loading hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

          

            {{-- Email Panel --}}
            <div x-show="tab === 'email'" x-cloak class="mb-8">
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 space-y-1.5">
                        <h3 class="text-lg font-semibold">Update Email</h3>
                        <p class="text-sm text-muted-foreground">Change your email address below.</p>
                    </div>
                    <div class="p-6 pt-0">
                        <form class="space-y-4">
                            <div>
                                <label for="email" class="block text-sm font-medium">Email Address</label>

                                <input type="email" id="InputEmail" placeholder="Input new email" class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm">
                            </div>
                            <!-- <div>
                            <label for="role2" class="block text-sm font-medium">Role</label>
                            <input id="role2" value="Administrator" disabled
                                class="mt-1 block w-full rounded-md border-input bg-muted px-3 py-2 text-sm shadow-sm">
                        </div> -->
                            <!-- Info Notice -->
                            <div class="flex items-start space-x-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">Email Verification Required</p>
                                    <p>A verification code will be sent to your registered email address to confirm email changes.</p>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" id="saveEmailBtn"
                                    class="text-sm rounded-md bg-blue-600 px-4 py-2 font-medium text-white shadow
           transition-colors duration-200 hover:bg-blue-500 focus:outline-none focus:ring-1 focus:ring-ring">
                                    <span class="btn-text">Change Email</span> <!-- This will show when the button is not loading -->
                                    <span class="btn-loading hidden"> <!-- This will be hidden initially -->
                                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <!-- Email Change OTP Modal -->
            <div id="emailOtpModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Verify Email Change</h3>
                        <button id="closeEmailOtpModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-4">
                            We've sent a 6-digit verification code to <strong id="otp_email_email">your registered email</strong>. Please enter it below to confirm your email change.
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label for="otpCodeEmail" class="block text-sm font-medium text-gray-700">Verification Code</label>
                                <input type="text" id="otpCodeEmail" maxlength="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 text-center text-lg font-mono tracking-widest focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="000000">
                            </div>

                            <div class="text-center">
                                <button id="resendOtpBtnEmail" class="text-sm text-blue-600 hover:text-blue-800 disabled:text-gray-400 disabled:cursor-not-allowed">
                                    <span class="resend-text">Didn't receive the code? Resend</span>
                                    <span class="resend-timer hidden">Resend in <span id="timerCountEmail">60</span>s</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button id="cancelEmailOtpBtn" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button id="verifyEmailOtpBtn" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500 disabled:bg-blue-300">
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

            {{-- Password Panel --}}
            <div x-show="tab === 'password'" x-cloak>
                <div class="rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 space-y-1.5">
                        <h3 class="text-lg font-semibold">Change Password</h3>
                        <p class="text-sm text-muted-foreground">Update your password below.</p>
                    </div>
                    <div class="p-6 pt-0">
                        <form id="changePasswordForm" class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium">Current Password</label>
                                <div class="relative">
                                    <input id="current_password" type="password" placeholder="Enter Current Password"
                                        class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2"
                                        autocomplete="current-password">
                                    <button type="button" id="toggleCurrentPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-sm">
                                        <svg id="currentPasswordToggle" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-medium">New Password</label>
                                <div class="relative">
                                    <input id="new_password" type="password" placeholder="Enter New Password"
                                        class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2"
                                        autocomplete="new-password">
                                    <button type="button" id="toggleNewPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-sm">
                                        <svg id="newPasswordToggle" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="confirm_password" class="block text-sm font-medium">Confirm Password</label>
                                <div class="relative">
                                    <input id="confirm_password" type="password" placeholder="Confirm Password"
                                        class="mt-1 block w-full rounded-md border-input px-3 py-2 text-sm shadow-sm focus:ring-1 focus:ring-ring focus:outline-none border-2"
                                        autocomplete="new-password">
                                    <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-sm">
                                        <svg id="confirmPasswordToggle" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Info Notice -->
                            <div class="flex items-start space-x-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">Email Verification Required</p>
                                    <p>A verification code will be sent to your registered email address to confirm password changes.</p>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" id="savePassBtn"
                                    class="text-sm rounded-md bg-blue-600 px-4 py-2 font-medium text-white shadow transition-colors duration-200 hover:bg-blue-500 focus:outline-none focus:ring-1 focus:ring-ring">
                                    <span class="btn-text">Change Password</span>
                                    <span class="btn-loading hidden">
                                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Password Change OTP Modal -->

            <div id="passwordOtpModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Verify Password Change</h3>
                        <button id="closePasswordOtpModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-4">
                            We've sent a 6-digit verification code to <strong id="otp_email_email">your registered email</strong>. Please enter it below to confirm your password change.
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label for="otpCodePassword" class="block text-sm font-medium text-gray-700">Verification Code</label>
                                <input type="text" id="otpCodePassword" maxlength="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 px-3 py-2 text-center text-lg font-mono tracking-widest focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="000000">
                            </div>

                            <div class="text-center">
                                <button id="resendOtpBtnPassword" class="text-sm text-blue-600 hover:text-blue-800 disabled:text-gray-400 disabled:cursor-not-allowed">
                                    <span class="resend-text">Didn't receive the code? Resend</span>
                                    <span class="resend-timer hidden">Resend in <span id="timerCountPassword">60</span>s</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button id="cancelPasswordOtpBtn" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button id="verifyPasswordOtpBtn" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500 disabled:bg-blue-300">
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
    </main>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="{{ asset('assets/js/admin_change_ministry.js') }}"></script>
<script src="{{ asset('assets/js/admin_details.js') }}"></script>
<script src="{{ asset('assets/js/admin_change_email.js') }}"></script>
<script src="{{ asset('assets/js/admin_change_password.js') }}"></script>
@endsection