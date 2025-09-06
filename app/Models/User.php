<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasPassKeys
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasApiTokens, HasFactory, HasRoles, InteractsWithPasskeys, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user's display name (username from profile or name fallback)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->profile?->username ?? $this->name;
    }

    /**
     * Get the user's Gravatar URL
     */
    public function getGravatarUrlAttribute(): string
    {
        $hash = md5(strtolower(trim($this->email)));

        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=256";
    }

    /**
     * Get the user's profile picture URL
     */
    public function getProfilePictureUrlAttribute(): string
    {
        return $this->profile?->profile_picture_url ?? $this->gravatar_url;
    }

    /**
     * Get the user's cover photo URL
     */
    public function getCoverPhotoUrlAttribute(): ?string
    {
        return $this->profile?->cover_photo_url;
    }

    /**
     * Get the user's assigned tasks
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\UserAssignedTask::class);
    }

    /**
     * Get the user's created tasks
     */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\Task::class);
    }

    /**
     * Get the user's created rewards
     */
    public function createdRewards(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskReward::class);
    }

    /**
     * Get the user's created punishments
     */
    public function createdPunishments(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskPunishment::class);
    }

    /**
     * Get the user's task activities
     */
    public function taskActivities(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskActivity::class);
    }

    /**
     * Get the user's recent task activities
     */
    public function recentTaskActivities(int $limit = 10): HasMany
    {
        return $this->taskActivities()
            ->with(['task', 'userAssignedTask'])
            ->orderBy('activity_at', 'desc')
            ->limit($limit);
    }

    /**
     * Get user stats service instance
     */
    public function stats(): \App\Services\UserStatsService
    {
        return new \App\Services\UserStatsService($this);
    }

    /**
     * Get the user's active outcomes (rewards and punishments)
     */
    public function activeOutcomes()
    {
        return $this->hasMany(UserOutcome::class)
            ->active()
            ->notExpired()
            ->with(['outcome', 'task'])
            ->orderBy('assigned_at', 'desc');
    }

    /**
     * Get the user's current active reward
     */
    public function getCurrentActiveReward(): ?UserOutcome
    {
        return $this->activeOutcomes()
            ->where('outcome_type', 'App\\Models\\Tasks\\TaskReward')
            ->first();
    }

    /**
     * Get the user's current active punishment
     */
    public function getCurrentActivePunishment(): ?UserOutcome
    {
        return $this->activeOutcomes()
            ->where('outcome_type', 'App\\Models\\Tasks\\TaskPunishment')
            ->first();
    }

    /**
     * Get all user outcomes (active and completed)
     */
    public function outcomes()
    {
        return $this->hasMany(UserOutcome::class)
            ->with(['outcome', 'task'])
            ->orderBy('assigned_at', 'desc');
    }

    /**
     * Get the maximum number of active outcomes allowed for this user
     */
    public function getMaxActiveOutcomes(): int
    {
        return config('app.tasks.max_active_outcomes', 2);
    }

    /**
     * Check if the user has reached their maximum active outcomes limit
     */
    public function hasReachedOutcomeLimit(): bool
    {
        $activeCount = $this->activeOutcomes()->count();
        $maxAllowed = $this->getMaxActiveOutcomes();
        
        return $activeCount >= $maxAllowed;
    }

    /**
     * Get the number of active outcomes the user currently has
     */
    public function getActiveOutcomeCount(): int
    {
        return $this->activeOutcomes()->count();
    }

    /**
     * Get the number of remaining outcome slots available
     */
    public function getRemainingOutcomeSlots(): int
    {
        $activeCount = $this->getActiveOutcomeCount();
        $maxAllowed = $this->getMaxActiveOutcomes();
        
        return max(0, $maxAllowed - $activeCount);
    }

    /**
     * Clean up expired outcomes for this user
     */
    public function cleanupExpiredOutcomes(): int
    {
        $expiredOutcomes = $this->activeOutcomes()
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredOutcomes as $outcome) {
            $outcome->markAsExpired();
            $count++;
        }

        return $count;
    }

    /**
     * Get the oldest active outcome (for potential replacement)
     */
    public function getOldestActiveOutcome(): ?UserOutcome
    {
        return $this->activeOutcomes()
            ->orderBy('assigned_at', 'asc')
            ->first();
    }
}
