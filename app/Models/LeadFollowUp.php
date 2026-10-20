<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LeadFollowUp extends Model
{
    use HasFactory;

    protected $table = 'lead_follow_ups';

    protected $fillable = [
        'lead_id',
        'user_id',
        'follow_up_type',
        'scheduled_at',
        'purpose_notes',
        'status',
        'completed_at',
        'result_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isToday(): bool
    {
        return $this->scheduled_at->isToday();
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->scheduled_at->isPast() && !$this->scheduled_at->isToday();
    }
}
