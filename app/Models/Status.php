<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Status extends Model implements HasMedia, ReactableInterface
{
    use HasFactory, InteractsWithMedia, LogsActivity, Reactable, SoftDeletes, Commentable;

    protected $fillable = [
        'content',
        'user_id',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected $with = ['user'];

    /**
     * Get the user who created the status.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Scope to get only public statuses.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get statuses for a specific user.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope to get recent statuses.
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get the maximum character limit for status content.
     */
    public static function getMaxLength(): int
    {
        return config('app.statuses.max_length', 280);
    }

    /**
     * Check if the status content is within the character limit.
     */
    public function isWithinLimit(): bool
    {
        return strlen($this->content) <= self::getMaxLength();
    }

    /**
     * Get the character count for the status content.
     */
    public function getCharacterCountAttribute(): int
    {
        return strlen($this->content);
    }

    /**
     * Get the remaining characters available.
     */
    public function getRemainingCharactersAttribute(): int
    {
        return max(0, self::getMaxLength() - $this->character_count);
    }

    /**
     * Register media collections for the status
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('status_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
    }

    /**
     * Register media conversions for the status
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Status image conversions
        $this->addMediaConversion('status_thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('status_images');

        $this->addMediaConversion('status_medium')
            ->width(600)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('status_images');

        $this->addMediaConversion('status_large')
            ->width(1200)
            ->height(1200)
            ->sharpen(10)
            ->performOnCollections('status_images');
    }

    /**
     * Get the status image URL
     */
    public function getStatusImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('status_images');

        if ($media) {
            try {
                return $media->getUrl('status_medium');
            } catch (\Exception $e) {
                return $media->getUrl();
            }
        }

        return null;
    }

    /**
     * Check if the status has an image
     */
    public function hasImage(): bool
    {
        return $this->getFirstMedia('status_images') !== null;
    }

    /**
     * Get activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['content', 'is_public'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
