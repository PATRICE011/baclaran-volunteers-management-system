<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOtp;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function showFindEmailForm()
    {
        return view('Auth.find_email');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('status', 'If your email exists, you will receive an OTP shortly.');
        }

        // Delete existing unused OTPs
        UserOtp::where('user_id', $user->id)
            ->where('purpose', 'password_reset')
            ->where('is_used', false)
            ->delete();

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP
        UserOtp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'purpose' => 'password_reset',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP
        $this->mailService->sendOTP(
            $user->email,
            $user->first_name . ' ' . $user->last_name,
            $otp,
            'password_reset'
        );

        // Store email in session
        Session::put('password_reset_email', $user->email);

        return redirect()->route('password.otp') ->with('success', 'OTP sent to your email address');
    }

    public function showOtpForm()
    {
        if (!Session::has('password_reset_email')) {
            return redirect()->route('password.request');
        }

        return view('Auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $email = Session::get('password_reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'User not found']);
        }

        $otpRecord = UserOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('purpose', 'password_reset')
            ->where('is_used', false)
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        // Mark OTP as used
        $otpRecord->update(['is_used' => true]);

        // Create password reset token
        $token = \Illuminate\Support\Str::random(60);
        Session::put('password_reset_token', $token);
        Session::put('reset_user_id', $user->id);

        return redirect()->route('password.reset')->with('success', 'OTP verified successfully!');
    }

    public function showNewPasswordForm()
    {
        if (!Session::has('password_reset_token') || !Session::has('reset_user_id')) {
            return redirect()->route('password.request');
        }

        return view('Auth.new_password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ]);

        if (!Session::has('reset_user_id')) {
            return redirect()->route('password.request');
        }

        $user = User::find(Session::get('reset_user_id'));
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear session
        Session::forget([
            'password_reset_email',
            'password_reset_token',
            'reset_user_id'
        ]);

        return redirect()->route('login')
            ->with('success', 'Password changed successfully!');
    }

    public function resendOtp(Request $request)
    {
        $email = Session::get('password_reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please try again.'
            ]);
        }

        // Delete existing unused OTP
        UserOtp::where('user_id', $user->id)
            ->where('purpose', 'password_reset')
            ->where('is_used', false)
            ->delete();

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store new OTP
        UserOtp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'purpose' => 'password_reset',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send new OTP
        $this->mailService->sendOTP(
            $user->email,
            $user->first_name . ' ' . $user->last_name,
            $otp,
            'password_reset'
        );

        return response()->json([
            'success' => true,
            'message' => 'New OTP has been sent.'
        ]);
    }
}
