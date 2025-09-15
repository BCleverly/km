<?php

namespace App\Notifications;

use App\Models\CoupleTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoupleTaskCompleted extends Notification implements ShouldQueue
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
            ->subject('Task Completed by ' . $this->coupleTask->assignedTo->display_name)
            ->greeting('Hello ' . $notifiable->display_name . '!')
            ->line('Great news! Your submissive partner has completed the task you assigned:')
            ->line('**' . $this->coupleTask->title . '**')
            ->when($this->coupleTask->completion_notes, function ($mail) {
                return $mail->line('**Completion notes:**')
                           ->line('"' . $this->coupleTask->completion_notes . '"');
            })
            ->line('Completed on: ' . $this->coupleTask->completed_at->format('M j, Y g:i A'))
            ->action('View Details', route('app.couple-tasks.my-tasks'))
            ->line('Well done! 🎉');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'couple_task_completed',
            'couple_task_id' => $this->coupleTask->id,
            'completed_by' => $this->coupleTask->assignedTo->display_name,
            'title' => $this->coupleTask->title,
            'completed_at' => $this->coupleTask->completed_at->toISOString(),
            'completion_notes' => $this->coupleTask->completion_notes,
        ];
    }
}
