<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
}