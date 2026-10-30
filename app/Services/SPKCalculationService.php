<?php

namespace App\Services;

use App\Models\Alternatif;
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
                'consistency_vector' => [],
                'lambda_max' => 0,
                'ci' => 0,
                'ri' => 0,
                'cr' => 0,
                'is_consistent' => true,
            ];
        }

        // Fetch existing comparisons
        $comparisons = KriteriaPerbandingan::all()->keyBy(function ($item) {
            return $item->kriteria_1_id . '_' . $item->kriteria_2_id;
        });

        // 1. Build dynamic pairwise matrix
        $pairwiseMatrix = [];
        foreach ($kriterias as $k1) {
            foreach ($kriterias as $k2) {
                if ($k1->id === $k2->id) {
                    $pairwiseMatrix[$k1->kode][$k2->kode] = 1.0;
                } else {
                    $key1 = $k1->id . '_' . $k2->id;
                    $key2 = $k2->id . '_' . $k1->id;

                    if ($comparisons->has($key1)) {
                        $pairwiseMatrix[$k1->kode][$k2->kode] = (float) $comparisons->get($key1)->nilai;
                    } elseif ($comparisons->has($key2)) {
                        $val = (float) $comparisons->get($key2)->nilai;
                        $pairwiseMatrix[$k1->kode][$k2->kode] = $val > 0 ? 1 / $val : 1.0;
                    } else {
                        $pairwiseMatrix[$k1->kode][$k2->kode] = 1.0;
                    }
                }
            }
        }

        // 2. Jumlah per kolom (column sums)
        $colSums = [];
        foreach ($kriterias as $kCol) {
            $sum = 0;
            foreach ($kriterias as $kRow) {
                $sum += $pairwiseMatrix[$kRow->kode][$kCol->kode] ?? 1.0;
            }
            $colSums[$kCol->kode] = $sum;
        }

        // 3. Normalisasi Matriks AHP & Hitung Rata-rata Baris (Bobot Prioritas w)
        $normalizedMatrix = [];
        $weights = [];
        foreach ($kriterias as $kRow) {
            $rowSum = 0;
            foreach ($kriterias as $kCol) {
                $rawVal = $pairwiseMatrix[$kRow->kode][$kCol->kode] ?? 1.0;
                $colSum = $colSums[$kCol->kode] > 0 ? $colSums[$kCol->kode] : 1.0;
                $normVal = $rawVal / $colSum;
                $normalizedMatrix[$kRow->kode][$kCol->kode] = $normVal;
                $rowSum += $normVal;
            }
            $weightVal = $n > 0 ? $rowSum / $n : 0;
            $weights[$kRow->kode] = $weightVal;

            if ($persistWeights) {
                $kRow->update(['bobot' => $weightVal]);
            }
        }

        // 4. Uji Konsistensi (A * w, lambda_max, CI, CR)
        $aw = [];
        $consistencyVector = [];
        if ($n > 1) {
            foreach ($kriterias as $kRow) {
                $sum = 0;
                foreach ($kriterias as $kCol) {
                    $sum += ($pairwiseMatrix[$kRow->kode][$kCol->kode] ?? 1.0) * ($weights[$kCol->kode] ?? 0);
                }
                $aw[$kRow->kode] = $sum;
                $wVal = $weights[$kRow->kode] ?? 0;
                $consistencyVector[$kRow->kode] = $wVal > 0 ? $sum / $wVal : 0;
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
                // Cost: Positif adalah min, Negatif adalah max
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

        // Urutkan dari Vi tertinggi (Ranking 1)
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
}
