<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Services\SPKCalculationService;
use Illuminate\Http\Request;
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
        $ahp          = $this->spkService->calculateAHP();
        $activeMethod = $this->spkService->getActiveMethod();
        $methodLabels = $this->spkService->getMethodLabels();
        $methodInfo   = $methodLabels[$activeMethod] ?? $methodLabels['topsis'];

        $methodResult = $this->spkService->calculateActiveMethod($activeMethod);
        $ranked       = $methodResult['ranked_results'];
        $avgVi        = count($ranked) > 0 ? array_sum(array_column($ranked, 'vi')) / count($ranked) : 0;

        $metrics = [
            'total_alternatif' => count($methodResult['alternatifs']),
            'total_kriteria'   => count($methodResult['kriterias']),
            'metode'           => $methodInfo['short'],
            'metode_name'      => $methodInfo['name'],
            'active_method'    => $activeMethod,
            'avg_preferensi'   => $avgVi,
            'cr'               => $ahp['cr'],
            'is_consistent'    => $ahp['is_consistent'],
            'batch_label'      => 'Dataset Aktif SPK',
            'studi_kasus'      => "Sistem Pendukung Keputusan ({$methodInfo['short']})",
            'tanggal'          => date('l, d F Y'),
        ];

        $topKandidat = array_slice($ranked, 0, 5);
        $isEmpty     = $metrics['total_kriteria'] === 0;

        return view('spk.dashboard', compact('metrics', 'topKandidat', 'ranked', 'ahp', 'isEmpty', 'activeMethod', 'methodInfo'));
    }

    /**
     * Display the Criteria and Weights management screen.
     */
    public function kriteria(): View
    {
        $ahp          = $this->spkService->calculateAHP();
        $activeMethod = $this->spkService->getActiveMethod();
        $methodLabels = $this->spkService->getMethodLabels();
        $methodInfo   = $methodLabels[$activeMethod] ?? $methodLabels['topsis'];

        $kriterias = $ahp['kriterias'];
        $isEmpty   = $kriterias->isEmpty();

        return view('spk.kriteria', compact('kriterias', 'ahp', 'isEmpty', 'activeMethod', 'methodInfo'));
    }

    /**
     * Display the Decision Matrix and Alternatives screen.
     */
    public function alternatif(): View
    {
        $activeMethod = $this->spkService->getActiveMethod();
        $methodLabels = $this->spkService->getMethodLabels();
        $methodInfo   = $methodLabels[$activeMethod] ?? $methodLabels['topsis'];

        $topsis      = $this->spkService->calculateTOPSIS();
        $kriterias   = $topsis['kriterias'];
        $alternatifs = $topsis['alternatifs'];
        $matrixX     = $topsis['matrix_x'];
        $divisors    = $topsis['divisors'];
        $isEmpty     = $kriterias->isEmpty() || $alternatifs->isEmpty();

        return view('spk.alternatif', compact('kriterias', 'alternatifs', 'matrixX', 'divisors', 'topsis', 'isEmpty', 'activeMethod', 'methodInfo'));
    }

    /**
     * Display the Decision Results, Rankings, and Comparisons.
     */
    public function keputusan(Request $request): View
    {
        $activeMethod = $this->spkService->getActiveMethod();
        $methods      = $this->spkService->getMethodLabels();

        $allowedTabs    = ['topsis', 'saw', 'wp', 'komparasi'];
        $selectedMethod = strtolower($request->input('metode', $activeMethod));
        if (! in_array($selectedMethod, $allowedTabs, true)) {
            $selectedMethod = $activeMethod;
        }

        $comparisonData = $this->spkService->calculateComparison();
        $currentResult  = $selectedMethod === 'komparasi'
            ? $comparisonData[$activeMethod]
            : $this->spkService->calculateActiveMethod($selectedMethod);

        $ranked = $currentResult['ranked_results'] ?? [];
        $podium = [
            'juara'     => $ranked[0] ?? null,
            'runner_up' => $ranked[1] ?? null,
            'third'     => $ranked[2] ?? null,
        ];

        $isEmpty = count($ranked) === 0;

        return view('spk.keputusan', compact(
            'activeMethod',
            'selectedMethod',
            'methods',
            'currentResult',
            'comparisonData',
            'ranked',
            'podium',
            'isEmpty'
        ));
    }

    /**
     * Display the Import data page.
     */
    public function importPage(): View
    {
        $kriteriaCount   = Kriteria::count();
        $alternatifCount = Alternatif::count();
        $hasData         = $kriteriaCount > 0 || $alternatifCount > 0;
        $stats           = ['kriteria' => $kriteriaCount, 'alternatif' => $alternatifCount];

        return view('spk.import', compact('hasData', 'stats'));
    }

    /**
     * Export ranking results to a CSV file based on selected or active method.
     */
    public function exportKeputusanCsv(Request $request): StreamedResponse
    {
        $metode = $request->input('metode', $this->spkService->getActiveMethod());
        $methods = $this->spkService->getMethodLabels();
        $methodInfo = $methods[$metode] ?? $methods['topsis'];

        $result = $this->spkService->calculateActiveMethod($metode);
        $ranked = $result['ranked_results'] ?? [];

        $filename = "hasil_keputusan_{$metode}_" . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->streamDownload(function () use ($ranked, $metode) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header
            $headerCols = [
                'Peringkat',
                'Kode Alternatif',
                'Nama Alternatif',
                'Kategori',
            ];

            if ($metode === 'topsis') {
                $headerCols[] = 'Jarak Solusi Positif (D+)';
                $headerCols[] = 'Jarak Solusi Negatif (D-)';
            } elseif ($metode === 'wp') {
                $headerCols[] = 'Nilai Vektor S';
            }

            $headerCols[] = 'Nilai Preferensi (Vi)';
            $headerCols[] = 'Persentase Kelayakan';
            $headerCols[] = 'Status Kelayakan';

            fputcsv($handle, $headerCols);

            foreach ($ranked as $item) {
                $row = [
                    $item['rank'],
                    $item['kode'],
                    $item['alternatif']->nama ?? '',
                    $item['alternatif']->kategori ?? 'Umum',
                ];

                if ($metode === 'topsis') {
                    $row[] = number_format($item['d_plus'] ?? 0, 6, '.', '');
                    $row[] = number_format($item['d_minus'] ?? 0, 6, '.', '');
                } elseif ($metode === 'wp') {
                    $row[] = number_format($item['vector_s'] ?? 0, 6, '.', '');
                }

                $row[] = number_format($item['vi'] ?? 0, 6, '.', '');
                $row[] = round(($item['vi'] ?? 0) * 100, 2) . '%';
                $row[] = $item['status'] ?? '-';

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, $headers);
    }
}
