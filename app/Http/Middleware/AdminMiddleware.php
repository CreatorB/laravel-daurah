<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Session::has('user_id')) {
            return redirect()->route('login')->with('error', __('questions.admin.login_required'));
        }

        if (Session::get('role') !== 'admin') {
            abort(403, __('questions.admin.forbidden'));
        }

        return $next($request);
    }
}
