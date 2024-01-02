<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;
    public function products()
    {
        return $this->belongsToMany(Product::class, 'recipe_products'); // Adjust table name if different
    }
}
