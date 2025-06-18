<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Show the login page
    public function getLogin()
    {
        return view('Auth.sign_in');
    }

    // Handle the login request
    public function login(Request $request)
    {
        try {
            // Validate the login credentials
            $credentials = $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);

            Log::info('Login attempt', ['email' => $request->email]);

            // Attempt login with the credentials
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                $user = Auth::user();

                Log::info('User logged in successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role
                ]);

                // Redirect based on user role (admin or non-admin)
                return redirect()->intended('/dashboard');
            }

            // Log failed login attempt
            Log::warning('Failed login attempt', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            // Throw validation exception if login fails
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        } catch (\Exception $e) {
            // Log any exceptions
            Log::error('Exception during login process', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw the exception after logging
            throw $e;
        }
    }

   
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ============ FORGOT PASSWORD ========

     public function getFindEmail(){
        
        return view('Auth.find_email');
    }

    public function getReqOt(){
        return view ('Auth.otp');
    }
    
    public function getNewPass(){
        return view ('Auth.new_password');
    }
    
}
