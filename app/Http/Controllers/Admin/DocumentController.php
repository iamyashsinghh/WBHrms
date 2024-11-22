<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{
    public function list()
    {
        $page_heading = "Document Types";
        return view('admin.document.type.list', compact('page_heading'));
    }

    /**
     * Handle DataTable AJAX requests.
     */
    public function ajax_list(Request $request)
    {
        if ($request->ajax()) {
            $data = DocumentType::query();
            return DataTables::of($data)
                ->addColumn('actions', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary" onclick="handleManageDocumentType(' . $row->id . ')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="handleDeleteDocumentType(' . $row->id . ')">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['actions', 'icon'])
                ->make(true);
        }
    }

    /**
     * Handle the creation or update of document types.
     */
    public function manage_process(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_required' => 'required|in:0,1',
        ]);

        $data = $request->only(['name', 'icon', 'is_required']);

        if ($id) {
            $documentType = DocumentType::findOrFail($id);
            $documentType->update($data);
            return response()->json(['success' => 'Document type updated successfully.']);
        } else {
            DocumentType::create($data);
            return response()->json(['success' => 'Document type created successfully.']);
        }
    }

    /**
     * Delete a document type.
     */
    public function destroy($id)
    {
        $documentType = DocumentType::findOrFail($id);
        $documentType->delete();
        return response()->json(['success' => 'Document type deleted successfully.']);
    }


    public function uploadDocument(Request $request)
    {
        try {
            $request->validate([
                'document_id' => 'nullable',
                'doc_type' => 'required|string',
                'document' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation failed:", ['errors' => $e->errors()]);

            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        if ($request->document_id) {
            $document = Document::find($request->document_id);
        } else {
            $document = new Document();
            $document->emp_code = $request->emp_code;
            $document->doc_type = $request->doc_type;
        }

        $file = $request->file('document');
        $filePath = 'uploads/documents/' . $document->emp_code . '/';
        $fileName = time() . '_' . $file->getClientOriginalName();

        try {
            if (!File::isDirectory(public_path($filePath))) {
                File::makeDirectory(public_path($filePath), 0777, true, true);
            }

            $file->move(public_path($filePath), $fileName);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'File upload failed.');
        }

        $document->path = $filePath . $fileName;
        $document->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Document Uploaded!']);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }
}
