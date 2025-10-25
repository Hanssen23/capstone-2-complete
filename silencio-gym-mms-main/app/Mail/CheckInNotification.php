<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckInNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $checkInTime;
    public $currentPlan;
    public $membershipExpiry;

    /**
     * Create a new message instance.
     */
    public function __construct(Member $member, $checkInTime, $currentPlan, $membershipExpiry)
    {
        $this->member = $member;
        $this->checkInTime = $checkInTime;
        $this->currentPlan = $currentPlan;
        $this->membershipExpiry = $membershipExpiry;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Check-in Confirmation - RBA GYM',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.check-in',
            with: [
                'memberName' => $this->member->name,
                'checkInTime' => $this->checkInTime,
                'currentPlan' => $this->currentPlan,
                'membershipExpiry' => $this->membershipExpiry,
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

