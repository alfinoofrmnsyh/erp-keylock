<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'asset_code',
        'serial_number',
        'ownership',
        'license_plate',
        'year',
        'hour_meter',
        'current_location',
        'status',
        'condition_notes',
        'last_service_at',
        'next_service_due',
    ];

    protected $casts = [
        'year' => 'integer',
        'hour_meter' => 'decimal:2',
        'last_service_at' => 'date',
        'next_service_due' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryItems(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(AssetService::class)->latest('service_date')->latest('id');
    }

    public function latestService(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AssetService::class)->latest('service_date')->latest('id');
    }

    public function activeOrderItem(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderItem::class)
            ->whereHas('order', function ($q) {
                $q->whereIn('status', ['draft', 'sent', 'approved', 'in_progress'])
                  ->whereIn('order_type', ['rental_order', 'sales_order']);
            })
            ->latest('id');
    }

    public function getActiveRentalAttribute()
    {
        return $this->activeOrderItem?->order;
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeRented($query)
    {
        return $query->where('status', 'rented');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }
}
