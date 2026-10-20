<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    // Mengizinkan semua kolom di bawah ini untuk diisi melalui Command
    protected $fillable = [
        'locale', 'hero_section', 'about_section', 'service_section', 
        'advantages_section', 'products_section', 'workflow_section', 
        'testimonials_section', 'coverage_section', 'faq_section', 
        'cta_section', 'footer_section'
    ];

    // Otomatis mengubah data JSON dari database menjadi Array PHP
    protected $casts = [
        'hero_section' => 'array',
        'about_section' => 'array',
        'service_section' => 'array',
        'advantages_section' => 'array',
        'products_section' => 'array',
        'workflow_section' => 'array',
        'testimonials_section' => 'array',
        'coverage_section' => 'array',
        'faq_section' => 'array',
        'cta_section' => 'array',
        'footer_section' => 'array',
    ];
}