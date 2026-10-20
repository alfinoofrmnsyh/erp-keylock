<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    protected $table = 'galleries';

    protected $fillable = [
        'category_id',
        'image_url',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array', 
        'status' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getDisplayTitleAttribute()
    {
        if (is_array($this->title)) {
            return $this->title['id'] ?? $this->title['en'] ?? reset($this->title) ?: '-';
        }
        return $this->title ?: '-';
    }

    public function getDisplayDescriptionAttribute()
    {
        if (is_array($this->description)) {
            return $this->description['id'] ?? $this->description['en'] ?? reset($this->description) ?: '';
        }
        return $this->description ?: '';
    }
}