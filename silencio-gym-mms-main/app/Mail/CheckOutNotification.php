<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckOutNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $checkOutTime;
    public $duration;

    /**
     * Create a new message instance.
     */
    public function __construct(Member $member, $checkOutTime, $duration)
    {
        $this->member = $member;
        $this->checkOutTime = $checkOutTime;
        $this->duration = $duration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Check-out Confirmation - Silencio Gym',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.check-out',
            with: [
                'memberName' => $this->member->name,
                'checkOutTime' => $this->checkOutTime,
                'duration' => $this->duration,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

