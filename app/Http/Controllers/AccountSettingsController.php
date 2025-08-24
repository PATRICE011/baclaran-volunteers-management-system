<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserOtp;
use App\Models\Ministry;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AccountSettingsController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Show the account settings page
     */
    public function index()
    {
        $user = Auth::user()->load('ministry');
        $ministries = Ministry::with('children')->whereNull('parent_id')->get();
        return view('admin_accountsettings', compact('user', 'ministries'));
    }

    /**
     * Get current user data
     */
    public function getUserData()
    {
        if (!$user = Auth::user()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ]);
        }

        // Generate profile picture URL
        $profilePictureUrl = null;
        if ($user->profile_picture) {
            $profilePictureUrl = Storage::url($user->profile_picture);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'role' => ucfirst($user->role),
                'profile_picture' => $profilePictureUrl,
            ]
        ]);
    }
    public function updateProfilePicture(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            ]);

            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if it exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                // Store the new profile picture
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');

                // Update user's profile picture path
                $user->profile_picture = $path;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully',
                    'profile_picture_url' => Storage::url($path), // Full URL for frontend
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded',
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile picture update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile picture',
            ], 500);
        }
    }

    /**
     * Request OTP for name change
     */
    public function requestNameChangeOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
                'last_name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            ], [
                'first_name.regex' => 'First name may only contain letters, spaces, and hyphens.',
                'last_name.regex' => 'Last name may only contain letters, spaces, and hyphens.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Trim and capitalize the input values
            $firstName = ucwords(trim($request->first_name));
            $lastName = ucwords(trim($request->last_name));

            // Check if the name is actually changing
            if ($user->first_name === $firstName && $user->last_name === $lastName) {
                return response()->json([
                    'success' => false,
                    'message' => 'No changes detected in the name.'
                ]);
            }

            // Delete any existing unused OTPs for this user and purpose
            UserOtp::where('user_id', $user->id)
                ->where('purpose', 'name_change')
                ->where('is_used', false)
                ->delete();

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTP with pending data
            UserOtp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'purpose' => 'name_change',
                'pending_data' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ],
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Send OTP via email to the user's registered email
            $this->mailService->sendOTP(
                $user->email, // Send to user's registered email
                $user->first_name . ' ' . $user->last_name,
                $otp,
                'name_change'
            );

            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your registered email address (' .
                    substr($user->email, 0, 3) . '***@' .
                    substr(strrchr($user->email, "@"), 1) . '). Please check your inbox.'
            ]);
        } catch (\Exception $e) {
            Log::error('Name change OTP request failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);
        }
    }

    /**
     * Verify OTP and update name
     */
    public function verifyOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|string|size:6|regex:/^[0-9]+$/',
            ], [
                'otp.regex' => 'OTP must contain only numbers.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a valid 6-digit OTP.'
                ], 422);
            }

            $user = Auth::user();

            // Find the OTP
            $otpRecord = UserOtp::where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->where('purpose', 'name_change')
                ->where('is_used', false)
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP code.'
                ]);
            }

            if ($otpRecord->isExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ]);
            }

            // Update user name
            $user->update([
                'first_name' => $otpRecord->pending_data['first_name'],
                'last_name' => $otpRecord->pending_data['last_name'],
            ]);

            // Mark OTP as used
            $otpRecord->update(['is_used' => true]);

            // Clear any other unused OTPs for this purpose
            UserOtp::where('user_id', $user->id)
                ->where('purpose', 'name_change')
                ->where('is_used', false)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Your name has been updated successfully!',
                'data' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Name change OTP verification failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your name. Please try again.'
            ], 500);
        }
    }


    public function requestEmailChangeOTP(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:users,email',
            ]);

            $user = Auth::user();
            $newEmail = $request->email;

            // Delete any existing OTP for email change purpose
            UserOtp::where('user_id', $user->id)
                ->where('purpose', 'email_change')
                ->where('is_used', false)
                ->delete();

            // Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTP with pending email data
            UserOtp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'purpose' => 'email_change',
                'pending_data' => [
                    'email' => $newEmail, // Store the new email here
                ],
                'expires_at' => now()->addMinutes(10),
            ]);

            // Send OTP to the user's registered email
            $this->mailService->sendOTP($user->email, $user->name, $otp, 'email_change');

            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your email address.',
            ]);
        } catch (\Exception $e) {
            Log::error('Email OTP request failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.',
            ], 500);
        }
    }

    public function verifyEmailOTP(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|digits:6',
                'email' => 'required|email',
            ]);

            $user = Auth::user();
            $otpRecord = UserOtp::where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->where('purpose', 'email_change')
                ->where('is_used', false)
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP.',
                ]);
            }

            // Mark OTP as used
            $otpRecord->update(['is_used' => true]);

            // Update user's email using the pending data
            $user->update([
                'email' => $otpRecord->pending_data['email'], // Use the stored new email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully. Please log in again.',
            ]);
        } catch (\Exception $e) {
            Log::error('Email OTP verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying the OTP.',
            ], 500);
        }
    }
    public function requestPasswordChangeOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password' => 'required|string|confirmed|min:8|regex:/^(?=.*[A-Z])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            ], [
                'password.regex' => 'Password must contain at least one uppercase letter and one special character.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ]);
            }


            // Delete any existing unused OTPs for this user and purpose
            UserOtp::where('user_id', $user->id)
                ->where('purpose', 'password_change')
                ->where('is_used', false)
                ->delete();

            // Generate 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store OTP with pending data
            UserOtp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'purpose' => 'password_change',
                'pending_data' => [
                    'password' => bcrypt($request->password),
                ],
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Send OTP via email
            $this->mailService->sendOTP(
                $user->email,
                $user->first_name . ' ' . $user->last_name,
                $otp,
                'password_change'
            );

            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your registered email address (' .
                    substr($user->email, 0, 3) . '***@' .
                    substr(strrchr($user->email, "@"), 1) . '). Please check your inbox.'
            ]);
        } catch (\Exception $e) {
            Log::error('Password change OTP request failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);
        }
    }
    public function verifyPasswordChangeOTP(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|digits:6',
                'password' => 'required|string',
            ]);

            $user = Auth::user();
            $otpRecord = UserOtp::where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->where('purpose', 'password_change')
                ->where('is_used', false)
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP.',
                ]);
            }

            // Mark OTP as used
            $otpRecord->update(['is_used' => true]);

            // Update user's password
            $user->update(['password' => bcrypt($request->password)]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully. Please log in again.',
            ]);
        } catch (\Exception $e) {
            Log::error('Password change OTP verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while changing your password.',
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        try {
            // Validate the purpose field to allow 'name_change', 'password_change', and 'email_change'
            $validator = Validator::make($request->all(), [
                'purpose' => 'required|string|in:name_change,password_change,email_change',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request.'
                ], 422);
            }

            $user = Auth::user();

            // Find the latest unused OTP for this purpose
            $otpRecord = UserOtp::where('user_id', $user->id)
                ->where('purpose', $request->purpose)
                ->where('is_used', false)
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active OTP request found. Please initiate a new request.'
                ]);
            }

            // Generate new OTP
            $newOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Update the existing record with the new OTP and expiration time
            $otpRecord->update([
                'otp' => $newOtp,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Send new OTP via email based on the purpose
            if ($request->purpose === 'name_change') {
                // Send OTP for name change
                $this->mailService->sendOTP(
                    $user->email,
                    $user->first_name . ' ' . $user->last_name,
                    $newOtp,
                    'name_change'
                );
            } elseif ($request->purpose === 'password_change') {
                // Send OTP for password change
                $this->mailService->sendOTP(
                    $user->email,
                    $user->first_name . ' ' . $user->last_name,
                    $newOtp,
                    'password_change'
                );
            } elseif ($request->purpose === 'email_change') {
                // Send OTP for email change
                $this->mailService->sendOTP(
                    $user->email,
                    $user->first_name . ' ' . $user->last_name,
                    $newOtp,
                    'email_change'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'A new OTP has been sent to your registered email address.'
            ]);
        } catch (\Exception $e) {
            Log::error('OTP resend failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again later.'
            ], 500);
        }
    }

    public function updateMinistry(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ministry_id' => 'nullable|exists:ministries,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $user->ministry_id = $request->ministry_id;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Ministry updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Ministry update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update ministry. Please try again later.'
            ], 500);
        }
    }
}
