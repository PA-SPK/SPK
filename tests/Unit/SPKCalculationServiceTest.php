<?php

namespace Tests\Unit;

use App\Services\SPKCalculationService;
use Database\Seeders\SPKDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SPKCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SPKDataSeeder::class);
    }

    /**
     * Test AHP calculation matches Excel values.
     */
    public function test_ahp_calculation_matches_case_study(): void
    {
        $service = new SPKCalculationService();
        $ahp = $service->calculateAHP();

        $this->assertEqualsWithDelta(0.188190, $ahp['weights']['K1'], 0.0001);
        $this->assertEqualsWithDelta(0.350091, $ahp['weights']['K2'], 0.0001);
        $this->assertEqualsWithDelta(0.204063, $ahp['weights']['K3'], 0.0001);
        $this->assertEqualsWithDelta(0.098917, $ahp['weights']['K4'], 0.0001);
        $this->assertEqualsWithDelta(0.059435, $ahp['weights']['K5'], 0.0001);
        $this->assertEqualsWithDelta(0.099303, $ahp['weights']['K6'], 0.0001);

        $this->assertEqualsWithDelta(0.009169, $ahp['cr'], 0.0001);
        $this->assertTrue($ahp['is_consistent']);
    }

    /**
     * Test TOPSIS ranking matches Excel values.
     */
    public function test_topsis_ranking_matches_case_study(): void
    {
        $service = new SPKCalculationService();
        $topsis = $service->calculateTOPSIS();

        $rankings = $topsis['ranked_results'];

        // Winner Rank 1 is L4 (Desa Tanjung Harapan)
        $this->assertEquals('L4', $rankings[0]['kode']);
        $this->assertEqualsWithDelta(0.641206, $rankings[0]['vi'], 0.0001);

        // Rank 2 is L7 (Desa Karang Anyar)
        $this->assertEquals('L7', $rankings[1]['kode']);
        $this->assertEqualsWithDelta(0.634697, $rankings[1]['vi'], 0.0001);

        // Rank 3 is L2 (Desa Karya Bakti)
        $this->assertEquals('L2', $rankings[2]['kode']);
        $this->assertEqualsWithDelta(0.546787, $rankings[2]['vi'], 0.0001);

        // Last is L6 (Kelurahan Suka Maju)
        $this->assertEquals('L6', $rankings[7]['kode']);
        $this->assertEqualsWithDelta(0.340961, $rankings[7]['vi'], 0.0001);
    }
}
