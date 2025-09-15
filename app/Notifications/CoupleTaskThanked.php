<?php

namespace App\Notifications;

use App\Models\CoupleTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoupleTaskThanked extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public CoupleTask $coupleTask
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Thank You from ' . $this->coupleTask->assignedTo->display_name)
            ->greeting('Hello ' . $notifiable->display_name . '!')
            ->line('Your submissive partner has sent you a thank you message for the task:')
            ->line('**' . $this->coupleTask->title . '**')
            ->line('**Thank you message:**')
            ->line('"' . $this->coupleTask->thank_you_message . '"')
            ->action('View Details', route('app.couple-tasks.my-tasks'))
            ->line('You\'re doing great! 💕');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'couple_task_thanked',
            'couple_task_id' => $this->coupleTask->id,
            'thanked_by' => $this->coupleTask->assignedTo->display_name,
            'title' => $this->coupleTask->title,
            'thank_you_message' => $this->coupleTask->thank_you_message,
            'thanked_at' => $this->coupleTask->thanked_at->toISOString(),
        ];
    }
}
