<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'sales_id',
        'sales_name',
        'lead_id',
        'lead_code',
        'category',
        'code',
        'name',
        'pic_name',
        'email',
        'phone',
        'billing_address',
        'shipping_address',
        'credit_limit',
        'payment_term_days',
        'npwp',
        'lead_source',
        'lead_status',
        'notes',
        'is_active',
    ];

    public function sales(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function lead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * Get attributed active sales user ID for commissions/incentives.
     * Rule: If customer has assigned sales PIC and that sales user is active,
     * sales credit/incentive permanently goes to that sales PIC,
     * even if another user/admin creates the quotation/order.
     * Falls back to fallback user if sales PIC is inactive or not assigned.
     */
    public function getActiveSalesUserId(?int $fallbackUserId = null): int
    {
        $fallback = $fallbackUserId ?? (auth()->id() ?? 1);

        if ($this->sales_id) {
            $sales = $this->sales ?? User::find($this->sales_id);
            if ($sales && $sales->is_active) {
                return (int) $sales->id;
            }
        }

        return (int) $fallback;
    }

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_term_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeCustomers($query)
    {
        return $query->where('type', 'customer');
    }

    public function scopeVendors($query)
    {
        return $query->where('type', 'vendor');
    }

    public function scopeLeads($query)
    {
        return $query->where('type', 'lead');
    }
}