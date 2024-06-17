<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                Log::info('User logged in successfully.', ['user_id' => Auth::id()]);
                return redirect()->intended(route('mains.index'))->with('status', 'Login successful!');
            } else {
                Log::warning('Login attempt failed. Invalid credentials.', ['email' => $request->input('email')]);
            }
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            Log::error('Exception Trace: ' . $e->getTraceAsString());
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Incorrect email or password',
        ])->onlyInput('email');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended('mains')->with('status', 'You have successfully logged in.');
    }
}
