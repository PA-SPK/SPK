<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\KriteriaPerbandingan;
use App\Models\NilaiAlternatif;
use App\Services\SPKCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KriteriaController extends Controller
{
    public function __construct(
        public SPKCalculationService $spkService
    ) {}

    /**
     * Display a listing of criteria and AHP pairwise comparison.
     */
    public function index(): View
    {
        $ahp = $this->spkService->calculateAHP();
        $topsis = $this->spkService->calculateTOPSIS();
        $kriterias = $topsis['kriterias'];

        return view('spk.kriteria', compact('kriterias', 'ahp'));
    }

    /**
     * Store a newly created criterion.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:kriterias,kode',
            'nama' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'tipe' => 'required|in:Benefit,Cost',
            'urutan' => 'nullable|integer',
        ]);

        $maxUrutan = Kriteria::max('urutan') ?? 0;
        $validated['urutan'] = $validated['urutan'] ?? ($maxUrutan + 1);

        $kriteria = Kriteria::create($validated);

        // Auto-assign default nilai 0 for all existing alternatives
        $alternatifs = Alternatif::all();
        foreach ($alternatifs as $alt) {
            NilaiAlternatif::create([
                'alternatif_id' => $alt->id,
                'kriteria_id' => $kriteria->id,
                'nilai' => 0,
            ]);
        }

        // Auto-recalculate AHP & persist weights
        $this->spkService->calculateAHP(true);

        return redirect()->route('spk.kriteria')->with('success', "Kriteria {$kriteria->kode} ({$kriteria->nama}) berhasil ditambahkan.");
    }

    /**
     * Update the specified criterion.
     */
    public function update(Request $request, Kriteria $kriteria): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:kriterias,kode,' . $kriteria->id,
            'nama' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'tipe' => 'required|in:Benefit,Cost',
            'urutan' => 'nullable|integer',
        ]);

        $kriteria->update($validated);
        $this->spkService->calculateAHP(true);

        return redirect()->route('spk.kriteria')->with('success', "Kriteria {$kriteria->kode} berhasil diperbarui.");
    }

    /**
     * Remove the specified criterion.
     */
    public function destroy(Kriteria $kriteria): RedirectResponse
    {
        $kode = $kriteria->kode;
        $kriteria->delete();

        // Recalculate AHP & persist
        $this->spkService->calculateAHP(true);

        return redirect()->route('spk.kriteria')->with('success', "Kriteria {$kode} berhasil dihapus.");
    }

    /**
     * Update Pairwise Comparison Matrix (AHP).
     */
    public function updatePerbandingan(Request $request): RedirectResponse
    {
        $matrix = $request->input('matrix', []);

        foreach ($matrix as $k1Kode => $cols) {
            $k1 = Kriteria::where('kode', $k1Kode)->first();
            if (! $k1) continue;

            foreach ($cols as $k2Kode => $val) {
                $k2 = Kriteria::where('kode', $k2Kode)->first();
                if (! $k2 || $k1->id === $k2->id) continue;

                $val = (float) $val;
                if ($val <= 0) $val = 1.0;

                KriteriaPerbandingan::updateOrCreate(
                    [
                        'kriteria_1_id' => $k1->id,
                        'kriteria_2_id' => $k2->id,
                    ],
                    [
                        'nilai' => $val,
                    ]
                );

                // Auto reciprocal
                KriteriaPerbandingan::updateOrCreate(
                    [
                        'kriteria_1_id' => $k2->id,
                        'kriteria_2_id' => $k1->id,
                    ],
                    [
                        'nilai' => 1 / $val,
                    ]
                );
            }
        }

        $ahp = $this->spkService->calculateAHP(true);

        $msg = $ahp['is_consistent']
            ? "Matriks AHP berhasil diperbarui dan KONSISTEN (CR = " . number_format($ahp['cr'], 4) . ")."
            : "Peringatan: Matriks AHP berhasil diperbarui namun TIDAK KONSISTEN (CR = " . number_format($ahp['cr'], 4) . " > 0.10).";

        return redirect()->route('spk.kriteria')->with($ahp['is_consistent'] ? 'success' : 'warning', $msg);
    }
}

