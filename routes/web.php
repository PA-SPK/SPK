<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SPKController;
use Illuminate\Support\Facades\Route;

// Views
Route::get('/', [SPKController::class, 'dashboard'])->name('spk.dashboard');
Route::get('/dashboard', [SPKController::class, 'dashboard']);
Route::get('/kriteria', [SPKController::class, 'kriteria'])->name('spk.kriteria');
Route::get('/alternatif', [SPKController::class, 'alternatif'])->name('spk.alternatif');
Route::get('/keputusan', [SPKController::class, 'keputusan'])->name('spk.keputusan');

// Import Excel / CSV (full dataset: Kriteria + Alternatif in one file)
Route::get('/import', [SPKController::class, 'importPage'])->name('spk.import');
Route::post('/import', [ImportController::class, 'import'])->name('spk.import.post');
Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('spk.import.template');

// Export Hasil Keputusan TOPSIS (CSV)
Route::get('/keputusan/export', [SPKController::class, 'exportKeputusanCsv'])->name('spk.keputusan.export');

// Export / Import Alternatif only (CSV)
Route::get('/alternatif/export', [AlternatifController::class, 'exportCsv'])->name('spk.alternatif.export');
Route::get('/alternatif/template', [AlternatifController::class, 'downloadTemplate'])->name('spk.alternatif.template');
Route::post('/alternatif/import', [AlternatifController::class, 'importCsv'])->name('spk.alternatif.import');

// Kriteria CRUD & Pairwise Comparison
Route::post('/kriteria', [KriteriaController::class, 'store'])->name('spk.kriteria.store');
Route::put('/kriteria/{kriteria}', [KriteriaController::class, 'update'])->name('spk.kriteria.update');
Route::delete('/kriteria/{kriteria}', [KriteriaController::class, 'destroy'])->name('spk.kriteria.destroy');
Route::post('/kriteria/perbandingan', [KriteriaController::class, 'updatePerbandingan'])->name('spk.kriteria.perbandingan');
Route::get('/kriteria/{any}', function () {
    return redirect()->route('spk.kriteria');
})->where('any', '.*');

// Alternatif CRUD & Matrix Values
Route::post('/alternatif', [AlternatifController::class, 'store'])->name('spk.alternatif.store');
Route::put('/alternatif/{alternatif}', [AlternatifController::class, 'update'])->name('spk.alternatif.update');
Route::delete('/alternatif/{alternatif}', [AlternatifController::class, 'destroy'])->name('spk.alternatif.destroy');
Route::post('/alternatif/nilai', [AlternatifController::class, 'updateNilai'])->name('spk.alternatif.nilai');
Route::get('/alternatif/{any}', function () {
    return redirect()->route('spk.alternatif');
})->where('any', '.*');

// Settings
Route::get('/settings', [SettingsController::class, 'index'])->name('spk.settings');
Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('spk.settings.reset');
Route::post('/settings/seed', [SettingsController::class, 'seedDemo'])->name('spk.settings.seed');
