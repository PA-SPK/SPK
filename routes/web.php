<?php

use App\Http\Controllers\SPKController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SPKController::class, 'dashboard'])->name('spk.dashboard');
Route::get('/dashboard', [SPKController::class, 'dashboard']);

Route::get('/kriteria', [SPKController::class, 'kriteria'])->name('spk.kriteria');
Route::get('/alternatif', [SPKController::class, 'alternatif'])->name('spk.alternatif');
Route::get('/keputusan', [SPKController::class, 'keputusan'])->name('spk.keputusan');
