<?php

declare(strict_types=1);

namespace App;

enum TaskActivityType: string
{
    case Assigned = 'assigned';
    case Completed = 'completed';
    case Failed = 'failed';
    case RewardReceived = 'reward_received';
    case PunishmentReceived = 'punishment_received';
    case TaskCreated = 'task_created';
    case TaskViewed = 'task_viewed';

    public function label(): string
    {
        return match($this) {
            self::Assigned => 'Task Assigned',
            self::Completed => 'Task Completed',
            self::Failed => 'Task Failed',
            self::RewardReceived => 'Reward Received',
            self::PunishmentReceived => 'Punishment Received',
            self::TaskCreated => 'Task Created',
            self::TaskViewed => 'Task Viewed',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Assigned => '📋',
            self::Completed => '✅',
            self::Failed => '❌',
            self::RewardReceived => '🎁',
            self::PunishmentReceived => '⚡',
            self::TaskCreated => '➕',
            self::TaskViewed => '👁️',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Assigned => 'blue',
            self::Completed => 'green',
            self::Failed => 'red',
            self::RewardReceived => 'yellow',
            self::PunishmentReceived => 'orange',
            self::TaskCreated => 'purple',
            self::TaskViewed => 'gray',
        };
    }
}
