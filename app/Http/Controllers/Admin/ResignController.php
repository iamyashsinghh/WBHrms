<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resignation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ResignController extends Controller
{
    public function index()
    {
        $page_heading = 'Resignation';
        return view('admin.resignation.list', compact('page_heading'));
    }

    public function ajax_list(Request $request)
    {
        $resignation = Resignation::with('employee:emp_code,name');
        return DataTables::of($resignation)->make(true);
    }
}
