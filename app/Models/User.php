<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Qirolab\Laravel\Reactions\Contracts\ReactsInterface;
use Qirolab\Laravel\Reactions\Traits\Reacts;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasPassKeys, ReactsInterface
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasApiTokens, HasFactory, HasRoles, InteractsWithPasskeys, Notifiable, Reacts;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'username',
        'email_verified_at',
        'partner_id',
        'subscription_plan',
        'subscription_ends_at',
        'has_used_trial',
        'trial_ends_at',
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
            'user_type' => \App\TargetUserType::class,
            'subscription_plan' => \App\Enums\SubscriptionPlan::class,
            'subscription_ends_at' => 'datetime',
            'has_used_trial' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (app()->isProduction()) {
            return str_ends_with($this->email, '@bencleverly.dev'); // && $this->hasVerifiedEmail();
        }

        return true;
    }

    /**
     * Get the user type label.
     */
    protected function userTypeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user_type?->label() ?? 'User',
        );
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

    public function displayName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->profile?->username ?? $this->name
        );
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
     * Get the user's fantasies
     */
    public function fantasies(): HasMany
    {
        return $this->hasMany(\App\Models\Fantasy::class);
    }

    /**
     * Get the user's stories
     */
    public function stories(): HasMany
    {
        return $this->hasMany(\App\Models\Story::class);
    }

    /**
     * Get the user's statuses
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(\App\Models\Status::class);
    }

    /**
     * Check if user has reached their daily status limit
     */
    public function hasReachedDailyStatusLimit(): bool
    {
        $maxPerDay = config('app.statuses.max_per_user_per_day', 10);
        $todayCount = $this->statuses()
            ->whereDate('created_at', today())
            ->count();

        return $todayCount >= $maxPerDay;
    }

    /**
     * Get the number of statuses created today
     */
    public function getTodayStatusCount(): int
    {
        return $this->statuses()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get the user's recent task activities
     */
    public function recentTaskActivities(int $limit = 10): HasMany
    {
        return $this->taskActivities()
            ->with(['task', 'userAssignedTask.media'])
            ->orderBy('activity_at', 'desc')
            ->limit($limit);
    }

    /**
     * Get user stats service instance
     *
     * @deprecated Use tasks() method instead. This method will be removed in a future version.
     */
    public function stats(): \App\Services\UserStatsService
    {
        return new \App\Services\UserStatsService($this);
    }

    /**
     * Get task service instance for task-related operations and statistics
     */
    public function tasks(): \App\Services\TaskService
    {
        return new \App\Services\TaskService($this);
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
            ->whereHas('outcome', function ($query) {
                $query->where('intended_type', 'reward');
            })
            ->first();
    }

    /**
     * Get the user's current active punishment
     */
    public function getCurrentActivePunishment(): ?UserOutcome
    {
        return $this->activeOutcomes()
            ->whereHas('outcome', function ($query) {
                $query->where('intended_type', 'punishment');
            })
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

    /**
     * Check if user is on trial (either generic trial or subscription trial)
     */
    public function isOnTrial(): bool
    {
        // Check generic trial (trial_ends_at on user)
        if ($this->onGenericTrial()) {
            return true;
        }

        // Check subscription trial
        if ($this->subscription() && $this->subscription()->onTrial()) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has an active subscription (including trial)
     */
    public function hasActiveSubscription(): bool
    {
        // Admins always have access
        if ($this->hasRole('Admin')) {
            return true;
        }

        // Check if on trial
        if ($this->isOnTrial()) {
            return true;
        }

        // Check if has active subscription
        if ($this->subscription() && $this->subscription()->active()) {
            return true;
        }

        // Check if has lifetime subscription
        if ($this->subscription_plan === \App\Enums\SubscriptionPlan::Lifetime) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has a paid subscription (not trial)
     */
    public function hasPaidSubscription(): bool
    {
        // Admins always have access
        if ($this->hasRole('Admin')) {
            return true;
        }

        // Check if has active subscription (not trial)
        if ($this->subscription() && $this->subscription()->active() && ! $this->subscription()->onTrial()) {
            return true;
        }

        // Check if has lifetime subscription
        if ($this->subscription_plan === \App\Enums\SubscriptionPlan::Lifetime) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has an active premium subscription
     */
    public function hasActivePremiumSubscription(): bool
    {
        return $this->hasPaidSubscription() && $this->subscription_plan->value >= \App\Enums\SubscriptionPlan::Premium->value;
    }

    /**
     * Check if user has a lifetime subscription
     */
    public function hasLifetimeSubscription(): bool
    {
        return $this->subscription_plan === \App\Enums\SubscriptionPlan::Lifetime;
    }

    /**
     * Check if user can upload completion images (premium feature)
     * Available to: premium subscribers, lifetime subscribers, and admins
     */
    public function canUploadCompletionImages(): bool
    {
        return $this->hasRole('Admin') || $this->subscription_plan->canUploadImages();
    }

    /**
     * Check if user can create stories
     */
    public function canCreateStories(): bool
    {
        return $this->hasRole('Admin') || $this->subscription_plan->canCreateStories();
    }

    /**
     * Check if user can access premium content
     */
    public function canAccessPremiumContent(): bool
    {
        return $this->hasRole('Admin') || $this->subscription_plan->canAccessPremiumContent();
    }

    /**
     * Check if user can create custom tasks
     */
    public function canCreateCustomTasks(): bool
    {
        return $this->hasRole('Admin') || $this->subscription_plan->canCreateCustomTasks();
    }

    /**
     * Get the maximum number of tasks per day for this user
     */
    public function getMaxTasksPerDay(): ?int
    {
        if ($this->hasRole('Admin')) {
            return null; // Unlimited for admins
        }

        return $this->subscription_plan->maxTasksPerDay();
    }

    /**
     * Check if user has reached their daily task limit
     */
    public function hasReachedDailyTaskLimit(): bool
    {
        $maxTasks = $this->getMaxTasksPerDay();

        if ($maxTasks === null) {
            return false; // Unlimited
        }

        $todayTasks = $this->assignedTasks()
            ->whereDate('created_at', today())
            ->count();

        return $todayTasks >= $maxTasks;
    }

    /**
     * Get subscription status for display
     */
    public function getSubscriptionStatusAttribute(): string
    {
        if ($this->hasRole('Admin')) {
            return 'Admin';
        }

        return $this->subscription_plan->label();
    }

    /**
     * Check if user needs to choose a subscription plan
     */
    public function needsSubscriptionChoice(): bool
    {
        // Admins don't need subscription
        if ($this->hasRole('Admin')) {
            return false;
        }

        // If they have a paid subscription, they're good
        if ($this->hasPaidSubscription()) {
            return false;
        }

        // If they're on trial, they don't need to choose yet
        if ($this->isOnTrial()) {
            return false;
        }

        // If they've used their trial and don't have a subscription, they need to choose
        return $this->has_used_trial;
    }

    /**
     * Start the trial period for a new user
     */
    public function startTrial(): void
    {
        if (! $this->has_used_trial) {
            $this->update([
                'trial_ends_at' => now()->addDays((int) config('subscription.trial_days')),
                'has_used_trial' => true,
            ]);
        }
    }

    /**
     * Get the current subscription plan
     */
    public function getCurrentPlan(): \App\Enums\SubscriptionPlan
    {
        return $this->subscription_plan;
    }

    /**
     * Update subscription plan
     */
    public function updateSubscriptionPlan(\App\Enums\SubscriptionPlan $plan): void
    {
        $this->update(['subscription_plan' => $plan]);
    }

    /**
     * Get the user's BDSM role from their profile
     */
    public function getBdsmRoleAttribute(): ?\App\Enums\BdsmRole
    {
        return $this->profile?->bdsm_role;
    }

    /**
     * Get the user's BDSM role label
     */
    public function getBdsmRoleLabelAttribute(): ?string
    {
        return $this->profile?->bdsm_role_label;
    }

    /**
     * Get the user's BDSM role description
     */
    public function getBdsmRoleDescriptionAttribute(): ?string
    {
        return $this->profile?->bdsm_role_description;
    }

    /**
     * Check if the user is dominant
     */
    public function isDominant(): bool
    {
        return $this->profile?->isDominant() ?? false;
    }

    /**
     * Check if the user is submissive
     */
    public function isSubmissive(): bool
    {
        return $this->profile?->isSubmissive() ?? false;
    }

    /**
     * Check if the user is a switch
     */
    public function isSwitch(): bool
    {
        return $this->profile?->isSwitch() ?? false;
    }

    /**
     * Check if the user has a BDSM role preference set
     */
    public function hasBdsmRole(): bool
    {
        return $this->profile?->hasBdsmRole() ?? false;
    }

    /**
     * Get couple tasks assigned by this user
     */
    public function coupleTasksAssigned(): HasMany
    {
        return $this->hasMany(CoupleTask::class, 'assigned_by');
    }

    /**
     * Get couple tasks assigned to this user
     */
    public function coupleTasksReceived(): HasMany
    {
        return $this->hasMany(CoupleTask::class, 'assigned_to');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function partnerResponses(): HasMany
    {
        return $this->hasMany(PartnerDesireResponse::class, 'user_id');
    }

    /**
     * Check if user can assign couple tasks
     */
    public function canAssignCoupleTasks(): bool
    {
        // Must be admin, have lifetime subscription, or be in a couple
        return $this->hasRole('Admin') ||
               $this->hasLifetimeSubscription() ||
               $this->user_type === \App\TargetUserType::Couple;
    }

    /**
     * Check if user can receive couple tasks
     */
    public function canReceiveCoupleTasks(): bool
    {
        // Must be admin, have lifetime subscription, or be in a couple
        return $this->hasRole('Admin') ||
               $this->hasLifetimeSubscription() ||
               $this->user_type === \App\TargetUserType::Couple;
    }

    /**
     * Get the partner for couple tasks (if in a couple)
     */
    public function getCouplePartner(): ?User
    {
        if ($this->user_type === \App\TargetUserType::Couple) {
            return $this->partner;
        }

        return null;
    }

    /**
     * Check if user can send partner invitations
     */
    public function canSendPartnerInvitations(): bool
    {
        // Couple users, lifetime subscribers, and admins can send invitations
        return $this->user_type === \App\TargetUserType::Couple ||
               $this->hasLifetimeSubscription() ||
               $this->hasRole('Admin');
    }

    /**
     * Get all sent partner invitations
     */
    public function sentPartnerInvitations()
    {
        return $this->hasMany(\App\Models\PartnerInvitation::class, 'invited_by');
    }

    /**
     * Get all accepted partner invitations
     */
    public function acceptedPartnerInvitations()
    {
        return $this->hasMany(\App\Models\PartnerInvitation::class, 'accepted_by');
    }
}
