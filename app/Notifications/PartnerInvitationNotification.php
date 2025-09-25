<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\PartnerInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PartnerInvitation $invitation
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $acceptUrl = route('partner-invitation.accept', [
            'token' => $this->invitation->token,
        ]);

        $message = (new MailMessage)
            ->subject('Partner Invitation - Kink Master')
            ->greeting('Hello!')
            ->line("You have been invited to join Kink Master as a partner by {$this->invitation->inviter->name}.")
            ->line('As a partner, you will be able to:')
            ->line('• Share tasks and activities together')
            ->line('• Access couple-specific content')
            ->line('• Collaborate on your journey')
            ->action('Accept Invitation', $acceptUrl)
            ->line('This invitation will expire in 24 hours.')
            ->line('If you did not expect this invitation, you can safely ignore this email.');

        if ($this->invitation->message) {
            $message->line('Personal message from your partner:')
                ->line('"'.$this->invitation->message.'"');
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'inviter_name' => $this->invitation->inviter->name,
            'message' => $this->invitation->message,
        ];
    }
}
