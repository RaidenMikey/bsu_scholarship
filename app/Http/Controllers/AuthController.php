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
            // Redirect central admin to their login page if they try to access regular login
            if (session('role') === 'central') {
                return redirect('/central/login');
            }
            return redirect(match (session('role')) {
                'student' => route('student.dashboard'),
                'sfao'    => '/sfao',
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

        // Prevent central admin from using regular login
        if ($user->role === 'central') {
            return back()->withErrors(['Central admin users must use the central admin login page.']);
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
            default   => '/'
        });
    }

    /**
     * Show central admin login form
     * Only accessible via direct URL - no navigation links should point here
     */
    public function showCentralLogin(Request $request)
    {
        // Prevent access if coming from internal navigation (referrer check)
        // This ensures the page can only be accessed by typing the URL directly
        $referrer = $request->headers->get('referer');
        if ($referrer) {
            $referrerUrl = parse_url($referrer);
            $referrerHost = $referrerUrl['host'] ?? null;
            $referrerPath = $referrerUrl['path'] ?? null;
            $currentHost = $request->getHost();
            
            // Allow if referrer is from different site (external)
            // Allow if coming from /login (programmatic redirect for central admin)
            // Block if referrer is from same site and not /login (internal navigation via links/buttons)
            if ($referrerHost === $currentHost && $referrerPath !== '/login') {
                return redirect('/')->withErrors(['Access denied.']);
            }
        }

        if (session()->has('user_id')) {
            if (session('role') === 'central') {
                return redirect()->route('central.dashboard');
            }
            return redirect(match (session('role')) {
                'student' => route('student.dashboard'),
                'sfao'    => '/sfao',
                default   => '/'
            });
        }
        return view('auth.centrallogin');
    }

    /**
     * Handle central admin login request
     */
    public function centralLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['Invalid credentials']);
        }

        // Only allow central admin role
        if ($user->role !== 'central') {
            return back()->withErrors(['This login page is only for central admin users.']);
        }

        if (!$user->hasVerifiedEmail()) {
            return back()->withErrors(['Your email is not verified. Please check your inbox.']);
        }

        session([
            'user_id' => $user->id,
            'role' => $user->role,
        ]);

        return redirect()->route('central.dashboard');
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

