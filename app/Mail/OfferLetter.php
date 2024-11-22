<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Queue\SerializesModels;

class OfferLetter extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data; // Assign the data to the property
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $pdf = PDF::loadView('mail.offerletterpdf', ['data' => $this->data])
        ->setPaper('a4', 'portrait')
        ->setOption('margin-top', '0mm')
        ->setOption('margin-bottom', '0mm')
        ->setOption('margin-left', '0mm')
        ->setOption('margin-right', '0mm');

        return $this->view('mail.offerletter', ['data' => $this->data])
            ->attachData($pdf->output(), 'OfferLetter.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
