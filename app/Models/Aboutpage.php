<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aboutpage extends Model
{

    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array',
    ];
}