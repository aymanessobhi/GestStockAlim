<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_products'); // Adjust table name if different
    }
}
