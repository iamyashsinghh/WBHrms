<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function getDocs(Request $request){
        $u =  $request->user();
        $userDoc = Document::where('emp_code', $u->emp_code)->get();
        return response()->json([
            'success' => true,
            'data' => $userDoc
        ], 200);
    }
}
