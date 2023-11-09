<?php

namespace Tests\Feature\Endpoints\Email;

use App\Objects\Mail;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Tests\TestCase;

class EmailListTest extends TestCase
{
    protected $mail;

    protected $elasticsearchHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mail = Mail::factory()->make();

        // resolve elastcisearch helper and delete all records
        $this->elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
        $this->elasticsearchHelper->deleteAll();
    }

    /**
     * @test
     *
     * @group slow
     */
    public function it_shows_list_of_emails()
    {
        // arrange
        $mail = $this->mail;

        // store mail to elasticsearch
        $this->elasticsearchHelper->storeEmail(
            toEmailAddress: $mail->email,
            messageSubject: $mail->subject,
            messageBody: $mail->body,
        );

        // wait for elasticsearch to refresh its index
        sleep(1);

        // act
        $response = $this->withoutExceptionHandling()
            ->get(route('api.list'));

        // assert
        $response->assertJsonPath('emails.0.email', $mail->email);
        $response->assertJsonPath('emails.0.subject', $mail->subject);
        $response->assertJsonPath('emails.0.body', $mail->body);
    }

    /** @test */
    public function it_shows_empty_list_of_emails()
    {
        // act
        $response = $this->withoutExceptionHandling()
            ->get(route('api.list'));

        // assert
        $response->assertJsonCount(0, 'emails');
    }
}
