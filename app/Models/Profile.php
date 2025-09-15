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
        'bdsm_role',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'bdsm_role' => \App\Enums\BdsmRole::class,
        ];
    }

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
     * Get the BDSM role label
     */
    public function getBdsmRoleLabelAttribute(): ?string
    {
        return $this->bdsm_role?->label();
    }

    /**
     * Get the BDSM role description
     */
    public function getBdsmRoleDescriptionAttribute(): ?string
    {
        return $this->bdsm_role?->description();
    }

    /**
     * Check if the user is dominant
     */
    public function isDominant(): bool
    {
        return $this->bdsm_role === \App\Enums\BdsmRole::Dominant;
    }

    /**
     * Check if the user is submissive
     */
    public function isSubmissive(): bool
    {
        return $this->bdsm_role === \App\Enums\BdsmRole::Submissive;
    }

    /**
     * Check if the user is a switch
     */
    public function isSwitch(): bool
    {
        return $this->bdsm_role === \App\Enums\BdsmRole::Switch;
    }

    /**
     * Check if the user has a BDSM role preference set
     */
    public function hasBdsmRole(): bool
    {
        return $this->bdsm_role !== null;
    }
}
