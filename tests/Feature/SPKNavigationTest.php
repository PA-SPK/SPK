<?php

namespace Tests\Feature;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\KriteriaPerbandingan;
use App\Models\NilaiAlternatif;
use Database\Seeders\SPKDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SPKNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SPKDataSeeder::class);
    }

    /**
     * Clear all SPK data in correct dependency order.
     */
    private function clearAllData(): void
    {
        NilaiAlternatif::query()->delete();
        KriteriaPerbandingan::query()->delete();
        Alternatif::query()->delete();
        Kriteria::query()->delete();
    }

    /**
     * Test that the dashboard route is accessible and contains expected case study content.
     */
    public function test_dashboard_screen_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Perhitungan SPK');
        $response->assertSee('Total Alternatif');
    }

    /**
     * Test that the kriteria route is accessible and contains expected criteria content.
     */
    public function test_kriteria_screen_is_accessible(): void
    {
        $response = $this->get('/kriteria');
        $response->assertStatus(200);
        $response->assertSee('Kriteria & Matriks Bobot AHP', false);
        $response->assertSee('Jarak Faskes Terdekat');
    }

    /**
     * Test that the alternatif route is accessible and contains expected candidate locations.
     */
    public function test_alternatif_screen_is_accessible(): void
    {
        $response = $this->get('/alternatif');
        $response->assertStatus(200);
        $response->assertSee('Matriks Keputusan Awal Alternatif', false);
        $response->assertSee('Desa Tanjung Harapan');
        $response->assertSee('Desa Sumber Rejeki');
    }

    /**
     * Test that the keputusan route is accessible and contains expected winner and rankings.
     */
    public function test_keputusan_screen_is_accessible(): void
    {
        $response = $this->get('/keputusan');
        $response->assertStatus(200);
        $response->assertSee('Hasil Evaluasi & Rekomendasi Keputusan', false);
        $response->assertSee('Desa Tanjung Harapan');
        $response->assertSee('0.6412');
    }

    /**
     * Test that the import page is accessible.
     */
    public function test_import_page_is_accessible(): void
    {
        $response = $this->get('/import');
        $response->assertStatus(200);
        $response->assertSee('Import');
    }

    /**
     * Test that the settings page is accessible.
     */
    public function test_settings_page_is_accessible(): void
    {
        $response = $this->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Aplikasi');
        $response->assertSee('Muat Data Demo');
        $response->assertSee('PHP Version');
    }

    /**
     * Test that the dashboard shows the empty state when no data is seeded.
     */
    public function test_dashboard_shows_empty_state_with_no_data(): void
    {
        $this->clearAllData();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Belum Ada Data SPK');
        $response->assertSee('Import File Excel / CSV');
    }

    /**
     * Test that the settings page can reset data.
     */
    public function test_settings_can_reset_data(): void
    {
        $this->assertGreaterThan(0, Kriteria::count());

        $response = $this->post('/settings/reset');
        $response->assertRedirect('/settings');

        $this->assertEquals(0, Kriteria::count());
        $this->assertEquals(0, Alternatif::count());
    }

    /**
     * Test that the settings page can seed demo data.
     */
    public function test_settings_can_seed_demo_data(): void
    {
        $this->clearAllData();
        $this->assertEquals(0, Kriteria::count());

        $response = $this->post('/settings/seed');
        $response->assertRedirect('/settings');

        $this->assertGreaterThan(0, Kriteria::count());
        $this->assertGreaterThan(0, Alternatif::count());
    }
}
