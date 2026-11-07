<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\KriteriaPerbandingan;
use App\Models\NilaiAlternatif;
use App\Services\SPKCalculationService;
use Database\Seeders\SPKDataSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        public SPKCalculationService $spkService
    ) {}

    /**
     * Display the application settings page.
     */
    public function index(): View
    {
        $stats = [
            'kriteria'     => Kriteria::count(),
            'alternatif'   => Alternatif::count(),
            'nilai'        => NilaiAlternatif::count(),
            'perbandingan' => KriteriaPerbandingan::count(),
        ];

        $activeMethod = $this->spkService->getActiveMethod();
        $methods      = $this->spkService->getMethodLabels();

        return view('spk.settings', compact('stats', 'activeMethod', 'methods'));
    }

    /**
     * Update active SPK evaluation method.
     */
    public function updateMethod(Request $request): RedirectResponse
    {
        $request->validate([
            'method' => 'required|in:topsis,saw,wp',
        ]);

        $selected = $request->input('method');
        $this->spkService->setActiveMethod($selected);
        $methods = $this->spkService->getMethodLabels();

        return redirect()->route('spk.settings')
            ->with('success', "Metode perankingan utama berhasil diubah menjadi: {$methods[$selected]['name']}!");
    }

    /**
     * Reset / kosongkan semua data SPK dari database.
     */
    public function reset(Request $request): RedirectResponse
    {
        NilaiAlternatif::query()->delete();
        KriteriaPerbandingan::query()->delete();
        Alternatif::query()->delete();
        Kriteria::query()->delete();

        return redirect()->route('spk.settings')
            ->with('success', 'Semua data SPK telah berhasil dikosongkan. Silakan import data baru atau tambah data secara manual.');
    }

    /**
     * Seed demo data into the database.
     */
    public function seedDemo(Request $request): RedirectResponse
    {
        NilaiAlternatif::query()->delete();
        KriteriaPerbandingan::query()->delete();
        Alternatif::query()->delete();
        Kriteria::query()->delete();

        $seeder = new SPKDataSeeder();
        $seeder->run();

        return redirect()->route('spk.settings')
            ->with('success', 'Data demo SPK berhasil dimuat! Terdapat ' . Kriteria::count() . ' kriteria dan ' . Alternatif::count() . ' alternatif.');
    }
}
