<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';

    protected $fillable = [
        'name',
        'role',
        'message',
        'rating',
    ];

    protected $casts = [
        'message' => 'array',
    ];

    public function getDisplayMessageAttribute()
    {
        if (is_array($this->message)) {
            return $this->message['id'] ?? $this->message['en'] ?? reset($this->message) ?: '-';
        }
        return $this->message ?: '-';
    }
}