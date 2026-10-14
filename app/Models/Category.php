<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'needs_weight', 
        'is_active'
    ];
    
    protected $casts = [
        'needs_weight' => 'boolean', 
        'is_active' => 'boolean'
    ];

    public function hsCodes(): HasMany
    {
        return $this->hasMany(HsCode::class);
    }
}
