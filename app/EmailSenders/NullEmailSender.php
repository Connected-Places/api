<?php

namespace App\EmailSenders;

use App\Contracts\EmailSender;
use App\Emails\Email;

class NullEmailSender implements EmailSender
{
    public function send(Email $email)
    {
        $email->notification->update(['message' => 'Sent by null sender - no message content provided']);
    }
}
