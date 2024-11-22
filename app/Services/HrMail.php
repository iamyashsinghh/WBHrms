<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class HrMail
{
    public static function to($recipients)
    {
        return Mail::mailer('secondary')->to($recipients);
    }
}
