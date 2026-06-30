<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials,$request->remember)) {

            return back()->withErrors([
                'email'=>'Invalid credentials.'
            ])->onlyInput('email');

        }

        $request->session()->regenerate();

        if (!auth()->user()->hasAnyRole([
            'super-admin',
            'admin',
            'moderator',
            'support'
        ])) {

            Auth::logout();

            return back()->withErrors([
                'email'=>'Unauthorized.'
            ]);

        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
