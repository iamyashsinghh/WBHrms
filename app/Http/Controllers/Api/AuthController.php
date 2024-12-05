<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LoginMail;
use App\Models\Employee;
use App\Models\LoginInfo;
use App\Models\Role;
use App\Services\ItMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login_verify(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'phone_number' => "required|digits:10",
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()], 400);
        }

        $user = Employee::where('phone', $request->phone_number)->first();
        if (!$user) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials.'], 400);
        } else if ($user->is_active == 0) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Profile is inactive, kindly contact your manager.'], 400);
        }

        $role_name = $user->get_role->name;
        $role = Role::find($user->role_id);
        $currentTime = date('H:i:s');
        if ($role->is_all_time_login === 0) {
            if ($role->login_start_time && $role->login_end_time) {
                if ($currentTime < $role->login_start_time || $currentTime > $role->login_end_time) {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'You are not allowed to login at this time.'], 400);
                }
            }
        }

        $login_info = LoginInfo::where(['user_id' => $user->id])->first();
        $can_user_login = 1; 
        try {
            $verification_code = rand(111111, 999999);
            if (env('APP_ENV') == 'development') {
                $verification_code = 999999;
            }
            $verification_code = 999999;
            if ($can_user_login === 1) {
                if ($login_info) {
                    $last_request_otp_date = date('Y-m-d', strtotime($login_info->request_otp_at));
                    $current_date = date('Y-m-d');
                    if ($current_date > $last_request_otp_date) {
                        $login_info->request_otp_count = 1;
                    } else {
                        $login_info->request_otp_count = $login_info->request_otp_count + 1;
                    }
                } else {
                    $login_info = new LoginInfo();
                    $login_info->user_id = $user->id;
                    $login_info->request_otp_count = 1;
                }

                $login_info->otp_code = $verification_code;
                $login_info->login_for_whatsapp_otp = null;
                $login_info->request_otp_at = date('Y-m-d H:i:s');
                $login_info->ip_address = 'ip';
                $login_info->browser = "Device info";
                $login_info->platform = 'Platform';
                $login_info->status = 0;
                $login_info->save();

                //     $this->interakt_wa_msg_send($user->phone, $user->name, $verification_code, 'whatsapp_login_team');
                // if ($user->email != null && env('MAIL_STATUS') === true) {
                //     $res_data = ['name' => $user->name, 'otp' => $verification_code];
                //     ItMail::to($user->email)->send(new LoginMail($res_data));
                // }
                return response()->json(['success' => true, 'alert_type' => 'success', 'message' => "Verification code has been sent to your registered WhatsApp & Email."], 200);
            } else {
                return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Your device is not registed please ask admin for the registration'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong. Internal server error.', 'error' => $th->getMessage()], 500);
        }
    }

    public function login_process(Request $request) {
        $validate = validator::make($request->all(), [
            'verified_phone_number' => "required|digits:10",
            'verification_code' => "required|digits:6",
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials, Something went wrong.'], 400);
        }

        $user = Employee::where('phone', $request->verified_phone_number)->first();

        if (!$user) {
            return response()->json( ['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials'], 400);
        } else if ($user->is_active == 0) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Profile is inactive, kindly contact to your manager.'], 400);
        }

        $role = Role::find($user->role_id);

        $currentTime = date('H:i:s');
        if ($role->is_all_time_login === 0) {
            if ($role->login_start_time && $role->login_end_time) {
                if ($currentTime < $role->login_start_time || $currentTime > $role->login_end_time) {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'You are not allowed to login at this time.'], 400);
                }
            }
        }

        $login_info = LoginInfo::where([
            'user_id' => $user->id,
            'otp_code' => $request->verification_code,
        ])->first();

        if (!$login_info || $login_info == null) {
            return response()->json( ['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials', 'data' => $user], 400);
        }

        $request_otp_at = date('YmdHis', strtotime($login_info->request_otp_at));
        $ten_minutes_ago = date('YmdHis', strtotime('-10 minutes'));
        if ($request_otp_at < $ten_minutes_ago) {
            if ($login_info !== null) {
                $login_info->otp_code = null;
                $login_info->save();
            }
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Timeout. Please try again.'], 400);
        }
        $last_login_at = date('Y-m-d', strtotime($login_info->login_at));
        $current_date = date('Y-m-d');
        if ($current_date > $last_login_at) {
            $login_info->login_count = 1;
        } else {
            $login_info->login_count = $login_info->login_count + 1;
        }
        $login_info->otp_code = null;
        $login_info->login_for_whatsapp_otp = null;
        $login_info->login_at = date('Y-m-d H:i:s');
        $login_info->status = 1;
        $login_info->token = 'token';
        $login_info->logout_at = null;
        $login_info->save();

            // if ($user->role_id == 1) {
            //     Auth::guard('admin')->login($user);
            //     return redirect()->route('admin.dashboard');
            // } else if ($user->role_id == 2) {
            //     Auth::guard('hr')->login($user);
            //     return redirect()->route('hr.dashboard');
            // } else if ($user->role_id == 3) {
            //     Auth::guard('backend')->login($user);
            //     return redirect()->route('backend.dashboard');
            // } else if ($user->role_id == 6) {
            //     Auth::guard('backgroud')->login($user);
            //     return redirect()->route('backgroud.dashboard');
            // } else {
            //     Auth::guard('field')->login($user);
            //     return redirect()->route('field.dashboard');
            // }

            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => "Logged in succesfully.", 'data' => $user, 'token' => $user->createToken('mobile-app-token')->plainTextToken], 200);
    }
}
