<?php

namespace App\Http\Middleware;

use App\Models\LoginInfo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $login_info = LoginInfo::where(['token' => session()->token()])->first();
        if ($login_info) {
            return $next($request);
        }
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }
}
