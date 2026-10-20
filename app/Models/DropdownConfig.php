<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'label_id',
        'label_zh',
        'badge_color',
        'order_num',
        'is_active',
        'description',
    ];

    protected $casts = [
        'order_num' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order_num')->orderBy('id');
    }

    /**
     * Get active options for a specific group
     */
    public static function getOptions(string $group)
    {
        return static::where('group', $group)
            ->where('is_active', true)
            ->orderBy('order_num')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get translated label based on current locale or requested lang
     */
    public function getLabel(string $lang = 'id'): string
    {
        if ($lang === 'zh' && !empty($this->label_zh)) {
            return $this->label_zh;
        }
        return $this->label_id ?: $this->key;
    }
}
