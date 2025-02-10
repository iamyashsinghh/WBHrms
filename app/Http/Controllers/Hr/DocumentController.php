<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{
    public function list()
    {
        $page_heading = "Document Types";
        return view('hr.document.type.list', compact('page_heading'));
    }

    public function get($id = null){
        $documentType = DocumentType::findOrFail($id);
        return $documentType;
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

    /**
     * Delete a document.
     */
    public function destroy_doc($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Document Deleted!']);
        return redirect()->back()->with('success', 'Document Deleted successfully.');
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
            $doc_name = DocumentType::where('id', $request->doc_type)->first();
            if ($doc_name) {
                $document = new Document();
                $document->emp_code = $request->emp_code;
                $document->doc_type = $request->doc_type;
                $document->doc_name = $doc_name->name;
            } else {
                session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Document Type Not Found!']);
                return redirect()->back()->with('error', 'File upload failed.');
            }
        }

        $file = $request->file('document');
        $filePath = 'uploads/documents/' . $document->emp_code . '/';
        $fileName = time() . '_' . $file->getClientOriginalName();

        try {
            $path = Storage::disk('public')->putFileAs($filePath, $file, $fileName);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'File upload failed.');
        }

        $document->path = 'storage/' . $filePath . $fileName;
        $document->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Document Uploaded!']);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }
}
