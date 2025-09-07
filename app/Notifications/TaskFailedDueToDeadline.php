<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tasks\UserAssignedTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskFailedDueToDeadline extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public UserAssignedTask $assignedTask
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $task = $this->assignedTask->task;
        $punishment = $this->assignedTask->potentialPunishment;

        return (new MailMessage)
            ->subject('Task Failed - Time to Complete Your Punishment')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your task '{$task->title}' has failed due to missing the deadline.")
            ->line("You had {$task->duration_display} to complete this task, but the time has expired.")
            ->line("As a good sub, you should now complete your punishment:")
            ->line("**{$punishment->title}**")
            ->line($punishment->description)
            ->line('Remember, being a good sub means accepting consequences and following through on commitments.')
            ->action('View Your Dashboard', route('dashboard'))
            ->line('Thank you for being part of our community!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $task = $this->assignedTask->task;
        $punishment = $this->assignedTask->potentialPunishment;

        return [
            'type' => 'task_failed_deadline',
            'task_id' => $task->id,
            'task_title' => $task->title,
            'assigned_task_id' => $this->assignedTask->id,
            'punishment_title' => $punishment->title,
            'punishment_description' => $punishment->description,
            'deadline_at' => $this->assignedTask->deadline_at,
            'message' => "Your task '{$task->title}' has failed due to missing the deadline. Complete your punishment: {$punishment->title}",
        ];
    }
}