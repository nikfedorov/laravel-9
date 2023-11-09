<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendMail;
use App\Mail\MailDispatch;
use App\Models\User;
use App\Objects\Mail as MailObject;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendMailJobTest extends TestCase
{
    protected $mail;

    protected $user;

    protected $elasticsearchHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mail = MailObject::factory()->make();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_sends_email()
    {
        Mail::fake();

        // arrange
        $sender = $this->user;
        $mail = $this->mail;

        // act
        SendMail::dispatch($sender, $mail);

        // assert
        Mail::assertQueued(function (MailDispatch $email) use ($mail) {
            return $email->hasTo($mail->email)
                && $email->subject === $mail->subject
                && $email->body === $mail->body;
        });

        $sender->refresh();
        $this->assertTrue($sender->last_email_sent_at->diffInSeconds(now()) < 1);
    }

    /**
     * @test
     *
     * @group slow
     */
    public function it_saves_email_to_elasticsearch()
    {
        // resolve elastcisearch helper and delete all records
        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
        $elasticsearchHelper->deleteAll();

        // arrange
        $sender = $this->user;
        $mail = $this->mail;

        // act
        SendMail::dispatch($sender, $mail);

        // wait for elasticsearch to refresh its index
        sleep(1);

        // assert
        $emails = $elasticsearchHelper->getEmails();
        $this->assertTrue(count($emails) == 1);
        $this->assertTrue($emails['0']['email'] == $mail->email);
        $this->assertTrue($emails['0']['subject'] == $mail->subject);
        $this->assertTrue($emails['0']['body'] == $mail->body);
    }

    /** @test */
    public function it_saves_email_to_redis()
    {
        // resolve redis helper and delete all records
        $redisHelper = app()->make(RedisHelperInterface::class);
        $redisHelper->deleteAll();

        // arrange
        $sender = $this->user;
        $mail = $this->mail;

        // act
        SendMail::dispatch($sender, $mail);

        // assert
        $emails = $redisHelper->getList();
        $this->assertTrue(count($emails) == 1);

        $email = $redisHelper->get($emails['0']);
        $this->assertTrue($email->email == $mail->email);
        $this->assertTrue($email->subject == $mail->subject);
    }
}
