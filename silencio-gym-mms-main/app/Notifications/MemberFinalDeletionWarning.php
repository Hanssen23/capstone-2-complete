<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class MemberFinalDeletionWarning extends Notification
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
        $urgencyText = $this->daysUntilDeletion <= 1 ? 'TOMORROW' : "in {$this->daysUntilDeletion} days";
        
        return (new MailMessage)
            ->subject('🚨 FINAL NOTICE: Account Deletion ' . strtoupper($urgencyText) . ' - Silencio Gym')
            ->greeting("URGENT: {$notifiable->first_name}!")
            ->line('🚨 **This is your FINAL WARNING** 🚨')
            ->line("**Your Silencio Gym account will be permanently deleted {$urgencyText}.**")
            ->line('**This is your last chance to save your account!**')
            ->line('Once deleted, you will lose:')
            ->line('• Your membership history')
            ->line('• Your member number and profile')
            ->line('• Access to member benefits')
            ->line('• All account data (this cannot be recovered)')
            ->action('🔄 SAVE MY ACCOUNT NOW', $reactivationUrl)
            ->line('**To prevent deletion, you must take action immediately:**')
            ->line('1. Click the button above to reactivate your account, OR')
            ->line('2. Log in to your member portal, OR')
            ->line('3. Visit the gym and use your RFID card, OR')
            ->line('4. Contact us directly')
            ->line('**Account Information:**')
            ->line("Member Number: {$notifiable->member_number}")
            ->line("Email: {$notifiable->email}")
            ->line("Status: " . ucfirst($notifiable->status))
            ->line("Deletion Date: " . now()->addDays($this->daysUntilDeletion)->format('F j, Y'))
            ->line('**Important:** If you do not take action, your account will be automatically and permanently deleted. This action cannot be undone.')
            ->line('**Questions?** Contact us immediately:')
            ->line('• Visit Silencio Gym in person')
            ->line('• Reply to this email')
            ->line('• Call us during business hours')
            ->salutation('Urgent regards, Silencio Gym Team');
    }

    /**
     * Generate reactivation URL
     */
    private function generateReactivationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'member.reactivate.show',
            now()->addDays($this->daysUntilDeletion + 3), // Valid for deletion period + 3 days
            ['member' => $notifiable->id]
        );
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'final_deletion_warning',
            'days_until_deletion' => $this->daysUntilDeletion,
            'member_id' => $notifiable->id,
            'sent_at' => now(),
        ];
    }
}
