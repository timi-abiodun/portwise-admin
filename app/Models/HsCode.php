<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HsCode extends Model
{
    protected $table = 'hs_codes';
    protected $fillable = ['category_id', 'code', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
