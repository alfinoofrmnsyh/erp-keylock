<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'vendor_ref_no',
        'invoice_type',
        'order_id',
        'customer_order_id',
        'delivery_id',
        'contact_id',
        'issue_date',
        'due_date',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'shipping_delivery_fee',
        'shipping_return_fee',
        'tax_percent',
        'tax_amount',
        'pph_percent',
        'pph_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'status',
        'payment_terms',
        'tax_invoice_document',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'shipping_delivery_fee' => 'decimal:2',
        'shipping_return_fee' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'pph_percent' => 'decimal:2',
        'pph_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'invoice_id');
    }

    public function customerOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'customer_order_id');
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCustomerInvoices($query)
    {
        return $query->where('invoice_type', 'customer_invoice');
    }

    public function scopeVendorBills($query)
    {
        return $query->where('invoice_type', 'vendor_bill');
    }

    /**
     * Recalculate payment status and balance
     */
    public function recalculatePaymentStatus(): void
    {
        $paid = $this->payments()->where('status', 'verified')->sum('amount');
        $balance = max(0, $this->total_amount - $paid);

        $status = $this->status;
        if ($paid >= $this->total_amount) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial';
        } elseif ($this->due_date && $this->due_date->isPast()) {
            $status = 'overdue';
        } else {
            $status = 'sent';
        }

        $this->updateQuietly([
            'paid_amount' => $paid,
            'balance_due' => $balance,
            'status' => $status,
        ]);
    }
}
