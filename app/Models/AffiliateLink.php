<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AffiliateLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'url',
        'partner_type',
        'commission_type',
        'commission_rate',
        'commission_fixed',
        'currency',
        'is_active',
        'is_premium',
        'tracking_id',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'commission_fixed' => 'decimal:2',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Tasks\Task::class, 'task_affiliate_links')
            ->withPivot(['link_text', 'description', 'sort_order', 'is_primary'])
            ->orderBy('task_affiliate_links.sort_order');
    }

    /**
     * Scope a query to only include active affiliate links.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include premium affiliate links.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope a query to only include affiliate links of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('partner_type', $type);
    }

    /**
     * Get the commission amount for a given order value.
     */
    public function calculateCommission(float $orderValue): float
    {
        if ($this->commission_type === 'fixed') {
            return $this->commission_fixed ?? 0;
        }

        return $orderValue * (($this->commission_rate ?? 0) / 100);
    }

    /**
     * Get the formatted commission rate.
     */
    public function getFormattedCommissionRateAttribute(): string
    {
        if ($this->commission_type === 'fixed') {
            return $this->currency . ' ' . number_format($this->commission_fixed ?? 0, 2);
        }

        return number_format($this->commission_rate ?? 0, 2) . '%';
    }

    /**
     * Get the partner type options.
     */
    public static function getPartnerTypes(): array
    {
        return [
            'general' => 'General',
            'toys' => 'Sex Toys',
            'clothing' => 'Lingerie & Clothing',
            'books' => 'Books & Education',
            'accessories' => 'Accessories',
            'health' => 'Health & Wellness',
            'subscription' => 'Subscription Services',
            'other' => 'Other',
        ];
    }

    /**
     * Get the commission type options.
     */
    public static function getCommissionTypes(): array
    {
        return [
            'percentage' => 'Percentage',
            'fixed' => 'Fixed Amount',
        ];
    }
}