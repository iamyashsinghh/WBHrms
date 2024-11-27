<?php

namespace App\Mail;

use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Queue\SerializesModels;

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
        $pdf = PDF::loadView('mail.increment.incrementletterpdf', compact('data'))
        ->setPaper('a4', 'portrait')
        ->setOption('margin-top', '0mm')
        ->setOption('margin-bottom', '0mm')
        ->setOption('margin-left', '0mm')
        ->setOption('margin-right', '0mm');

        return $this->view('mail.increment.incrementletter', compact('data'))
            ->attachData($pdf->output(), 'IncrementLetter.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
