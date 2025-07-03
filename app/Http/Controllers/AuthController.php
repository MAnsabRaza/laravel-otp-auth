<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register()
    {
        return view("auth/register");
    }
    public function login()
    {
        return view("auth/login");
    }
    public function forgetPassword()
    {
        return view("auth/forgetPassword");
    }
    public function verifyOtp()
    {
        return view("auth/verifyOtp");
    }
    public function resetPassword()
    {
        return view("auth/resetPassword");
    }
    public function saveRegister(Request $request)
    {
        $data = $request->all();

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->address = $data['address'];
        $user->phone_number = $data['phone_number'];
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Registration successful!',
            'redirect' => route('login')
        ]);
    }
    public function checkLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => true,
                'message' => 'Login successful!',
                'redirect' => route('home')
            ]);
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
    public function sendOtp(Request $request)
    {
        $request->validate(([
            'email' => 'required|email|exists:users',
        ]));
        $user = User::where('email', $request->email)->first();
        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);
        session(['reset_email' => $request->email]);
        try {
            // Mail::raw("Your OTP for password reset is: $otp. This code will expire in 5 minutes.", function ($message) use ($user) {
            //     $message->to($user->email)
            //         ->subject("Password Reset OTP - " . config('app.name'));
            // });
            Mail::to($user->email)->send(new WelcomeMail($user, $otp));
        } catch (\Exception $e) {

            return response()->json([
                'status' => true,
                'message' => 'OTP generated successfully! (Email service temporarily unavailable)',
                'redirect' => route('verifyOtp')
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Login successful!',
            'redirect' => route('verifyOtp')
        ]);
    }
    public function sndVerifyOtp(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|numeric|digits:6'
            ]);
            $email = session('reset_email');
            if (!$email) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email session expired. Please request a new OTP.'
                ], 400);
            }
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.'
                ], 404);
            }
            if (!$user->otp || !$user->otp_expires_at) {
                return response()->json([
                    'status' => false,
                    'message' => 'No OTP found. Please request a new OTP.'
                ], 400);
            }
            if ($user->otp != $request->otp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP. Please check and try again.'
                ], 400);
            }

            if (Carbon::now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP has expired. Please request a new OTP.'
                ], 400);
            }
            session(['verified_email' => $email]);
            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully!',
                'redirect' => route('resetPassword')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('OTP verification failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed. Please try again.'
            ], 500);
        }
    }

    public function saveResetPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required',
        ]);
        $email = session('verified_email');
        if (!$email) {
            return response()->json([
                'status' => false,
                'message' => 'Session expired. Please verify OTP again.'
            ]);
        }

        $user = User::where('email', $email)->first();
        $user->update([
            'password' => Hash::make($request->new_password),
            'otp' => null,
            'otp_expires_at' => null
        ]);
        session()->forget(['reset_email', 'verified_email']);
        return response()->json([
            'status' => true,
            'message' => 'Change Password successful!',
            'redirect' => route('login')
        ]);
    }
}
