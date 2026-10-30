<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'satuan',
        'tipe',
        'bobot',
        'urutan',
    ];

    protected $casts = [
        'bobot' => 'float',
        'urutan' => 'integer',
    ];

    /**
     * Get the evaluation values associated with this criterion.
     */
    public function nilaiAlternatifs(): HasMany
    {
        return $this->hasMany(NilaiAlternatif::class, 'kriteria_id');
    }
}

