<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SPKController;
use Illuminate\Support\Facades\Route;

// Views
Route::get('/', [SPKController::class, 'dashboard'])->name('spk.dashboard');
Route::get('/dashboard', [SPKController::class, 'dashboard']);
Route::get('/kriteria', [SPKController::class, 'kriteria'])->name('spk.kriteria');
Route::get('/alternatif', [SPKController::class, 'alternatif'])->name('spk.alternatif');
Route::get('/keputusan', [SPKController::class, 'keputusan'])->name('spk.keputusan');

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
