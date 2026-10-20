<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetService extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'service_number',
        'service_date',
        'service_type',
        'hour_meter',
        'technician_name',
        'vendor_contact_id',
        'cost',
        'status',
        'description',
        'spareparts_replaced',
        'next_service_date',
        'next_service_hour_meter',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_date' => 'date',
        'hour_meter' => 'decimal:2',
        'next_service_hour_meter' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'vendor_contact_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
