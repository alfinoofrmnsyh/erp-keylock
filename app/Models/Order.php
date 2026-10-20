<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_po_number',
        'order_type',
        'quotation_type',
        'procurement_type',
        'contact_id',
        'parent_id',
        'order_date',
        'valid_until',
        'rental_start_date',
        'rental_end_date',
        'rental_duration',
        'rental_duration_unit',
        'status',
        'delivery_status',
        'invoice_status',
        'invoice_id',
        'subtotal',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'pph_percent',
        'pph_amount',
        'shipping_fee',
        'shipping_delivery_fee',
        'shipping_return_fee',
        'vendor_cost',
        'logistics_cost',
        'total_amount',
        'gross_profit',
        'gross_margin_percent',
        'deposit_amount',
        'payment_term',
        'shipping_address',
        'notes',
        'cancel_reason',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'valid_until' => 'date',
        'rental_start_date' => 'date',
        'rental_end_date' => 'date',
        'rental_duration' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'pph_percent' => 'decimal:2',
        'pph_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'shipping_delivery_fee' => 'decimal:2',
        'shipping_return_fee' => 'decimal:2',
        'vendor_cost' => 'decimal:2',
        'logistics_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'gross_margin_percent' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeQuotations($query)
    {
        return $query->where('order_type', 'quotation');
    }

    public function scopeSalesOrders($query)
    {
        return $query->where('order_type', 'sales_order');
    }

    public function scopeRentalOrders($query)
    {
        return $query->where('order_type', 'rental_order');
    }

    public function scopePurchaseOrders($query)
    {
        return $query->where('order_type', 'purchase_order');
    }

    /**
     * Linked Vendor Purchase Orders (Sub-rents, item purchases, towing)
     */
    public function vendorOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_id')->where('order_type', 'purchase_order');
    }

    /**
     * Customer order this PO was created for (if linked)
     */
    public function customerOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_id');
    }

    /**
     * Recalculate and persist gross margin for customer order
     */
    public function recalculateMargin(): void
    {
        if (!in_array($this->order_type, ['sales_order', 'rental_order'])) {
            return;
        }

        // 1. Calculate vendor costs from linked POs or item cost_prices
        $linkedVendorCost = $this->vendorOrders()->where('status', '!=', 'cancelled')->sum('total_amount');
        if ($linkedVendorCost == 0) {
            $itemCostTotal = 0;
            foreach ($this->items as $item) {
                $duration = max(1, $item->duration ?? 1);
                $itemCostTotal += (($item->cost_price ?? 0) * $item->quantity * $duration);
            }
            $vendorCost = $itemCostTotal;
        } else {
            $vendorCost = $linkedVendorCost;
        }

        $logisticsCost = $this->logistics_cost ?? 0;
        $revenue = $this->subtotal; // Omset before tax or total amount
        $grossProfit = $revenue - $vendorCost - $logisticsCost;
        $grossMarginPercent = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 2) : 0;

        $this->updateQuietly([
            'vendor_cost' => $vendorCost,
            'logistics_cost' => $logisticsCost,
            'gross_profit' => $grossProfit,
            'gross_margin_percent' => $grossMarginPercent,
        ]);
    }

    /**
     * Get effective rental end date
     */
    public function getEffectiveEndDateAttribute()
    {
        if ($this->rental_end_date) {
            return $this->rental_end_date;
        }
        if ($this->rental_start_date && $this->rental_duration) {
            $unit = strtolower($this->rental_duration_unit ?? 'month');
            $date = \Carbon\Carbon::parse($this->rental_start_date);
            if (str_contains($unit, 'day') || str_contains($unit, 'hari')) {
                return $date->addDays((int) $this->rental_duration);
            } elseif (str_contains($unit, 'week') || str_contains($unit, 'minggu')) {
                return $date->addWeeks((int) $this->rental_duration);
            } elseif (str_contains($unit, 'year') || str_contains($unit, 'tahun')) {
                return $date->addYears((int) $this->rental_duration);
            } else {
                return $date->addMonths((int) $this->rental_duration);
            }
        }
        return null;
    }
}
