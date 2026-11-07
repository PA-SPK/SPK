<?php

namespace App\Services;

use App\Models\Alternatif;
use App\Models\AppSetting;
use App\Models\Kriteria;
use App\Models\KriteriaPerbandingan;

class SPKCalculationService
{
    /**
     * Random Index (RI) table for AHP consistency calculation up to n=15.
     *
     * @var array<int, float>
     */
    protected array $riTable = [
        1 => 0.0,
        2 => 0.0,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49,
        11 => 1.51,
        12 => 1.54,
        13 => 1.56,
        14 => 1.57,
        15 => 1.59,
    ];

    /**
     * Get active ranking method ('topsis', 'saw', 'wp').
     */
    public function getActiveMethod(): string
    {
        return AppSetting::getValue('active_spk_method', 'topsis');
    }

    /**
     * Set active ranking method.
     */
    public function setActiveMethod(string $method): void
    {
        $allowed = ['topsis', 'saw', 'wp'];
        $clean = in_array(strtolower($method), $allowed, true) ? strtolower($method) : 'topsis';
        AppSetting::setValue('active_spk_method', $clean);
    }

    /**
     * List of supported evaluation methods and metadata.
     *
     * @return array<string, array{name: string, short: string, desc: string, icon: string}>
     */
    public function getMethodLabels(): array
    {
        return [
            'topsis' => [
                'name'  => 'TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)',
                'short' => 'AHP + TOPSIS',
                'desc'  => 'Menentukan alternatif terbaik berdasarkan jarak geometris terdekat dengan solusi ideal positif (A+) dan terjauh dari solusi ideal negatif (A-).',
                'icon'  => 'military_tech',
            ],
            'saw' => [
                'name'  => 'SAW (Simple Additive Weighting)',
                'short' => 'AHP + SAW',
                'desc'  => 'Menghitung nilai preferensi dengan penjumlahan terbobot dari nilai kinerja setiap alternatif pada seluruh atribut kriteria yang telah dinormalisasi linier.',
                'icon'  => 'calculate',
            ],
            'wp' => [
                'name'  => 'WP (Weighted Product)',
                'short' => 'AHP + WP',
                'desc'  => 'Menggunakan perkalian untuk menghubungkan rating atribut, di mana rating tiap atribut harus dipangkatkan terlebih dahulu dengan bobot kriteria yang bersangkutan.',
                'icon'  => 'functions',
            ],
        ];
    }

    /**
     * Calculate AHP Pairwise Comparison Matrix, Weights, and Consistency Ratio dynamically from DB.
     *
     * @param bool $persistWeights Whether to update criteria weights in database.
     * @return array<string, mixed>
     */
    public function calculateAHP(bool $persistWeights = false): array
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        $n = $kriterias->count();

        if ($n === 0) {
            return [
                'kriterias' => collect(),
                'pairwise_matrix' => [],
                'col_sums' => [],
                'normalized_matrix' => [],
                'weights' => [],
                'aw' => [],
                'consistency_vector' => [],
                'lambda_max' => 0.0,
                'ci' => 0.0,
                'ri' => 0.0,
                'cr' => 0.0,
                'is_consistent' => true,
            ];
        }

        $allPerbandingan = KriteriaPerbandingan::all();
        $perbandinganMap = [];
        foreach ($allPerbandingan as $p) {
            $perbandinganMap[$p->kriteria_1_id][$p->kriteria_2_id] = (float) $p->nilai;
        }

        $pairwiseMatrix = [];
        foreach ($kriterias as $k1) {
            foreach ($kriterias as $k2) {
                if ($k1->id === $k2->id) {
                    $val = 1.0;
                } elseif (isset($perbandinganMap[$k1->id][$k2->id])) {
                    $val = $perbandinganMap[$k1->id][$k2->id];
                } elseif (isset($perbandinganMap[$k2->id][$k1->id]) && $perbandinganMap[$k2->id][$k1->id] > 0) {
                    $val = 1.0 / $perbandinganMap[$k2->id][$k1->id];
                } else {
                    $val = 1.0;
                }
                $pairwiseMatrix[$k1->kode][$k2->kode] = $val;
            }
        }

        $colSums = [];
        foreach ($kriterias as $k2) {
            $sum = 0;
            foreach ($kriterias as $k1) {
                $sum += $pairwiseMatrix[$k1->kode][$k2->kode];
            }
            $colSums[$k2->kode] = $sum;
        }

