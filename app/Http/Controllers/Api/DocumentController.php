<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function getDocs(Request $request){
        $u =  $request->user();
        $userDoc = Employee::where('emp_code', $u->emp_code)->get_documents();
        return response()->json([
            'success' => true,
            'data' => $userDoc
        ], 200);
    }
}
