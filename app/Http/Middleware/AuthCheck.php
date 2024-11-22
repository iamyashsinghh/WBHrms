<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        } else if (Auth::guard('hr')->check()) {
            return redirect()->route('hr.dashboard');
        } else if (Auth::guard('backend_team')->check()) {
            return redirect()->route('backend_team.dashboard');
        } else if (Auth::guard('backgroud_team')->check()) {
            return redirect()->route('backgroud_team.dashboard');
        } else if (Auth::guard('feild_team')->check()) {
            return redirect()->route('feild_team.dashboard');
        }
        return $next($request);
    }
}
