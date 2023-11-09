<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailSendRequest;
use App\Jobs\SendMail;
use App\Objects\Mail;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Illuminate\Http\Response;

class EmailController extends Controller
{
    /**
     * Send a list of emails.
     */
    public function send(EmailSendRequest $request): Response
    {
        // obtain user from request
        $sender = $request->user();

        // send emails on user behalf
        foreach ($request->input('emails') as $email) {
            $mail = new Mail($email);
            SendMail::dispatch($sender, $mail);
        }

        // return nocontent
        return response()->noContent();
    }

    /**
     * List sent emails.
     */
    public function list(ElasticsearchHelperInterface $elasticsearchHelper)
    {
        return [
            'emails' => $elasticsearchHelper->getEmails(),
        ];
    }
}
