<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KriteriaPerbandingan extends Model
{
    protected $fillable = [
        'kriteria_1_id',
        'kriteria_2_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    /**
     * Get the first criterion in comparison.
     */
    public function kriteria1(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_1_id');
    }

    /**
     * Get the second criterion in comparison.
     */
    public function kriteria2(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_2_id');
    }
}

