<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckUserExists
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        $userId = session('user_id');
        $user = $userId ? User::find($userId) : null;

        if (!$user) {
            session()->flush();
            return redirect()->route('login')->with('session_expired', true);
        }

        // Role check (if passed as parameter)
        if ($role && $user->role !== $role) {
            return redirect()->route('login')->with('unauthorized', true);
        }

        return $next($request);
    }
}
