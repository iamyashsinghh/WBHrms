<?php

namespace App\Mail;

use App\Models\Employee;
use App\Models\Resignation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResignAccept extends Mailable
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

    public function build()
    {
        $emp = Employee::where('emp_code', $this->data)->first();
        $data = $emp;
        $resign = Resignation::where('emp_code', $data->emp_code)->first();
        $last_working_day = Carbon::parse($resign->resign_at)
        ->addDays((int) $resign->notice_period)
        ->format('d/m/Y');
        $hr_name = Employee::where('role_id', 2)->latest()->first();
        return $this->view('mail.resign.accept', compact('data', 'last_working_day', 'hr_name'));
    }

}
