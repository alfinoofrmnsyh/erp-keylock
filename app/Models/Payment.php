<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'payment_type',
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method',
        'bank_account',
        'reference_number',
        'proof_image',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCashIn($query)
    {
        return $query->where('payment_type', 'in');
    }

    public function scopeCashOut($query)
    {
        return $query->where('payment_type', 'out');
    }
}
