<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiAlternatif extends Model
{
    protected $fillable = [
        'alternatif_id',
        'kriteria_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    /**
     * Get the alternative for this evaluation value.
     */
    public function alternatif(): BelongsTo
    {
        return $this->belongsTo(Alternatif::class, 'alternatif_id');
    }

    /**
     * Get the criterion for this evaluation value.
     */
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }
}

