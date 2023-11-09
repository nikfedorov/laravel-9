<?php

namespace Tests\Feature\Objects;

use App\Objects\Mail;
use Tests\TestCase;

class MailObjectTest extends TestCase
{
    protected $mail;

    public function setUp(): void
    {
        parent::setUp();

        $this->mail = Mail::factory()->make();
    }

    /** @test */
    public function it_has_mail_attributes()
    {
        // arrange
        $mail = $this->mail;

        // assert
        $this->assertTrue($mail->email !== null);
        $this->assertTrue($mail->subject !== null);
        $this->assertTrue($mail->body !== null);
    }
}
