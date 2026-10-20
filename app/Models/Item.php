<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'category',
        'brand',
        'model',
        'unit',
        'item_type',
        'cost_price',
        'sale_price',
        'rental_rate_daily',
        'rental_rate_monthly',
        'overtime_rate_per_hour',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'rental_rate_daily' => 'decimal:2',
        'rental_rate_monthly' => 'decimal:2',
        'overtime_rate_per_hour' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAvailableAssetsCountAttribute(): int
    {
        return $this->assets()->where('status', 'available')->count();
    }
}
