<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PartnerInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'invited_by',
        'email',
        'token',
        'expires_at',
        'accepted_at',
        'accepted_by',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return ! is_null($this->accepted_at);
    }

    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isExpired();
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public static function createInvitation(User $inviter, string $email, ?string $message = null): self
    {
        return self::create([
            'invited_by' => $inviter->id,
            'email' => $email,
            'token' => self::generateToken(),
            'expires_at' => now()->addHours(24), // 24 hours to accept
            'message' => $message,
        ]);
    }

    public static function hasPendingInvitation(User $user): bool
    {
        return self::where('invited_by', $user->id)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->exists();
    }

    public static function getPendingInvitation(User $user): ?self
    {
        return self::where('invited_by', $user->id)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->first();
    }

    public function getTimeRemainingAttribute(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }

        $diff = now()->diff($this->expires_at);

        if ($diff->h > 0) {
            return $diff->h.'h '.$diff->i.'m remaining';
        }

        return $diff->i.'m remaining';
    }
}
