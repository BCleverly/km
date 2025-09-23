<?php

declare(strict_types=1);

namespace App\Models;

use App\ContentStatus;
use App\Enums\DesireItemType;
use App\TargetUserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Traits\Reactable;

class DesireItem extends Model implements ReactableInterface
{
    use HasFactory, Reactable;

    protected $fillable = [
        'title',
        'description',
        'item_type',
        'category_id',
        'target_user_type',
        'user_id',
        'status',
        'view_count',
        'is_premium',
        'difficulty_level',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'item_type' => DesireItemType::class,
            'target_user_type' => TargetUserType::class,
            'status' => ContentStatus::class,
            'view_count' => 'integer',
            'is_premium' => 'boolean',
            'difficulty_level' => 'integer',
            'tags' => 'array',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DesireCategory::class);
    }

    public function partnerResponses(): HasMany
    {
        return $this->hasMany(PartnerDesireResponse::class);
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ContentStatus::Approved);
    }

    public function scopePending($query)
    {
        return $query->where('status', ContentStatus::Pending);
    }

    public function scopeForUserType($query, TargetUserType $userType)
    {
        return $query->where(function ($q) use ($userType) {
            $q->where('target_user_type', $userType)
                ->orWhere('target_user_type', TargetUserType::Any);
        });
    }

    public function scopeForItemType($query, DesireItemType $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeByDifficulty($query, int $min, int $max)
    {
        return $query->whereBetween('difficulty_level', [$min, $max]);
    }

    public function scopeNotRespondedByUser($query, User $user)
    {
        return $query->whereDoesntHave('partnerResponses', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    public function scopeRespondedByUser($query, User $user)
    {
        return $query->whereHas('partnerResponses', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    public function scopeNotRespondedBy($query, int $userId)
    {
        return $query->whereDoesntHave('partnerResponses', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeRespondedBy($query, int $userId)
    {
        return $query->whereHas('partnerResponses', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
