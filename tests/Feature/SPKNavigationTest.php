<?php

namespace Tests\Feature;

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
     * Test that the dashboard route is accessible and contains expected case study content.
     */
    public function test_dashboard_screen_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SPK Lokasi Pembangunan Puskesmas');
        $response->assertSee('Kelompok 6');
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
        $response->assertSee('Hasil Evaluasi & Rekomendasi Lokasi Puskesmas', false);
        $response->assertSee('Desa Tanjung Harapan');
        $response->assertSee('0.6412');
    }
}
