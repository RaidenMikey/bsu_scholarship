<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Invitation;

/**
 * Authentication Controller
 * 
 * Handles user authentication operations:
 * - Login
 * - Registration
 * - Email verification
 * - Logout
 */
class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (session()->has('user_id')) {
            return redirect(match (session('role')) {
                'student' => route('student.dashboard'),
                'sfao'    => '/sfao',
                'central' => '/central',
                default   => '/'
            });
        }
        return view('auth.auth');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['Invalid credentials']);
        }

        if (!$user->hasVerifiedEmail()) {
            return back()->withErrors(['Your email is not verified. Please check your inbox.']);
        }

        if ($request->campus_id != $user->campus_id) {
            return back()->withErrors(['The selected campus does not match your account.']);
        }

        session([
            'user_id' => $user->id,
            'role' => $user->role,
        ]);

        return redirect(match ($user->role) {
            'student' => '/student',
            'sfao'    => '/sfao',
            'central' => '/central',
            default   => '/'
        });
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        session()->flush();
        return redirect('/login')->with('logged_out', true);
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required','email','unique:users,email','regex:/^[a-zA-Z0-9._%+-]+@g\.batstate-u\.edu\.ph$/'],
            'password'  => 'required|string|confirmed|min:6',
            'role'      => 'required|string',
            'campus_id' => 'required|exists:campuses,id',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'campus_id' => $request->campus_id,
        ]);

        $user->sendEmailVerificationNotification();

        return redirect('/login')->with('status', 'Account created! Please verify your email before logging in.');
    }

    /**
     * Show email verification notice
     */
    public function showVerificationNotice()
    {
        return view('auth.verify-email');
    }

    /**
     * Verify user email
     */
    public function verifyEmail($id, $hash, Request $request)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('status', 'Email already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        // Check if this is an SFAO user with an invitation
        if ($user->role === 'sfao' && $user->invitation) {
            return redirect()->route('sfao.password.setup')->with('status', 'Email verified! Please set your password.');
        }

        return redirect('/login')->with('status', 'Email verified successfully! You can now login.');
    }

    /**
     * Resend email verification notification
     */
    public function resendVerification(Request $request)
    {
        if (session()->has('user_id')) {
            $user = User::find(session('user_id'));
            if ($user && !$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
                return back()->with('status', 'Verification link sent!');
            }
        }
        return back()->withErrors(['User not found or already verified.']);
    }
}

