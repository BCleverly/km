<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Fantasy extends Model implements ReactableInterface
{
    use HasFactory, Reactable, LogsActivity;

    protected $fillable = [
        'content',
        'word_count',
        'user_id',
        'status',
        'view_count',
        'report_count',
        'is_premium',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'word_count' => 'integer',
            'view_count' => 'integer',
            'report_count' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * Get the user that owns the fantasy.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reactions for the fantasy.
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(\Qirolab\Laravel\Reactions\Models\Reaction::class, 'reactable');
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['content', 'status', 'report_count'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Scope to get approved fantasies.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 2);
    }

    /**
     * Scope to get pending fantasies.
     */
    public function scopePending($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get rejected fantasies.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 4);
    }

    /**
     * Scope to get fantasies in review.
     */
    public function scopeInReview($query)
    {
        return $query->where('status', 3);
    }

    /**
     * Scope to get public fantasies (approved and not premium).
     */
    public function scopePublic($query)
    {
        return $query->approved()->where('is_premium', false);
    }

    /**
     * Scope to get premium fantasies.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            1 => 'Pending',
            2 => 'Approved',
            3 => 'In Review',
            4 => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Check if the fantasy is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 2;
    }

    /**
     * Check if the fantasy is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if the fantasy is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 4;
    }

    /**
     * Check if the fantasy is in review.
     */
    public function isInReview(): bool
    {
        return $this->status === 3;
    }

    /**
     * Get the view count from Redis.
     */
    public function getViewCount(): int
    {
        return app(\App\Services\ViewTrackingService::class)->getViewCount('fantasy', $this->id);
    }

    /**
     * Increment the report count.
     */
    public function incrementReportCount(): void
    {
        $this->increment('report_count');
    }

    /**
     * Get the word count from content.
     */
    public function calculateWordCount(): int
    {
        return str_word_count(strip_tags($this->content));
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fantasy) {
            if (empty($fantasy->word_count)) {
                $fantasy->word_count = $fantasy->calculateWordCount();
            }
        });

        static::updating(function ($fantasy) {
            if ($fantasy->isDirty('content')) {
                $fantasy->word_count = $fantasy->calculateWordCount();
            }
        });
    }
}