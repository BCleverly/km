<?php

namespace App\Notifications;

use App\Models\CoupleTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoupleTaskAssigned extends Notification implements ShouldQueue
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
            ->subject('New Task Assigned by ' . $this->coupleTask->assignedBy->display_name)
            ->greeting('Hello ' . $notifiable->display_name . '!')
            ->line('Your dominant partner has assigned you a new task:')
            ->line('**' . $this->coupleTask->title . '**')
            ->line($this->coupleTask->description)
            ->when($this->coupleTask->dom_message, function ($mail) {
                return $mail->line('**Personal message from your dominant partner:**')
                           ->line('"' . $this->coupleTask->dom_message . '"');
            })
            ->line('**Difficulty Level:** ' . $this->coupleTask->difficulty_level . '/10')
            ->line('**Due:** ' . $this->coupleTask->deadline_at->format('M j, Y g:i A'))
            ->action('View Task', route('app.couple-tasks.my-tasks'))
            ->line('Don\'t disappoint them! 💕');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'couple_task_assigned',
            'couple_task_id' => $this->coupleTask->id,
            'assigned_by' => $this->coupleTask->assignedBy->display_name,
            'title' => $this->coupleTask->title,
            'difficulty_level' => $this->coupleTask->difficulty_level,
            'deadline_at' => $this->coupleTask->deadline_at->toISOString(),
            'dom_message' => $this->coupleTask->dom_message,
        ];
    }
}
