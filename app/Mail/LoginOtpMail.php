<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
        public int $minutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Golden Pangolin login code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login-otp',
            with: [
                'name' => $this->user->name,
                'code' => $this->code,
                'minutes' => $this->minutes,
            ],
        );
    }
}
