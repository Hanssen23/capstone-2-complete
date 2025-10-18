<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class MemberDeletionWarning extends Notification
{
    use Queueable;

    private int $daysUntilDeletion;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $daysUntilDeletion)
    {
        $this->daysUntilDeletion = $daysUntilDeletion;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reactivationUrl = $this->generateReactivationUrl($notifiable);
        
        return (new MailMessage)
            ->subject('Account Inactivity Notice - Silencio Gym')
            ->greeting("Hello {$notifiable->first_name}!")
            ->line('We noticed that your Silencio Gym account has been inactive for an extended period.')
            ->line("**Your account will be automatically deleted in {$this->daysUntilDeletion} days** if no action is taken.")
            ->line('**Why is this happening?**')
            ->line('We automatically remove inactive accounts to maintain our system and protect member privacy.')
            ->line('**What can you do?**')
            ->line('To keep your account active, simply:')
            ->line('• Log in to your member portal')
            ->line('• Visit the gym and use your RFID card')
            ->line('• Contact us if you need assistance')
            ->action('Reactivate My Account', $reactivationUrl)
            ->line('**Account Details:**')
            ->line("Member Number: {$notifiable->member_number}")
            ->line("Email: {$notifiable->email}")
            ->line("Current Status: " . ucfirst($notifiable->status))
            ->line('If you no longer wish to maintain your membership, no action is required and your account will be automatically removed.')
            ->line('**Need Help?**')
            ->line('Contact us at the gym or reply to this email if you have any questions.')
            ->salutation('Best regards, Silencio Gym Team');
    }

    /**
     * Generate reactivation URL
     */
    private function generateReactivationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'member.reactivate.show',
            now()->addDays($this->daysUntilDeletion + 7), // Valid for deletion period + 1 week
            ['member' => $notifiable->id]
        );
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'deletion_warning',
            'days_until_deletion' => $this->daysUntilDeletion,
            'member_id' => $notifiable->id,
            'sent_at' => now(),
        ];
    }
}
