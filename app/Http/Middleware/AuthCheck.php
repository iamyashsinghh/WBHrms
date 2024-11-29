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
        } else if (Auth::guard('backend')->check()) {
            return redirect()->route('backend.dashboard');
        } else if (Auth::guard('backgroud')->check()) {
            return redirect()->route('backgroud.dashboard');
        } else if (Auth::guard('field')->check()) {
            return redirect()->route('field.dashboard');
        }
        return $next($request);
    }
}
