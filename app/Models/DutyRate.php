<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DutyRate extends Model
{
    protected $table = 'duty_rates';
    protected $fillable = [
        'hs_code_id', 'rate_type', 'duty_pct', 'rate_per_kg',
        'effective_from', 'effective_to', 'source_ref',
    ];
    protected $casts = [
        'duty_pct' => 'decimal:3',
        'rate_per_kg' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function hsCode(): BelongsTo
    {
        return $this->belongsTo(HsCode::class);
    }
}
