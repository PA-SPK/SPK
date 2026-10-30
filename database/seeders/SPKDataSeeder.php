<?php

namespace Database\Seeders;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\KriteriaPerbandingan;
use App\Models\NilaiAlternatif;
use Illuminate\Database\Seeder;

class SPKDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kriteria Data dari Studi Kasus Dinkes (Kelompok 6)
        $kriterias = [
            [
                'kode' => 'K1',
                'nama' => 'Kepadatan Penduduk',
                'satuan' => 'Jiwa/km²',
                'tipe' => 'Benefit',
                'bobot' => 0.1881902478,
                'urutan' => 1,
            ],
            [
                'kode' => 'K2',
                'nama' => 'Jarak Faskes Terdekat',
                'satuan' => 'Km',
                'tipe' => 'Benefit',
                'bobot' => 0.3500908130,
                'urutan' => 2,
            ],
            [
                'kode' => 'K3',
                'nama' => 'Kasus Penyakit',
                'satuan' => 'Kasus/Thn',
                'tipe' => 'Benefit',
                'bobot' => 0.2040632636,
                'urutan' => 3,
            ],
            [
                'kode' => 'K4',
                'nama' => 'Akses Jalan',
                'satuan' => 'Skala 1-5',
                'tipe' => 'Benefit',
                'bobot' => 0.09891713309,
                'urutan' => 4,
            ],
            [
                'kode' => 'K5',
                'nama' => 'Biaya Lahan',
                'satuan' => 'Rp Juta/100m²',
                'tipe' => 'Cost',
                'bobot' => 0.05943508528,
                'urutan' => 5,
            ],
            [
                'kode' => 'K6',
                'nama' => 'Debit Air Bersih',
                'satuan' => 'Liter/detik',
                'tipe' => 'Benefit',
                'bobot' => 0.09930345722,
                'urutan' => 6,
            ],
        ];

        $kriteriaModels = [];
        foreach ($kriterias as $k) {
            $kriteriaModels[$k['kode']] = Kriteria::updateOrCreate(
                ['kode' => $k['kode']],
                $k
            );
        }

        // 2. Matriks Perbandingan Berpasangan AHP Pakar
        $pairwiseMatrix = [
            'K1' => ['K1' => 1.0, 'K2' => 0.5, 'K3' => 1.0, 'K4' => 2.0, 'K5' => 3.0, 'K6' => 2.0],
            'K2' => ['K1' => 2.0, 'K2' => 1.0, 'K3' => 2.0, 'K4' => 3.0, 'K5' => 5.0, 'K6' => 4.0],
            'K3' => ['K1' => 1.0, 'K2' => 0.5, 'K3' => 1.0, 'K4' => 3.0, 'K5' => 3.0, 'K6' => 2.0],
            'K4' => ['K1' => 0.5, 'K2' => 1/3, 'K3' => 1/3, 'K4' => 1.0, 'K5' => 2.0, 'K6' => 1.0],
            'K5' => ['K1' => 1/3, 'K2' => 0.2, 'K3' => 1/3, 'K4' => 0.5, 'K5' => 1.0, 'K6' => 0.5],
            'K6' => ['K1' => 0.5, 'K2' => 0.25,'K3' => 0.5, 'K4' => 1.0, 'K5' => 2.0, 'K6' => 1.0],
        ];

        foreach ($pairwiseMatrix as $rKey => $cols) {
            foreach ($cols as $cKey => $val) {
                if (isset($kriteriaModels[$rKey], $kriteriaModels[$cKey])) {
                    KriteriaPerbandingan::updateOrCreate(
                        [
                            'kriteria_1_id' => $kriteriaModels[$rKey]->id,
                            'kriteria_2_id' => $kriteriaModels[$cKey]->id,
                        ],
                        [
                            'nilai' => $val,
                        ]
                    );
                }
            }
        }

        // 3. Alternatif Lokasi Pembangunan Puskesmas
        $alternatifs = [
            [
                'kode' => 'L1',
                'nama' => 'Desa Sumber Rejeki',
                'kategori' => 'Desa',
                'deskripsi' => 'Daerah permukiman berkembang dengan kepadatan sedang dan akses jalan memadai.',
                'urutan' => 1,
                'scores' => ['K1' => 850, 'K2' => 12, 'K3' => 340, 'K4' => 3, 'K5' => 450, 'K6' => 15],
            ],
            [
                'kode' => 'L2',
                'nama' => 'Desa Karya Bakti',
                'kategori' => 'Desa',
                'deskripsi' => 'Lokasi cukup jauh dari faskes lain (18 km) dengan biaya lahan terjangkau.',
                'urutan' => 2,
                'scores' => ['K1' => 620, 'K2' => 18, 'K3' => 280, 'K4' => 2, 'K5' => 300, 'K6' => 8],
            ],
            [
                'kode' => 'L3',
                'nama' => 'Kelurahan Mekar Sari',
                'kategori' => 'Kelurahan',
                'deskripsi' => 'Kepadatan tinggi dan kasus penyakit tinggi, namun dekat dengan faskes eksisting.',
                'urutan' => 3,
                'scores' => ['K1' => 1030, 'K2' => 8, 'K3' => 410, 'K4' => 5, 'K5' => 650, 'K6' => 20],
            ],
            [
                'kode' => 'L4',
                'nama' => 'Desa Tanjung Harapan',
                'kategori' => 'Desa',
                'deskripsi' => 'Sangat jauh dari faskes eksisting (25 km) dan biaya lahan paling ekonomis (250 Juta).',
                'urutan' => 4,
                'scores' => ['K1' => 540, 'K2' => 25, 'K3' => 210, 'K4' => 2, 'K5' => 250, 'K6' => 2],
            ],
            [
                'kode' => 'L5',
                'nama' => 'Desa Sidomulyo',
                'kategori' => 'Desa',
                'deskripsi' => 'Kondisi geografis stabil dengan akses jalan baik (skala 4) dan debit air 12 L/s.',
                'urutan' => 5,
                'scores' => ['K1' => 780, 'K2' => 15, 'K3' => 320, 'K4' => 4, 'K5' => 400, 'K6' => 12],
            ],
            [
                'kode' => 'L6',
                'nama' => 'Kelurahan Suka Maju',
                'kategori' => 'Kelurahan',
                'deskripsi' => 'Akses jalan & air sangat baik, namun sangat dekat dengan RS/Puskesmas lain (5 km).',
                'urutan' => 6,
                'scores' => ['K1' => 950, 'K2' => 5, 'K3' => 380, 'K4' => 5, 'K5' => 700, 'K6' => 25],
            ],
            [
                'kode' => 'L7',
                'nama' => 'Desa Karang Anyar',
                'kategori' => 'Desa',
                'deskripsi' => 'Jarak faskes 20 km dengan kasus penyakit 290 dan biaya lahan moderat (350 Juta).',
                'urutan' => 7,
                'scores' => ['K1' => 670, 'K2' => 20, 'K3' => 290, 'K4' => 3, 'K5' => 350, 'K6' => 10],
            ],
            [
                'kode' => 'L8',
                'nama' => 'Desa Tirta Kencana',
                'kategori' => 'Desa',
                'deskripsi' => 'Jarak faskes 16 km dengan debit air bersih 14 L/s dan kepadatan penduduk 710.',
                'urutan' => 8,
                'scores' => ['K1' => 710, 'K2' => 16, 'K3' => 305, 'K4' => 3, 'K5' => 380, 'K6' => 14],
            ],
        ];

        foreach ($alternatifs as $altData) {
            $scores = $altData['scores'];
            unset($altData['scores']);

            $alt = Alternatif::updateOrCreate(
                ['kode' => $altData['kode']],
                $altData
            );

            foreach ($scores as $kKode => $val) {
                if (isset($kriteriaModels[$kKode])) {
                    NilaiAlternatif::updateOrCreate(
                        [
                            'alternatif_id' => $alt->id,
                            'kriteria_id' => $kriteriaModels[$kKode]->id,
                        ],
                        [
                            'nilai' => $val,
                        ]
                    );
                }
            }
        }
    }
}
