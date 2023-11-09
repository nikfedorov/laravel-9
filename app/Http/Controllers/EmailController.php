<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailSendRequest;
use App\Jobs\SendMail;
use App\Objects\Mail;

class EmailController extends Controller
{
    /**
     * Send a list of emails.
     */
    public function send(EmailSendRequest $request)
    {
        // obtain user from request
        $sender = $request->user();

        // send emails on user behalf
        foreach ($request->input('emails') as $email) {
            $mail = new Mail($email);
            SendMail::dispatch($sender, $mail);
        }
    }

    //  TODO - BONUS: implement list method
    public function list()
    {

    }
}