        $normalizedMatrix = [];
        $weights = [];
        foreach ($kriterias as $k1) {
            $rowSum = 0;
            foreach ($kriterias as $k2) {
                $normVal = $colSums[$k2->kode] > 0 ? $pairwiseMatrix[$k1->kode][$k2->kode] / $colSums[$k2->kode] : 0;
                $normalizedMatrix[$k1->kode][$k2->kode] = $normVal;
                $rowSum += $normVal;
            }
            $weights[$k1->kode] = $n > 0 ? $rowSum / $n : 0;
        }

        if ($persistWeights) {
            foreach ($kriterias as $k) {
                $k->bobot = $weights[$k->kode] ?? 0;
                $k->save();
            }
        }

        $aw = [];
        $consistencyVector = [];

        if ($n > 1) {
            foreach ($kriterias as $k1) {
                $sum = 0;
                foreach ($kriterias as $k2) {
                    $sum += $pairwiseMatrix[$k1->kode][$k2->kode] * $weights[$k2->kode];
                }
                $aw[$k1->kode] = $sum;
                $consistencyVector[$k1->kode] = $weights[$k1->kode] > 0 ? $sum / $weights[$k1->kode] : 0;
            }

            $lambdaMax = array_sum($consistencyVector) / $n;
            $ci = ($lambdaMax - $n) / ($n - 1);
            $ri = $this->riTable[$n] ?? 1.59;
            $cr = $ri > 0 ? $ci / $ri : 0;
            $isConsistent = $cr <= 0.10;
        } else {
            $lambdaMax = 1.0;
            $ci = 0.0;
            $ri = 0.0;
            $cr = 0.0;
            $isConsistent = true;
        }

