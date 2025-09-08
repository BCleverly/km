<?php

declare(strict_types=1);

namespace App\Models\Models;

use App\ContentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'status' => ContentStatus::class,
        'approved_at' => 'datetime',
    ];

    /**
     * Scope to get only approved tags
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::Approved);
    }

    /**
     * Scope to get only pending tags
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::Pending);
    }

    /**
     * Scope to get tags by type
     */
    public function scopeWithType(Builder $query, ?string $type = null): Builder
    {
        if ($type === null) {
            return $query;
        }
        return $query->where('type', $type);
    }

    /**
     * Get the user who created this tag
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the user who approved this tag
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    /**
     * Check if the tag is approved
     */
    public function isApproved(): bool
    {
        return $this->status === ContentStatus::Approved;
    }

    /**
     * Check if the tag is pending
     */
    public function isPending(): bool
    {
        return $this->status === ContentStatus::Pending;
    }

    /**
     * Approve the tag
     */
    public function approve(int $approvedBy): void
    {
        $this->update([
            'status' => ContentStatus::Approved,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the tag
     */
    public function reject(int $rejectedBy): void
    {
        $this->update([
            'status' => ContentStatus::Rejected,
            'approved_by' => $rejectedBy,
            'approved_at' => now(),
        ]);
    }
}
