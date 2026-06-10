<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        if (!Session::has('user_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($role && Session::get('role') !== $role) {
            if (Session::get('role') === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
}
