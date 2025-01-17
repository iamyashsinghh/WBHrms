<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function index(){
        $user = Employee::where('status', 1)->whereNotNull();
    }
}
