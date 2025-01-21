<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class IncrementLetter extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $data = $this->data;
        $hr_name = Employee::where('role_id', 2)->latest()->first();
        $pdf = PDF::loadView('mail.increment.incrementletterpdf', compact('data', 'hr_name'))
        ->setPaper('a4', 'portrait')
        ->setOption('margin-top', '0mm')
        ->setOption('margin-bottom', '0mm')
        ->setOption('margin-left', '0mm')
        ->setOption('margin-right', '0mm');

        $fileContent = $pdf->output();
        $filePath = 'uploads/documents/' . $data->emp_code . '/';
        $fileName = time() . '_' . 'incrementletter.pdf';
        $fullPath = $filePath . $fileName;

        Storage::disk('public')->put($fullPath, $fileContent);

        $document = new Document();
        $document->emp_code = $data->emp_code;
        $document->doc_type = null;
        $document->doc_name = "incrementletter";
        $document->path = 'storage/' . $fullPath;
        $document->save();

        return $this->view('mail.increment.incrementletter', compact('data', 'hr_name'))
            ->attachData($pdf->output(), 'IncrementLetter.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
