<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
        'code',
        'company_name',
        'industry',
        'address',
        'website',
        'source',
        'entry_date',
        'sales_id',
        'pic_name',
        'pic_title',
        'pic_phone',
        'pic_email',
        'additional_pics',
        'deal_type',
        'product_interest',
        'specification',
        'items',
        'quantity',
        'rental_duration',
        'project_location',
        'estimated_budget',
        'estimated_value',
        'expected_closing_date',
        'customer_notes',
        'pipeline_stage',
        'priority',
        'internal_notes',
        'converted_customer_id',
        'converted_order_id',
        'converted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'expected_closing_date' => 'date',
        'converted_at' => 'datetime',
        'additional_pics' => 'array',
        'items' => 'array',
        'estimated_budget' => 'decimal:2',
        'estimated_value' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'converted_customer_id');
    }

    public function convertedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class, 'lead_id')->latest('activity_date');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class, 'lead_id')->orderBy('scheduled_at', 'asc');
    }

    public function pendingFollowUps(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class, 'lead_id')->where('status', 'pending')->orderBy('scheduled_at', 'asc');
    }

    public function stageHistories(): HasMany
    {
        return $this->hasMany(LeadStageHistory::class, 'lead_id')->latest('created_at');
    }

    /**
     * Get next upcoming or overdue follow-up task
     */
    public function nextFollowUp()
    {
        return $this->pendingFollowUps()->first();
    }

    public static function stageLabels(): array
    {
        return [
            'new' => 'Prospek Baru',
            'contacted' => 'Sudah Dihubungi',
            'qualified' => 'Qualified / Tertarik',
            'proposal' => 'Penawaran',
            'negotiation' => 'Negosiasi',
            'won' => 'Won (Berhasil)',
            'lost' => 'Lost (Gagal)',
        ];
    }

    public static function stageZhLabels(): array
    {
        return [
            'new' => '新线索',
            'contacted' => '已初次联系',
            'qualified' => '已合格 / 意向明确',
            'proposal' => '已发送正式报价',
            'negotiation' => '商务与规格谈判',
            'won' => '赢单 (成交转化)',
            'lost' => '输单 (流失/取消)',
        ];
    }
}
