<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OfferLetter extends Mailable
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
        $emp = Employee::where('emp_code', $this->data)->first();
        $data = $emp;
        $salaryData = Salary::where('emp_code', $emp->emp_code)
        ->with('salaryType')
        ->join('salary_types', 'salary.salary_type', '=', 'salary_types.id')
        ->orderBy('salary_types.id', 'asc')
        ->select('salary.*')
        ->get();

        $salarySummary = [];
        foreach ($salaryData as $salary) {
            $perMonth = $salary->salary;
            $perAnnum = $salary->salary * 12;
            $salarySummary[] = [
                'name' => $salary->salaryType->name,
                'category' => $salary->salaryType->category,
                'per_month' => $perMonth,
                'per_annum' => $perAnnum,
            ];
        }
        $hr_name = Employee::where('role_id', 2)->latest()->first();

        $pdf = PDF::loadView('mail.offer.offerletterpdf', compact('data', 'salarySummary', 'hr_name'))
        ->setPaper('a4', 'portrait')
        ->setOption('margin-top', '0mm')
        ->setOption('margin-bottom', '0mm')
        ->setOption('margin-left', '0mm')
        ->setOption('margin-right', '0mm');

        $fileContent = $pdf->output();
        $filePath = 'uploads/documents/' . $emp->emp_code . '/';
        $fileName = time() . '_' . 'OfferLetter.pdf';
        $fullPath = $filePath . $fileName;

        Storage::disk('public')->put($fullPath, $fileContent);

        $document = new Document();
        $document->emp_code = $emp->emp_code;
        $document->doc_type = null;
        $document->doc_name = "OfferLetter";
        $document->path = 'storage/' . $fullPath;
        $document->save();

        $hr_name = Employee::where('role_id', 2)->latest()->first();

        return $this->view('mail.offer.offerletter', compact('data', 'salarySummary', 'hr_name'))
            ->attachData($pdf->output(), 'OfferLetter.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
