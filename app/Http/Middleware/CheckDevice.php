<?php

namespace App\Http\Middleware;

use App\Models\Device;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CheckDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guards = Role::pluck('name');
        $user = null;
        $activeGuard = null;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $activeGuard = $guard;
                break;
            }
        }
        if (!$user) {
            return $next($request); 
        }
        $device_id = Cookie::get("device_id_{$activeGuard}-{$user->phone}");
        $device = Device::where('device_id', $device_id)
            ->where('user_id', $user->id)
            ->where('type', $activeGuard)
            ->first();
        if (!$device) {
            Auth::guard($activeGuard)->logout();
            return redirect()->route('login')->with('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Unregistered device. Please contact the admin for device registration.']);
        }
        return $next($request);
    }
}
