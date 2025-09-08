<?php

declare(strict_types=1);

namespace App\Models\Tasks;

use App\ContentStatus;
use App\Models\Models\Tag;
use App\TargetUserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\HasTags;

class Outcome extends Model
{
    /** @use HasFactory<\Database\Factories\OutcomeFactory> */
    use HasFactory, HasTags;

    protected static function newFactory()
    {
        return \Database\Factories\OutcomeFactory::new();
    }

    protected $fillable = [
        'title',
        'description',
        'difficulty_level',
        'target_user_type',
        'user_id',
        'status',
        'view_count',
        'is_premium',
        'intended_type', // 'reward' or 'punishment'
    ];

    protected function casts(): array
    {
        return [
            'difficulty_level' => 'integer',
            'target_user_type' => TargetUserType::class,
            'status' => ContentStatus::class,
            'view_count' => 'integer',
            'is_premium' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedOutcomes(): HasMany
    {
        return $this->hasMany(UserOutcome::class, 'outcome_id');
    }

    public function recommendedForTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_recommended_outcomes')
            ->withPivot('sort_order')
            ->orderBy('task_recommended_outcomes.sort_order');
    }

    /**
     * Scope a query to only include approved outcomes.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', ContentStatus::Approved);
    }

    /**
     * Scope a query to only include pending outcomes.
     */
    public function scopePending($query)
    {
        return $query->where('status', ContentStatus::Pending);
    }

    /**
     * Scope a query to only include premium outcomes.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope a query to only include outcomes for a specific user type.
     */
    public function scopeForUserType($query, TargetUserType $userType)
    {
        return $query->where('target_user_type', $userType);
    }

    /**
     * Scope a query to only include outcomes intended as rewards.
     */
    public function scopeIntendedAsRewards($query)
    {
        return $query->where('intended_type', 'reward');
    }

    /**
     * Scope a query to only include outcomes intended as punishments.
     */
    public function scopeIntendedAsPunishments($query)
    {
        return $query->where('intended_type', 'punishment');
    }

    /**
     * Check if this outcome is intended as a reward.
     */
    public function isIntendedAsReward(): bool
    {
        return $this->intended_type === 'reward';
    }

    /**
     * Check if this outcome is intended as a punishment.
     */
    public function isIntendedAsPunishment(): bool
    {
        return $this->intended_type === 'punishment';
    }

    /**
     * Get the intended type label.
     */
    public function getIntendedTypeLabelAttribute(): string
    {
        return $this->intended_type === 'reward' ? 'Reward' : 'Punishment';
    }

    /**
     * Get the tag class name for this model
     */
    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    /**
     * Override the tags relationship to use our custom tag model
     */
    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }
}
