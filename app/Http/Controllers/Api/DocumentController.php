<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function getDocs(Request $request)
    {
        $u =  $request->user();
        $userDoc = Document::where('emp_code', $u->emp_code)->get();
        return response()->json([
            'success' => true,
            'data' => $userDoc
        ], 200);
    }

    public function createDocs(Request $request)
    {
        $u = $request->user();

        $request->validate([
            'doc_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpeg,jpg,png|max:15360',
        ]);

        Log::info($request);
        $file = $request->file('file');
        $filePath = 'uploads/documents/' . $u->emp_code . '/';
        $fileName = time() . '_' . $file->getClientOriginalName();
        $fullPath = $filePath . $fileName;

        $file->storeAs($filePath, $fileName, 'public');

        $document = new Document();
        $document->emp_code = $u->emp_code;
        $document->doc_type = null;
        $document->doc_name = $request->doc_name;
        $document->path = 'storage/' . $fullPath;
        $document->save();

        return response()->json([
            'message' => 'Document uploaded successfully',
            'data' => $document
        ], 201);
    }
}
