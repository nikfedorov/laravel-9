<?php

namespace Tests\Feature\Jobs;

use App\Jobs\SendMail;
use App\Mail\MailDispatch;
use App\Models\User;
use App\Objects\Mail as MailObject;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Exception;
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
    }

    /**
     * @test
     * @group slow
     */
    public function it_saves_email_to_elasticsearch()
    {
        // resolve elastcisearch helper and delete all records
        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
        try {
            $elasticsearchHelper->deleteAll();
        } catch (Exception $e) {}

        // arrange
        $sender = $this->user;
        $mail = $this->mail;

        // act
        SendMail::dispatch($sender, $mail);

        // wait for elasticsearch to refresh its index
        sleep(1);

        // assert
        $emails = $elasticsearchHelper->getEmails();
        $this->assertTrue($emails['hits']['total']['value'] == 1);
        $this->assertTrue($emails['hits']['hits'][0]['_source']['email'] == $mail->email);
        $this->assertTrue($emails['hits']['hits'][0]['_source']['subject'] == $mail->subject);
        $this->assertTrue($emails['hits']['hits'][0]['_source']['body'] == $mail->body);
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
