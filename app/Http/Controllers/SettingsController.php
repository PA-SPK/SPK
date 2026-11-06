<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\KriteriaPerbandingan;
use App\Models\NilaiAlternatif;
use Database\Seeders\SPKDataSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the application settings page.
     */
    public function index(): View
    {
        $stats = [
            'kriteria'    => Kriteria::count(),
            'alternatif'  => Alternatif::count(),
            'nilai'       => NilaiAlternatif::count(),
            'perbandingan' => KriteriaPerbandingan::count(),
        ];

        $systemInfo = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'env'             => app()->environment(),
        ];

        return view('spk.settings', compact('stats', 'systemInfo'));
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
     * Seed demo data (Puskesmas case study) into the database.
     */
    public function seedDemo(Request $request): RedirectResponse
    {
        // Clear existing data first to avoid duplicate key issues
        NilaiAlternatif::query()->delete();
        KriteriaPerbandingan::query()->delete();
        Alternatif::query()->delete();
        Kriteria::query()->delete();

        // Run the SPK data seeder
        $seeder = new SPKDataSeeder();
        $seeder->run();

        return redirect()->route('spk.settings')
            ->with('success', 'Data demo SPK berhasil dimuat! Terdapat ' . Kriteria::count() . ' kriteria dan ' . Alternatif::count() . ' alternatif.');
    }
}
