<?php

namespace App\Http\Controllers;

use App\Services\SPKCalculationService;
use Illuminate\View\View;

class SPKController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        public SPKCalculationService $spkService
    ) {}

    /**
     * Display the SPK main dashboard overview.
     */
    public function dashboard(): View
    {
        $ahp = $this->spkService->calculateAHP();
        $topsis = $this->spkService->calculateTOPSIS();

        $ranked = $topsis['ranked_results'];
        $avgVi = count($ranked) > 0 ? array_sum(array_column($ranked, 'vi')) / count($ranked) : 0;

        $metrics = [
            'total_alternatif' => count($topsis['alternatifs']),
            'total_kriteria' => count($topsis['kriterias']),
            'metode' => 'AHP + TOPSIS',
            'avg_preferensi' => $avgVi,
            'cr' => $ahp['cr'],
            'is_consistent' => $ahp['is_consistent'],
            'batch_label' => 'Studi Kasus Dinkes (Puskesmas Rawat Inap Baru)',
            'studi_kasus' => 'Kelompok 6 - Pemilihan 1 Lokasi Puskesmas Terbaik',
            'tanggal' => date('l, d F Y'),
        ];

        $topKandidat = array_slice($ranked, 0, 5);

        return view('spk.dashboard', compact('metrics', 'topKandidat', 'ranked', 'ahp'));
    }

    /**
     * Display the Criteria and Weights management screen.
     */
    public function kriteria(): View
    {
        $ahp = $this->spkService->calculateAHP();
        $topsis = $this->spkService->calculateTOPSIS();
        $kriterias = $topsis['kriterias'];

        return view('spk.kriteria', compact('kriterias', 'ahp'));
    }

    /**
     * Display the Decision Matrix and Alternatives screen.
     */
    public function alternatif(): View
    {
        $topsis = $this->spkService->calculateTOPSIS();

        $kriterias = $topsis['kriterias'];
        $alternatifs = $topsis['alternatifs'];
        $matrixX = $topsis['matrix_x'];
        $divisors = $topsis['divisors'];

        return view('spk.alternatif', compact('kriterias', 'alternatifs', 'matrixX', 'divisors'));
    }

    /**
     * Display the Decision Results, TOPSIS Rankings and Recommendations screen.
     */
    public function keputusan(): View
    {
        $topsis = $this->spkService->calculateTOPSIS();
        $ranked = $topsis['ranked_results'];

        $podium = [
            'juara' => $ranked[0] ?? null,
            'runner_up' => $ranked[1] ?? null,
            'third' => $ranked[2] ?? null,
        ];

        return view('spk.keputusan', compact('topsis', 'ranked', 'podium'));
    }
}
