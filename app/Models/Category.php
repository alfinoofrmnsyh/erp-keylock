<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'image'];

    protected $casts = [
        'name' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_slug', 'slug');
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'category_id', 'id');
    }

    public function getDisplayNameAttribute()
    {
        if (is_array($this->name)) {
            return $this->name['id'] ?? $this->name['en'] ?? reset($this->name) ?: '-';
        }
        return $this->name ?: '-';
    }
}
