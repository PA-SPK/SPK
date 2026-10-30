<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alternatif extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'deskripsi',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Get the evaluation values associated with this alternative.
     */
    public function nilaiAlternatifs(): HasMany
    {
        return $this->hasMany(NilaiAlternatif::class, 'alternatif_id');
    }
}

