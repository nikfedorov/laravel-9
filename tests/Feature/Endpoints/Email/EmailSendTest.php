<?php

namespace Tests\Feature\Endpoints\Email;

use App\Jobs\SendMail;
use App\Models\User;
use App\Objects\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EmailSendTest extends TestCase
{
    protected $mail;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        $this->mail = Mail::factory()->make();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_sends_an_email()
    {
        // arrange
        $mail = $this->mail;
        $user = $this->user;

        // act
        $response = $this->withoutExceptionHandling()
            ->post(route('api.send', $user).'?api_token='.$user->api_token, [
                'emails' => [
                    [
                        'email' => $mail->email,
                        'subject' => $mail->subject,
                        'body' => $mail->body,
                    ],
                ],
            ]);

        // assert
        $response->assertNoContent();

        // assert job created
        Queue::assertPushed(function (SendMail $job) use ($user, $mail) {
            return $job->sender->is($user)
                && $job->mail->email == $mail->email
                && $job->mail->subject == $mail->subject
                && $job->mail->body == $mail->body;
        });
    }

    /** @test */
    public function it_throws_forbidden_without_valid_token()
    {
        // arrange
        $user = $this->user;

        // act
        $response = $this
            ->post(route('api.send', $user));

        // assert
        $response->assertForbidden();
        Queue::assertNothingPushed();
    }

    /** @test */
    public function it_fails_validation_on_invalid_emails()
    {
        // arrange
        $user = $this->user;
        $user = $this->user;

        // act
        $response = $this
            ->post(route('api.send', $user).'?api_token='.$user->api_token);

        // assert
        $response->assertSessionHasErrors([
            'emails',
        ]);
        Queue::assertNothingPushed();
    }
}
