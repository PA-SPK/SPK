<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use Database\Seeders\SPKDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SPKCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SPKDataSeeder::class);
    }

    /**
     * Test adding a new criterion and checking auto-creation of default alternative values.
     */
    public function test_can_create_new_kriteria(): void
    {
        $response = $this->post('/kriteria', [
            'kode' => 'K7',
            'nama' => 'Ketersediaan Air Bersih',
            'satuan' => 'Liter/Hari',
            'tipe' => 'Benefit',
            'urutan' => 7,
        ]);

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseHas('kriterias', ['kode' => 'K7']);

        $k7 = Kriteria::where('kode', 'K7')->first();
        $this->assertNotNull($k7);

        // Check that all 8 alternatives have a NilaiAlternatif for K7 with default 0
        $this->assertEquals(8, NilaiAlternatif::where('kriteria_id', $k7->id)->count());
    }

    /**
     * Test updating a criterion.
     */
    public function test_can_update_kriteria(): void
    {
        $k1 = Kriteria::where('kode', 'K1')->first();

        $response = $this->put("/kriteria/{$k1->id}", [
            'kode' => 'K1_EDIT',
            'nama' => 'Kepadatan Penduduk Terkoreksi',
            'satuan' => 'Jiwa/km²',
            'tipe' => 'Benefit',
            'urutan' => 1,
        ]);

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseHas('kriterias', [
            'id' => $k1->id,
            'kode' => 'K1_EDIT',
            'nama' => 'Kepadatan Penduduk Terkoreksi',
        ]);
    }

    /**
     * Test deleting a criterion.
     */
    public function test_can_delete_kriteria(): void
    {
        $k6 = Kriteria::where('kode', 'K6')->first();
        $k6Id = $k6->id;

        $response = $this->delete("/kriteria/{$k6Id}");

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseMissing('kriterias', ['id' => $k6Id]);
        $this->assertEquals(0, NilaiAlternatif::where('kriteria_id', $k6Id)->count());
    }

    /**
     * Test updating pairwise comparisons.
     */
    public function test_can_update_pairwise_comparison(): void
    {
        $k1 = Kriteria::where('kode', 'K1')->first();
        $k2 = Kriteria::where('kode', 'K2')->first();

        $response = $this->post('/kriteria/perbandingan', [
            'matrix' => [
                'K1' => ['K2' => 3.0],
            ],
        ]);

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseHas('kriteria_perbandingans', [
            'kriteria_1_id' => $k1->id,
            'kriteria_2_id' => $k2->id,
            'nilai' => 3.0,
        ]);
        $this->assertDatabaseHas('kriteria_perbandingans', [
            'kriteria_1_id' => $k2->id,
            'kriteria_2_id' => $k1->id,
            'nilai' => 1 / 3.0,
        ]);
    }

    /**
     * Test creating a new alternative and auto-creating score rows for all criteria.
     */
    public function test_can_create_new_alternatif(): void
    {
        $response = $this->post('/alternatif', [
            'kode' => 'L9',
            'nama' => 'Desa Makmur Jaya',
            'kategori' => 'Desa',
            'deskripsi' => 'Lokasi kandidat tambahan nomor 9',
            'urutan' => 9,
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('alternatifs', ['kode' => 'L9']);

        $l9 = Alternatif::where('kode', 'L9')->first();
        $this->assertEquals(Kriteria::count(), NilaiAlternatif::where('alternatif_id', $l9->id)->count());
    }

    /**
     * Test updating an alternative.
     */
    public function test_can_update_alternatif(): void
    {
        $l1 = Alternatif::where('kode', 'L1')->first();

        $response = $this->put("/alternatif/{$l1->id}", [
            'kode' => 'L1_EDIT',
            'nama' => 'Desa Sumber Rejeki Baru',
            'kategori' => 'Desa Maju',
            'deskripsi' => 'Keterangan diperbarui',
            'urutan' => 1,
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('alternatifs', [
            'id' => $l1->id,
            'kode' => 'L1_EDIT',
            'nama' => 'Desa Sumber Rejeki Baru',
        ]);
    }

    /**
     * Test deleting an alternative.
     */
    public function test_can_delete_alternatif(): void
    {
        $l8 = Alternatif::where('kode', 'L8')->first();
        $l8Id = $l8->id;

        $response = $this->delete("/alternatif/{$l8Id}");

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseMissing('alternatifs', ['id' => $l8Id]);
        $this->assertEquals(0, NilaiAlternatif::where('alternatif_id', $l8Id)->count());
    }

    /**
     * Test updating alternative evaluation scores in the decision matrix.
     */
    public function test_can_update_matrix_scores(): void
    {
        $l1 = Alternatif::where('kode', 'L1')->first();
        $k1 = Kriteria::where('kode', 'K1')->first();

        $response = $this->post('/alternatif/nilai', [
            'nilai' => [
                $l1->id => [
                    $k1->id => 999.5,
                ],
            ],
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('nilai_alternatifs', [
            'alternatif_id' => $l1->id,
            'kriteria_id' => $k1->id,
            'nilai' => 999.5,
        ]);
    }

    /**
     * Test exporting alternative dataset to CSV.
     */
    public function test_can_export_alternatif_csv(): void
    {
        $response = $this->get('/alternatif/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('alternatif_spk_', $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Kode', $content);
        $this->assertStringContainsString('Nama', $content);
        $this->assertStringContainsString('Desa Sumber Rejeki', $content);
    }

    /**
     * Test downloading CSV template.
     */
    public function test_can_download_template_csv(): void
    {
        $response = $this->get('/alternatif/template');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('template_import_alternatif_spk.csv', $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Kode', $content);
        $this->assertStringContainsString('ALT01', $content);
    }

    /**
     * Test importing alternative dataset via CSV.
     */
    public function test_can_import_alternatif_csv(): void
    {
        $csvContent = "Kode,Nama,Kategori,Deskripsi,K1,K2,K3,K4,K5,K6\n"
                    . "L10,Desa Sukamaju,Desa,Wilayah pegunungan,450,18,60,5,300,4\n"
                    . "L11,Kelurahan Indah,Kelurahan,Wilayah perkotaan,500,22,70,3,400,5\n";

        $file = UploadedFile::fake()->createWithContent('alternatifs.csv', $csvContent);

        $response = $this->post('/alternatif/import', [
            'file_csv' => $file,
        ]);

        $response->assertRedirect('/alternatif');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('alternatifs', ['kode' => 'L10', 'nama' => 'Desa Sukamaju']);
        $this->assertDatabaseHas('alternatifs', ['kode' => 'L11', 'nama' => 'Kelurahan Indah']);

        $l10 = Alternatif::where('kode', 'L10')->first();
        $k1 = Kriteria::where('kode', 'K1')->first();
        $this->assertDatabaseHas('nilai_alternatifs', [
            'alternatif_id' => $l10->id,
            'kriteria_id' => $k1->id,
            'nilai' => 450,
        ]);
    }

    /**
     * Test exporting final TOPSIS ranking results to CSV.
     */
    public function test_can_export_keputusan_csv(): void
    {
        $response = $this->get('/keputusan/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('hasil_keputusan_topsis_', $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Peringkat', $content);
        $this->assertStringContainsString('Kode Alternatif', $content);
        $this->assertStringContainsString('Desa Tanjung Harapan', $content);
    }

    /**
     * Test importing a full single-sheet study-case Excel file (Puskesmas case study format).
     */
    public function test_can_import_single_sheet_study_case_excel(): void
    {
        $testFilePath = storage_path('app/test_user.xlsx');
        if (!file_exists($testFilePath)) {
            $this->markTestSkipped('test_user.xlsx not available');
        }

        $file = new UploadedFile($testFilePath, 'studi_kasus_kelompok6.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->post('/import', [
            'file_spk' => $file,
            'mode'     => 'replace',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        // Check criteria were imported
        $this->assertEquals(6, Kriteria::count());
        $this->assertDatabaseHas('kriterias', ['kode' => 'K1', 'nama' => 'Kepadatan Penduduk', 'tipe' => 'Benefit']);
        $this->assertDatabaseHas('kriterias', ['kode' => 'K5', 'nama' => 'BIaya Lahan', 'tipe' => 'Cost']);

        // Check alternatives were imported
        $this->assertEquals(8, Alternatif::count());
        $this->assertDatabaseHas('alternatifs', ['kode' => 'L1', 'nama' => 'Desa Sumber Rejeki']);
        $this->assertDatabaseHas('alternatifs', ['kode' => 'L4', 'nama' => 'Desa Tanjung Harapan']);

        // Check values
        $l1 = Alternatif::where('kode', 'L1')->first();
        $k1 = Kriteria::where('kode', 'K1')->first();
        $this->assertDatabaseHas('nilai_alternatifs', [
            'alternatif_id' => $l1->id,
            'kriteria_id'   => $k1->id,
            'nilai'         => 850,
        ]);
    }

    /**
     * Test importing study case as a CSV file (without needing ZipArchive).
     */
    public function test_can_import_single_sheet_study_case_csv(): void
    {
        $csvContent = <<<CSV
"Kelompok 6",,,,,,,,,,,,
TAHAP 1 ,,,,,,,,,,,,
"Tabel Kriteria (Matriks Perbandingan Berpasangan)",,,,,,,,,,,"K1 Kepadatan Penduduk (Jiwa/km2)",Benefit
Kriteria ,K1,K2,K3,K4,K5,K6,,,,,"K2 Jarak Faskes Terdekat (Km)",Benefit
K1 Kepadatan Penduduk (Jiwa/km2),1,0.5,1,2,3,2,,,,,"K3 Kasus Penyakit (Kasus/Thn)",Benefit
K2 Jarak Faskes Terdekat (Km),2,1,2,3,5,4,,,,,"K4 Akses Jalan (Skala 1-5)",Benefit
K3 Kasus Penyakit (Kasus/Thn),1,0.5,1,3,3,2,,,,,"K5 BIaya Lahan (Rp Juta/100m2)",Cost
K4 Akses Jalan (Skala 1-5),0.5,0.33,0.33,1,2,1,,,,,"K6 Debit Air Bersih (Liter/detik)",Benefit
K5 BIaya Lahan (Rp Juta/100m2),0.33,0.2,0.33,0.5,1,0.5,,,,,,
K6 Debit Air Bersih (Liter/detik),0.5,0.25,0.5,1,2,1,,,,,,
TAHAP 2,,,,,,,,,,,,
"Tabel Matriks Keputusan (Alternatif)",,,,,,,,,,,,
Kode,Lokasi Usulan, K1,K2,K3,K4,K5,K6,,,,,
L1,Desa Sumber Rejeki,850,12,340,3,450,15,,,,,
L2,Desa Karya Bakti,620,18,280,2,300,8,,,,,
L3,Kelurahan Mekar Sari,1030,8,410,5,650,20,,,,,
L4,Desa Tanjung Harapan,540,25,210,2,250,2,,,,,
L5,Desa Sidomulyo,780,15,320,4,400,12,,,,,
L6,Kelurahan Suka Maju,950,5,380,5,700,25,,,,,
L7,Desa Karang Anyar,670,20,290,3,350,10,,,,,
L8,Desa Tirta Kencana,710,16,305,3,380,14,,,,,
CSV;

        $file = UploadedFile::fake()->createWithContent('studi_kasus.csv', $csvContent);

        $response = $this->post('/import', [
            'file_spk' => $file,
            'mode'     => 'replace',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertEquals(6, Kriteria::count());
        $this->assertEquals(8, Alternatif::count());
        $this->assertDatabaseHas('alternatifs', ['kode' => 'L4', 'nama' => 'Desa Tanjung Harapan']);
    }
}
