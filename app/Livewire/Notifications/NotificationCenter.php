<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\Attributes\Computed;

class NotificationCenter extends Component
{
    public function render()
    {
        return view('livewire.notifications.notification-center');
    }

    #[Computed]
    public function unreadNotifications()
    {
        return auth()->user()->unreadNotifications()
            ->latest()
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function allNotifications()
    {
        return auth()->user()->notifications()
            ->latest()
            ->limit(10)
            ->get();
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function deleteNotification($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->delete();
        }
    }
}
