<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserOtp;
use App\Services\MailService;
use Carbon\Carbon;

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
        $user = Auth::user();
        return view('account.settings', compact('user'));
    }

    /**
     * Get current user data
     */
    public function getUserData()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => ucfirst($user->role),
            ]
        ]);
    }

    /**
     * Request OTP for name change
     */
    public function requestNameChangeOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            // Check if the name is actually changing
            if ($user->first_name === $request->first_name && $user->last_name === $request->last_name) {
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
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ],
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Send OTP via email
            $this->mailService->sendOTP(
                $user->email,
                $user->full_name,
                $otp,
                'name_change'
            );

            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your email address. Please check your inbox.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500);
        }
    }

    /**
     * Verify OTP and update name
     */
    public function verifyNameChangeOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|string|size:6',
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

            return response()->json([
                'success' => true,
                'message' => 'Your name has been updated successfully!',
                'data' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your name. Please try again.'
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'purpose' => 'required|string|in:name_change',
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
            
            // Update the existing record
            $otpRecord->update([
                'otp' => $newOtp,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Send new OTP via email
            $this->mailService->sendOTP(
                $user->email,
                $user->full_name,
                $newOtp,
                $request->purpose
            );

            return response()->json([
                'success' => true,
                'message' => 'A new OTP has been sent to your email address.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again later.'
            ], 500);
        }
    }
}