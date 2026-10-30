<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use App\Services\SPKCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlternatifController extends Controller
{
    public function __construct(
        public SPKCalculationService $spkService
    ) {}

    /**
     * Store a newly created alternative.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:alternatifs,kode',
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $maxUrutan = Alternatif::max('urutan') ?? 0;
        $validated['urutan'] = $validated['urutan'] ?? ($maxUrutan + 1);
        $validated['kategori'] = $validated['kategori'] ?? 'Desa';

        $alternatif = Alternatif::create($validated);

        // Auto-assign default nilai 0 for all existing criteria
        $kriterias = Kriteria::all();
        $inputNilai = $request->input('nilai', []);

        foreach ($kriterias as $k) {
            $val = isset($inputNilai[$k->kode]) ? (float) $inputNilai[$k->kode] : 0.0;
            NilaiAlternatif::create([
                'alternatif_id' => $alternatif->id,
                'kriteria_id' => $k->id,
                'nilai' => $val,
            ]);
        }

        return redirect()->route('spk.alternatif')->with('success', "Alternatif {$alternatif->kode} ({$alternatif->nama}) berhasil ditambahkan.");
    }

    /**
     * Update the specified alternative details.
     */
    public function update(Request $request, Alternatif $alternatif): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:alternatifs,kode,' . $alternatif->id,
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $validated['kategori'] = $validated['kategori'] ?? 'Desa';
        $alternatif->update($validated);

        return redirect()->route('spk.alternatif')->with('success', "Alternatif {$alternatif->kode} ({$alternatif->nama}) berhasil diperbarui.");
    }

    /**
     * Remove the specified alternative.
     */
    public function destroy(string|int $alternatif): RedirectResponse
    {
        $item = Alternatif::find($alternatif);
        if (! $item) {
            $item = Alternatif::where('kode', $alternatif)->first();
        }

        if (! $item) {
            return redirect()->route('spk.alternatif')->with('warning', 'Alternatif tidak ditemukan atau sudah dihapus.');
        }

        $kode = $item->kode;
        $nama = $item->nama;
        $item->delete();

        return redirect()->route('spk.alternatif')->with('success', "Alternatif {$kode} ({$nama}) berhasil dihapus.");
    }

    /**
     * Bulk update decision matrix values (nilai_alternatifs).
     */
    public function updateNilai(Request $request): RedirectResponse
    {
        $matrix = $request->input('nilai', []);

        foreach ($matrix as $altId => $critValues) {
            $alternatif = Alternatif::find($altId);
            if (! $alternatif) {
                continue;
            }

            foreach ($critValues as $critId => $val) {
                $kriteria = Kriteria::find($critId);
                if (! $kriteria) {
                    continue;
                }

                NilaiAlternatif::updateOrCreate(
                    [
                        'alternatif_id' => $alternatif->id,
                        'kriteria_id' => $kriteria->id,
                    ],
                    [
                        'nilai' => (float) $val,
                    ]
                );
            }
        }

        return redirect()->route('spk.alternatif')->with('success', 'Matriks keputusan alternatif berhasil diperbarui dan perhitungan TOPSIS telah disesuaikan.');
    }
}

