<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DesireResponseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerDesireResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_id',
        'desire_item_id',
        'response_type',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'response_type' => DesireResponseType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function desireItem(): BelongsTo
    {
        return $this->belongsTo(DesireItem::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForPartner($query, int $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    public function scopeForDesireItem($query, int $desireItemId)
    {
        return $query->where('desire_item_id', $desireItemId);
    }

    public function scopeByResponseType($query, DesireResponseType $responseType)
    {
        return $query->where('response_type', $responseType);
    }

    public function scopeYes($query)
    {
        return $query->where('response_type', DesireResponseType::Yes);
    }

    public function scopeMaybe($query)
    {
        return $query->where('response_type', DesireResponseType::Maybe);
    }

    public function scopeNo($query)
    {
        return $query->where('response_type', DesireResponseType::No);
    }
}
