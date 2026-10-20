<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->document_id)) {
                $product->document_id = (string) Str::uuid();
            }
        });
    }
    protected $fillable = [
        'locale', 
        'document_id', 
        'title', 
        'slug', 
        'category_slug', 
        'desc', 
        'image',
        'trending'
    ];

    protected $casts = [
        'title' => 'array',
        'desc'  => 'array',
        'trending' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_slug', 'slug');
    }

    public function getDisplayTitleAttribute()
    {
        if (is_array($this->title)) {
            return $this->title['id'] ?? $this->title['en'] ?? reset($this->title) ?: '-';
        }
        return $this->title ?: '-';
    }
}