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
     *
     * @return void
     */
    public function __construct(
        public User $sender,
        public MailObject $mail
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ElasticsearchHelperInterface $elasticsearchHelper, RedisHelperInterface $redisHelper)
    {
        // compose an email
        $email = (new MailDispatch($this->mail->body))
            ->subject($this->mail->subject);

        // send an email
        Mail::to($this->mail->email)
            ->send($email);

        // save data to elasticsearch
        $elasticsearchHelper->storeEmail($this->mail->body, $this->mail->subject, $this->mail->email);

        // save data to redis
        $redisHelper->storeRecentMessage($this->mail->subject, $this->mail->email);
    }
}
