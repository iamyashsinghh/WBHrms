<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class ItMail
{
    public static function to($recipients)
    {
        return Mail::mailer('smtp')->to($recipients);
    }
}
