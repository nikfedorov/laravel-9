<?php

namespace App\Jobs;

use App\Mail\MailDispatch;
use App\Models\User;
use App\Objects\Mail as MailObject;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $sender,
        public MailObject $mail
    ) {
        $this->onQueue('send-mail');
    }

    /**
     * Execute the job.
     */
    public function handle(ElasticsearchHelperInterface $elasticsearchHelper, RedisHelperInterface $redisHelper): void
    {
        // compose an email
        $email = (new MailDispatch($this->mail->body))
            ->subject($this->mail->subject)
            ->onQueue('emails');

        // send an email
        Mail::to($this->mail->email)
            ->queue($email);

        // save data to elasticsearch
        $elasticsearchHelper->storeEmail(
            toEmailAddress: $this->mail->email,
            messageSubject: $this->mail->subject,
            messageBody: $this->mail->body,
        );

        // save data to redis
        $redisHelper->storeRecentMessage(
            toEmailAddress: $this->mail->email,
            messageSubject: $this->mail->subject,
        );

        // memorize last email sent time
        $this->sender->last_email_sent_at = now();
        $this->sender->save();
    }
}
