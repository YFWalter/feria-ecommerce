<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'is_active', 'show_on_home', 'order'];

    protected $casts = [
        'is_active'    => 'boolean',
        'show_on_home' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
