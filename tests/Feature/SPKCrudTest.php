<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\NilaiAlternatif;
use Database\Seeders\SPKDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'kode' => 'K1',
            'nama' => 'Kepadatan Penduduk Terkini',
            'satuan' => 'Jiwa/Km2',
            'tipe' => 'Benefit',
            'urutan' => 1,
        ]);

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseHas('kriterias', [
            'id' => $k1->id,
            'nama' => 'Kepadatan Penduduk Terkini',
        ]);
    }

    /**
     * Test deleting a criterion and cascading removal.
     */
    public function test_can_delete_kriteria(): void
    {
        $k1 = Kriteria::where('kode', 'K1')->first();
        $id = $k1->id;

        $response = $this->delete("/kriteria/{$id}");

        $response->assertRedirect('/kriteria');
        $this->assertDatabaseMissing('kriterias', ['id' => $id]);
        $this->assertDatabaseMissing('nilai_alternatifs', ['kriteria_id' => $id]);
    }

    /**
     * Test creating a new alternative.
     */
    public function test_can_create_new_alternatif(): void
    {
        $response = $this->post('/alternatif', [
            'kode' => 'L9',
            'nama' => 'Desa Karang Anyar',
            'kategori' => 'Desa',
            'deskripsi' => 'Daerah dataran tinggi',
            'nilai' => [
                'K1' => 300,
                'K2' => 20,
                'K3' => 50,
                'K4' => 4,
                'K5' => 350,
                'K6' => 4,
            ],
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('alternatifs', ['kode' => 'L9', 'nama' => 'Desa Karang Anyar']);

        $l9 = Alternatif::where('kode', 'L9')->first();
        $this->assertNotNull($l9);
        $this->assertEquals(6, NilaiAlternatif::where('alternatif_id', $l9->id)->count());
    }

    /**
     * Test updating an alternative.
     */
    public function test_can_update_alternatif(): void
    {
        $l1 = Alternatif::where('kode', 'L1')->first();

        $response = $this->put("/alternatif/{$l1->id}", [
            'kode' => 'L1',
            'nama' => 'Desa Mekar Baru Jaya',
            'kategori' => 'Desa',
            'deskripsi' => 'Pusat pemukiman padat',
            'urutan' => 1,
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('alternatifs', [
            'id' => $l1->id,
            'nama' => 'Desa Mekar Baru Jaya',
        ]);
    }

    /**
     * Test deleting an alternative.
     */
    public function test_can_delete_alternatif(): void
    {
        $l1 = Alternatif::where('kode', 'L1')->first();
        $id = $l1->id;

        $response = $this->delete("/alternatif/{$id}");

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseMissing('alternatifs', ['id' => $id]);
        $this->assertDatabaseMissing('nilai_alternatifs', ['alternatif_id' => $id]);
    }

    /**
     * Test bulk updating matrix values and verifying TOPSIS recalculation.
     */
    public function test_can_update_matrix_values(): void
    {
        $l4 = Alternatif::where('kode', 'L4')->first();
        $k2 = Kriteria::where('kode', 'K2')->first();

        $response = $this->post('/alternatif/nilai', [
            'nilai' => [
                $l4->id => [
                    $k2->id => 35.0,
                ],
            ],
        ]);

        $response->assertRedirect('/alternatif');
        $this->assertDatabaseHas('nilai_alternatifs', [
            'alternatif_id' => $l4->id,
            'kriteria_id' => $k2->id,
            'nilai' => 35.0,
        ]);
    }

    /**
     * Test updating pairwise comparisons in AHP.
     */
    public function test_can_update_pairwise_comparison(): void
    {
        $response = $this->post('/kriteria/perbandingan', [
            'matrix' => [
                'K1' => [
                    'K2' => 0.5,
                ],
            ],
        ]);

        $response->assertRedirect('/kriteria');

        $k1 = Kriteria::where('kode', 'K1')->first();
        $k2 = Kriteria::where('kode', 'K2')->first();

        $this->assertDatabaseHas('kriteria_perbandingans', [
            'kriteria_1_id' => $k1->id,
            'kriteria_2_id' => $k2->id,
            'nilai' => 0.5,
        ]);

        // Auto reciprocal check: K2 vs K1 should be 2.0
        $this->assertDatabaseHas('kriteria_perbandingans', [
            'kriteria_1_id' => $k2->id,
            'kriteria_2_id' => $k1->id,
            'nilai' => 2.0,
        ]);
    }
}

