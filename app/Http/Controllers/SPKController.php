<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Services\SPKCalculationService;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $ahp    = $this->spkService->calculateAHP();
        $topsis = $this->spkService->calculateTOPSIS();

        $ranked = $topsis['ranked_results'];
        $avgVi  = count($ranked) > 0 ? array_sum(array_column($ranked, 'vi')) / count($ranked) : 0;

        $metrics = [
            'total_alternatif' => count($topsis['alternatifs']),
            'total_kriteria'   => count($topsis['kriterias']),
            'metode'           => 'AHP + TOPSIS',
            'avg_preferensi'   => $avgVi,
            'cr'               => $ahp['cr'],
            'is_consistent'    => $ahp['is_consistent'],
            'batch_label'      => 'Dataset Aktif SPK',
            'studi_kasus'      => 'Sistem Pendukung Keputusan AHP & TOPSIS',
            'tanggal'          => date('l, d F Y'),
        ];

        $topKandidat = array_slice($ranked, 0, 5);
        $isEmpty     = $metrics['total_kriteria'] === 0;

        return view('spk.dashboard', compact('metrics', 'topKandidat', 'ranked', 'ahp', 'isEmpty'));
    }

    /**
     * Display the Criteria and Weights management screen.
     */
    public function kriteria(): View
    {
        $ahp     = $this->spkService->calculateAHP();
        $topsis  = $this->spkService->calculateTOPSIS();
        $kriterias = $topsis['kriterias'];
        $isEmpty = $kriterias->isEmpty();

        return view('spk.kriteria', compact('kriterias', 'ahp', 'isEmpty'));
    }

    /**
     * Display the Decision Matrix and Alternatives screen.
     */
    public function alternatif(): View
    {
        $topsis = $this->spkService->calculateTOPSIS();

        $kriterias  = $topsis['kriterias'];
        $alternatifs = $topsis['alternatifs'];
        $matrixX    = $topsis['matrix_x'];
        $divisors   = $topsis['divisors'];
        $isEmpty    = $kriterias->isEmpty() || $alternatifs->isEmpty();

        return view('spk.alternatif', compact('kriterias', 'alternatifs', 'matrixX', 'divisors', 'isEmpty'));
    }

    /**
     * Display the Decision Results, TOPSIS Rankings and Recommendations screen.
     */
    public function keputusan(): View
    {
        $topsis = $this->spkService->calculateTOPSIS();
        $ranked = $topsis['ranked_results'];

        $podium = [
            'juara'    => $ranked[0] ?? null,
            'runner_up' => $ranked[1] ?? null,
            'third'    => $ranked[2] ?? null,
        ];

        $isEmpty = count($ranked) === 0;

        return view('spk.keputusan', compact('topsis', 'ranked', 'podium', 'isEmpty'));
    }

    /**
     * Display the Import data page.
     */
    public function importPage(): View
    {
        $kriteriaCount  = Kriteria::count();
        $alternatifCount = Alternatif::count();
        $hasData        = $kriteriaCount > 0 || $alternatifCount > 0;
        $stats          = ['kriteria' => $kriteriaCount, 'alternatif' => $alternatifCount];

        return view('spk.import', compact('hasData', 'stats'));
    }

    /**
     * Export the final TOPSIS ranking and evaluation results to a CSV file.
     */
    public function exportKeputusanCsv(): StreamedResponse
    {
        $topsis  = $this->spkService->calculateTOPSIS();
        $ranked  = $topsis['ranked_results'];

        $filename = 'hasil_keputusan_topsis_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->streamDownload(function () use ($ranked) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($handle, [
                'Peringkat',
                'Kode Alternatif',
                'Nama Alternatif',
                'Kategori',
                'Jarak Solusi Positif (D+)',
                'Jarak Solusi Negatif (D-)',
                'Nilai Preferensi (Vi)',
                'Persentase Kelayakan',
                'Status Kelayakan',
                'Rekomendasi',
            ]);

            foreach ($ranked as $item) {
                fputcsv($handle, [
                    $item['rank'],
                    $item['kode'],
                    $item['alternatif']->nama ?? '',
                    $item['alternatif']->kategori ?? 'Umum',
                    number_format($item['d_plus'], 6, '.', ''),
                    number_format($item['d_minus'], 6, '.', ''),
                    number_format($item['vi'], 6, '.', ''),
                    round($item['vi'] * 100, 2) . '%',
                    $item['kelayakan'],
                    $item['status'],
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }
}