        return [
            'kriterias' => $kriterias,
            'pairwise_matrix' => $pairwiseMatrix,
            'col_sums' => $colSums,
            'normalized_matrix' => $normalizedMatrix,
            'weights' => $weights,
            'aw' => $aw,
            'consistency_vector' => $consistencyVector,
            'lambda_max' => $lambdaMax,
            'ci' => $ci,
            'ri' => $ri,
            'cr' => $cr,
            'is_consistent' => $isConsistent,
        ];
    }

    /**
     * Run full dynamic TOPSIS evaluation using weights from AHP.
     *
     * @return array<string, mixed>
     */
    public function calculateTOPSIS(): array
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        $alternatifs = Alternatif::with('nilaiAlternatifs.kriteria')->orderBy('urutan')->get();

        $ahp = $this->calculateAHP();
        $weights = $ahp['weights'];

        if ($kriterias->isEmpty() || $alternatifs->isEmpty()) {
            return [
                'method' => 'topsis',
                'method_label' => 'AHP + TOPSIS',
                'kriterias' => $kriterias,
                'alternatifs' => $alternatifs,
                'ahp' => $ahp,
                'matrix_x' => [],
                'divisors' => [],
                'matrix_r' => [],
                'matrix_y' => [],
                'ideal_positive' => [],
                'ideal_negative' => [],
                'distance_positive' => [],
                'distance_negative' => [],
                'preference_vi' => [],
                'ranked_results' => [],
            ];
        }

        // 1. Matriks Keputusan Awal (X)
        $matrixX = [];
        foreach ($alternatifs as $alt) {
            $scores = [];
            foreach ($kriterias as $k) {
                $na = $alt->nilaiAlternatifs->firstWhere('kriteria_id', $k->id);
                $scores[$k->kode] = $na ? (float) $na->nilai : 0.0;
            }
            $matrixX[$alt->kode] = [
                'alternatif' => $alt,
                'scores' => $scores,
            ];
        }

        // 2. Pembagi Vektor: sqrt(sum(x_ij^2))
        $divisors = [];
        foreach ($kriterias as $k) {
            $sumSquares = 0;
            foreach ($matrixX as $row) {
                $val = $row['scores'][$k->kode] ?? 0;
                $sumSquares += pow($val, 2);
            }
            $divisors[$k->kode] = sqrt($sumSquares);
        }

        // 3. Matriks Ternormalisasi (R): r_ij = x_ij / divisor
        $matrixR = [];
        foreach ($matrixX as $altKode => $row) {
            foreach ($kriterias as $k) {
                $matrixR[$altKode][$k->kode] = ($divisors[$k->kode] ?? 0) > 0
                    ? $row['scores'][$k->kode] / $divisors[$k->kode]
                    : 0.0;
            }
        }

        // 4. Matriks Ternormalisasi Terbobot (Y): y_ij = w_j * r_ij
        $matrixY = [];
        foreach ($matrixR as $altKode => $row) {
            foreach ($kriterias as $k) {
                $w = $weights[$k->kode] ?? (float) $k->bobot;
                $matrixY[$altKode][$k->kode] = $row[$k->kode] * $w;
            }
        }

        // 5. Solusi Ideal Positif (A+) dan Solusi Ideal Negatif (A-)
        $idealPositive = [];
        $idealNegative = [];
        foreach ($kriterias as $k) {
            $colValues = [];
            foreach ($matrixY as $row) {
                $colValues[] = $row[$k->kode] ?? 0.0;
            }

            if (empty($colValues)) {
                $idealPositive[$k->kode] = 0.0;
                $idealNegative[$k->kode] = 0.0;
                continue;
            }

            if ($k->tipe === 'Benefit') {
                $idealPositive[$k->kode] = max($colValues);
                $idealNegative[$k->kode] = min($colValues);
            } else {
                $idealPositive[$k->kode] = min($colValues);
                $idealNegative[$k->kode] = max($colValues);
            }
        }

        // 6. Jarak Euclidean ke Solusi Ideal Positif (D+) dan Negatif (D-)
        $distancePositive = [];
        $distanceNegative = [];
        $preferenceVi = [];
        $rankedResults = [];

        foreach ($matrixY as $altKode => $row) {
            $sumDPlus = 0;
            $sumDMinus = 0;
            foreach ($kriterias as $k) {
                $val = $row[$k->kode] ?? 0.0;
                $sumDPlus += pow($val - ($idealPositive[$k->kode] ?? 0.0), 2);
                $sumDMinus += pow($val - ($idealNegative[$k->kode] ?? 0.0), 2);
            }
            $dPlus = sqrt($sumDPlus);
            $dMinus = sqrt($sumDMinus);
            $vi = ($dPlus + $dMinus) > 0 ? $dMinus / ($dPlus + $dMinus) : 0;

            $distancePositive[$altKode] = $dPlus;
            $distanceNegative[$altKode] = $dMinus;
            $preferenceVi[$altKode] = $vi;

            $rankedResults[] = [
                'kode' => $altKode,
                'alternatif' => $matrixX[$altKode]['alternatif'],
                'd_plus' => $dPlus,
                'd_minus' => $dMinus,
                'vi' => $vi,
                'kelayakan' => round($vi * 100, 2) . '%',
            ];
        }

        usort($rankedResults, fn($a, $b) => $b['vi'] <=> $a['vi']);

        foreach ($rankedResults as $idx => &$item) {
            $item['rank'] = $idx + 1;
            if ($item['rank'] === 1) {
                $item['status'] = 'Rekomendasi Utama';
                $item['badge'] = 'primary';
            } elseif ($item['rank'] <= 3) {
                $item['status'] = 'Lolos Kuota';
                $item['badge'] = 'secondary';
            } else {
                $item['status'] = 'Cadangan';
                $item['badge'] = 'tertiary';
            }
        }

        return [
            'method' => 'topsis',
            'method_label' => 'AHP + TOPSIS',
            'kriterias' => $kriterias,
            'alternatifs' => $alternatifs,
            'ahp' => $ahp,
            'matrix_x' => $matrixX,
            'divisors' => $divisors,
            'matrix_r' => $matrixR,
            'matrix_y' => $matrixY,
            'ideal_positive' => $idealPositive,
            'ideal_negative' => $idealNegative,
            'distance_positive' => $distancePositive,
            'distance_negative' => $distanceNegative,
            'preference_vi' => $preferenceVi,
            'ranked_results' => $rankedResults,
        ];
    }

    /**
     * Run SAW (Simple Additive Weighting) calculation.
     *
     * @return array<string, mixed>
     */
    public function calculateSAW(): array
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        $alternatifs = Alternatif::with('nilaiAlternatifs.kriteria')->orderBy('urutan')->get();

        $ahp = $this->calculateAHP();
        $weights = $ahp['weights'];

        if ($kriterias->isEmpty() || $alternatifs->isEmpty()) {
            return [
                'method' => 'saw',
                'method_label' => 'AHP + SAW',
                'kriterias' => $kriterias,
                'alternatifs' => $alternatifs,
                'ahp' => $ahp,
                'matrix_x' => [],
                'col_max' => [],
                'col_min' => [],
                'matrix_r' => [],
                'preference_vi' => [],
                'ranked_results' => [],
            ];
        }

        // 1. Matriks Keputusan Awal (X)
        $matrixX = [];
        foreach ($alternatifs as $alt) {
            $scores = [];
            foreach ($kriterias as $k) {
                $na = $alt->nilaiAlternatifs->firstWhere('kriteria_id', $k->id);
                $scores[$k->kode] = $na ? (float) $na->nilai : 0.0;
            }
            $matrixX[$alt->kode] = [
                'alternatif' => $alt,
                'scores' => $scores,
            ];
        }

        // 2. Cari Max & Min per kolom kriteria
        $colMax = [];
        $colMin = [];
        foreach ($kriterias as $k) {
            $vals = [];
            foreach ($matrixX as $row) {
                $vals[] = $row['scores'][$k->kode] ?? 0.0;
            }
            $colMax[$k->kode] = !empty($vals) ? max($vals) : 0.0;
            $colMin[$k->kode] = !empty($vals) ? min($vals) : 0.0;
        }

        // 3. Normalisasi Matriks R (SAW)
        // Benefit: r_ij = x_ij / max(x_j)
        // Cost:    r_ij = min(x_j) / x_ij
        $matrixR = [];
        foreach ($matrixX as $altKode => $row) {
            foreach ($kriterias as $k) {
                $x = $row['scores'][$k->kode] ?? 0.0;
                if ($k->tipe === 'Benefit') {
                    $matrixR[$altKode][$k->kode] = ($colMax[$k->kode] > 0) ? ($x / $colMax[$k->kode]) : 0.0;
                } else {
                    $matrixR[$altKode][$k->kode] = ($x > 0) ? ($colMin[$k->kode] / $x) : 0.0;
                }
            }
        }

        // 4. Hitung Nilai Preferensi Vi = sum(w_j * r_ij)
        $preferenceVi = [];
        $rankedResults = [];

        foreach ($matrixR as $altKode => $row) {
            $vi = 0.0;
            foreach ($kriterias as $k) {
                $w = $weights[$k->kode] ?? (float) $k->bobot;
                $vi += ($w * ($row[$k->kode] ?? 0.0));
            }
            $preferenceVi[$altKode] = $vi;

            $rankedResults[] = [
                'kode' => $altKode,
                'alternatif' => $matrixX[$altKode]['alternatif'],
                'vi' => $vi,
                'kelayakan' => round($vi * 100, 2) . '%',
            ];
        }

        usort($rankedResults, fn($a, $b) => $b['vi'] <=> $a['vi']);

        foreach ($rankedResults as $idx => &$item) {
            $item['rank'] = $idx + 1;
            if ($item['rank'] === 1) {
                $item['status'] = 'Rekomendasi Utama';
                $item['badge'] = 'primary';
            } elseif ($item['rank'] <= 3) {
                $item['status'] = 'Lolos Kuota';
                $item['badge'] = 'secondary';
            } else {
                $item['status'] = 'Cadangan';
                $item['badge'] = 'tertiary';
            }
        }

        return [
            'method' => 'saw',
            'method_label' => 'AHP + SAW',
            'kriterias' => $kriterias,
            'alternatifs' => $alternatifs,
            'ahp' => $ahp,
            'matrix_x' => $matrixX,
            'col_max' => $colMax,
            'col_min' => $colMin,
            'matrix_r' => $matrixR,
            'preference_vi' => $preferenceVi,
            'ranked_results' => $rankedResults,
        ];
    }

    /**
     * Run WP (Weighted Product) calculation.
     *
     * @return array<string, mixed>
     */
    public function calculateWP(): array
    {
        $kriterias = Kriteria::orderBy('urutan')->get();
        $alternatifs = Alternatif::with('nilaiAlternatifs.kriteria')->orderBy('urutan')->get();

        $ahp = $this->calculateAHP();
        $weights = $ahp['weights'];

        if ($kriterias->isEmpty() || $alternatifs->isEmpty()) {
            return [
                'method' => 'wp',
                'method_label' => 'AHP + WP',
                'kriterias' => $kriterias,
                'alternatifs' => $alternatifs,
                'ahp' => $ahp,
                'matrix_x' => [],
                'norm_weights' => [],
                'vector_s' => [],
                'total_s' => 0.0,
                'preference_vi' => [],
                'ranked_results' => [],
            ];
        }

        // 1. Matriks Keputusan Awal (X)
        $matrixX = [];
        foreach ($alternatifs as $alt) {
            $scores = [];
            foreach ($kriterias as $k) {
                $na = $alt->nilaiAlternatifs->firstWhere('kriteria_id', $k->id);
                $scores[$k->kode] = $na ? (float) $na->nilai : 0.0;
            }
            $matrixX[$alt->kode] = [
                'alternatif' => $alt,
                'scores' => $scores,
            ];
        }

        // 2. Normalisasi Bobot: w_j / sum(w)
        $sumWeights = array_sum($weights);
        $normWeights = [];
        foreach ($kriterias as $k) {
            $w = $weights[$k->kode] ?? (float) $k->bobot;
            $normWeights[$k->kode] = $sumWeights > 0 ? $w / $sumWeights : 0.0;
        }

        // 3. Hitung Vektor S_i = product(x_ij ^ power)
        // Benefit: power = +w_j, Cost: power = -w_j
        $vectorS = [];
        foreach ($matrixX as $altKode => $row) {
            $s = 1.0;
            foreach ($kriterias as $k) {
                $x = $row['scores'][$k->kode] ?? 0.0;
                $w = $normWeights[$k->kode] ?? 0.0;
                $power = ($k->tipe === 'Cost') ? -$w : $w;
                $base = ($x > 0) ? $x : 0.0001;
                $s *= pow($base, $power);
            }
            $vectorS[$altKode] = $s;
        }

        // 4. Hitung Vektor V_i = S_i / sum(S)
        $totalS = array_sum($vectorS);
        $preferenceVi = [];
        $rankedResults = [];

        foreach ($vectorS as $altKode => $s) {
            $vi = $totalS > 0 ? ($s / $totalS) : 0.0;
            $preferenceVi[$altKode] = $vi;

            $rankedResults[] = [
                'kode' => $altKode,
                'alternatif' => $matrixX[$altKode]['alternatif'],
                'vector_s' => $s,
                'vi' => $vi,
                'kelayakan' => round($vi * 100, 2) . '%',
            ];
        }

        usort($rankedResults, fn($a, $b) => $b['vi'] <=> $a['vi']);

        foreach ($rankedResults as $idx => &$item) {
            $item['rank'] = $idx + 1;
            if ($item['rank'] === 1) {
                $item['status'] = 'Rekomendasi Utama';
                $item['badge'] = 'primary';
            } elseif ($item['rank'] <= 3) {
                $item['status'] = 'Lolos Kuota';
                $item['badge'] = 'secondary';
            } else {
                $item['status'] = 'Cadangan';
                $item['badge'] = 'tertiary';
            }
        }

        return [
            'method' => 'wp',
            'method_label' => 'AHP + WP',
            'kriterias' => $kriterias,
            'alternatifs' => $alternatifs,
            'ahp' => $ahp,
            'matrix_x' => $matrixX,
            'norm_weights' => $normWeights,
            'vector_s' => $vectorS,
            'total_s' => $totalS,
            'preference_vi' => $preferenceVi,
            'ranked_results' => $rankedResults,
        ];
    }

    /**
     * Run calculation based on given or active method.
     */
    public function calculateActiveMethod(?string $method = null): array
    {
        $m = strtolower($method ?: $this->getActiveMethod());
        return match ($m) {
            'saw'   => $this->calculateSAW(),
            'wp'    => $this->calculateWP(),
            default => $this->calculateTOPSIS(),
        };
    }

    /**
     * Compare rankings across all 3 methods (TOPSIS, SAW, WP).
     *
     * @return array<string, mixed>
     */
    public function calculateComparison(): array
    {
        $topsis = $this->calculateTOPSIS();
        $saw    = $this->calculateSAW();
        $wp     = $this->calculateWP();

        $topsisMap = collect($topsis['ranked_results'])->keyBy('kode');
        $sawMap    = collect($saw['ranked_results'])->keyBy('kode');
        $wpMap     = collect($wp['ranked_results'])->keyBy('kode');

        $alternatifs = Alternatif::orderBy('urutan')->get();
        $comparison = [];

        foreach ($alternatifs as $alt) {
            $tRank = $topsisMap[$alt->kode]['rank'] ?? 0;
            $sRank = $sawMap[$alt->kode]['rank'] ?? 0;
            $wRank = $wpMap[$alt->kode]['rank'] ?? 0;

            $avgRank = ($tRank + $sRank + $wRank) / 3;

            $comparison[] = [
                'kode'        => $alt->kode,
                'nama'        => $alt->nama,
                'alternatif'  => $alt,
                'topsis_rank' => $tRank,
                'topsis_vi'   => $topsisMap[$alt->kode]['vi'] ?? 0,
                'saw_rank'    => $sRank,
                'saw_vi'      => $sawMap[$alt->kode]['vi'] ?? 0,
                'wp_rank'     => $wRank,
                'wp_vi'       => $wpMap[$alt->kode]['vi'] ?? 0,
                'avg_rank'    => $avgRank,
                'is_consistent' => ($tRank === $sRank && $sRank === $wRank),
            ];
        }

        usort($comparison, fn($a, $b) => $a['avg_rank'] <=> $b['avg_rank']);

        return [
            'topsis'     => $topsis,
            'saw'        => $saw,
            'wp'         => $wp,
            'comparison' => $comparison,
        ];
    }
}
