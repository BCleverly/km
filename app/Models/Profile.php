<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Profile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'username',
        'about',
        'theme_preference',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Register media collections for the profile
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_pictures')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();

        $this->addMediaCollection('cover_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
    }

    /**
     * Register media conversions for the profile
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Profile picture conversions
        $this->addMediaConversion('profile_thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('profile_pictures');

        $this->addMediaConversion('profile_medium')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('profile_pictures');

        // Cover photo conversions
        $this->addMediaConversion('cover_thumb')
            ->width(400)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('cover_photos');

        $this->addMediaConversion('cover_medium')
            ->width(800)
            ->height(400)
            ->sharpen(10)
            ->performOnCollections('cover_photos');
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        $media = $this->getFirstMedia('profile_pictures');

        if ($media) {
            // Try to get the converted version first
            try {
                return $media->getUrl('profile_medium');
            } catch (\Exception $e) {
                // If conversion isn't ready, use the original image
                return $media->getUrl();
            }
        }

        // Fallback to user's Gravatar
        return $this->user->gravatar_url;
    }

    /**
     * Get the cover photo URL
     */
    public function getCoverPhotoUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('cover_photos');

        if ($media) {
            // Try to get the converted version first
            try {
                return $media->getUrl('cover_medium');
            } catch (\Exception $e) {
                // If conversion isn't ready, use the original image
                return $media->getUrl();
            }
        }

        return null;
    }

    /**
     * Get the effective theme preference
     * If theme_preference is 'system', detect the user's system preference
     */
    public function getEffectiveThemeAttribute(): string
    {
        if ($this->theme_preference === 'system') {
            // You can implement system detection here if needed
            // For now, we'll default to 'light' when system is selected
            return 'light';
        }

        return $this->theme_preference;
    }
}
