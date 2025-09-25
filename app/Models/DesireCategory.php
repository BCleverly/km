<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DesireItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DesireCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'item_type',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'item_type' => DesireItemType::class,
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function desireItems(): HasMany
    {
        return $this->hasMany(DesireItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForItemType($query, DesireItemType $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
