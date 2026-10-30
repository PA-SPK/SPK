@extends('layouts.app')

@section('title', 'Kriteria & Bobot AHP - SPK Decision Suite')
@section('page_title', 'Kriteria & Pembobotan AHP')

@section('content')
<div class="p-margin max-w-[1540px] mx-auto w-full flex flex-col gap-space-lg">
    <!-- Top Header Overview -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    Tahap 1: Analytical Hierarchy Process (AHP)
                </span>
                <span class="text-on-surface-variant font-label-sm text-label-sm tracking-wide uppercase">Dinas Kesehatan Kabupaten</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Kriteria & Matriks Bobot AHP</h1>
            <p class="font-body-md text-on-surface-variant mt-0.5">Kelola kriteria dan pembobotan berbasis Analytical Hierarchy Process (AHP) secara dinamis</p>
        </div>
        <div class="flex flex-wrap items-center gap-space-sm">
            <button type="button" onclick="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-sm hover:bg-primary/90 shadow-md shadow-primary/20 transition-all font-semibold">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Tambah Kriteria</span>
            </button>
            <div class="px-4 py-2.5 rounded-xl {{ $ahp['is_consistent'] ? 'bg-primary-fixed/40 text-on-primary-fixed-variant border-primary/20' : 'bg-error/10 text-error border-error/20' }} font-label-md text-label-md font-semibold border flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] {{ $ahp['is_consistent'] ? 'text-primary' : 'text-error' }}">
                    {{ $ahp['is_consistent'] ? 'verified' : 'warning' }}
                </span>
                <span>Uji Konsistensi: {{ $ahp['is_consistent'] ? 'KONSISTEN' : 'TIDAK KONSISTEN' }} (CR = {{ number_format($ahp['cr'], 4) }})</span>
            </div>
        </div>
    </div>

    @php
        $benefitCriteria = $kriterias->where('tipe', 'Benefit');
        $costCriteria = $kriterias->where('tipe', 'Cost');
        $benefitWeight = $benefitCriteria->sum(fn($k) => $ahp['weights'][$k->kode] ?? $k->bobot);
        $costWeight = $costCriteria->sum(fn($k) => $ahp['weights'][$k->kode] ?? $k->bobot);
    @endphp

    <!-- KPI Metric Cards: 4 Columns -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter items-stretch">
        <!-- Card 1: Total Kriteria -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary-fixed/40 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">format_list_bulleted</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    {{ count($kriterias) }} Parameter
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">{{ count($kriterias) }} Kriteria</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant mt-1">Total Kriteria Penilaian</div>
            </div>
        </div>

        <!-- Card 2: Benefit Criteria -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">trending_up</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm font-bold">
                    {{ number_format($benefitWeight * 100, 2) }}% Bobot
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">{{ count($benefitCriteria) }} Kriteria</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant mt-1">Tipe Benefit (Maksimum)</div>
            </div>
        </div>

        <!-- Card 3: Cost Criteria -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-tertiary-fixed text-tertiary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">trending_down</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold">
                    {{ number_format($costWeight * 100, 2) }}% Bobot
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg font-bold text-tertiary tracking-tight">{{ count($costCriteria) }} Kriteria</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant mt-1">Tipe Cost (Minimum)</div>
            </div>
        </div>

        <!-- Card 4: Uji Konsistensi AHP -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-secondary-fixed/50 text-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">verified</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full {{ $ahp['is_consistent'] ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-error/20 text-error' }} font-label-sm text-label-sm font-semibold">
                    {{ $ahp['is_consistent'] ? 'CR < 0.10' : 'CR > 0.10' }}
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg font-bold {{ $ahp['is_consistent'] ? 'text-primary' : 'text-error' }} tracking-tight">CR = {{ number_format($ahp['cr'], 4) }}</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant font-semibold mt-1">&lambda;max = {{ number_format($ahp['lambda_max'], 4) }} &bull; CI = {{ number_format($ahp['ci'], 4) }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout: 12-column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start w-full">
        <!-- Left Section (8 cols): Criteria Table & Matriks Perbandingan -->
        <div class="lg:col-span-8 flex flex-col gap-space-md">
            <!-- 1. Daftar Kriteria & Bobot Prioritas -->
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm pb-space-md">
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Daftar Kriteria & Bobot Prioritas (w)</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Bobot dihitung dari rata-rata baris matriks ternormalisasi AHP</p>
                    </div>
                    <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-fixed/40 text-primary text-xs font-semibold hover:bg-primary-fixed">
                        <span class="material-symbols-outlined text-sm">add</span> Tambah Kriteria
                    </button>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md uppercase tracking-wider rounded-xl">
                                <th class="py-3 px-4 rounded-l-xl">Kode</th>
                                <th class="py-3 px-4">Nama Parameter Kriteria</th>
                                <th class="py-3 px-4">Satuan</th>
                                <th class="py-3 px-4">Tipe</th>
                                <th class="py-3 px-4 text-center">Bobot (w)</th>
                                <th class="py-3 px-4 min-w-[140px]">Persentase</th>
                                <th class="py-3 px-4 text-center rounded-r-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="kriteria-table-body">
                            @forelse($kriterias as $k)
                                @php
                                    $w = $ahp['weights'][$k->kode] ?? $k->bobot;
                                    $pct = $w * 100;
                                @endphp
                                <tr class="hover:bg-surface-container-low/70 transition-colors group">
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-lg {{ $k->tipe == 'Benefit' ? 'bg-surface-container-high text-primary' : 'bg-tertiary-fixed text-tertiary' }} font-label-md text-label-md font-bold">
                                            {{ $k->kode }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-label-lg text-label-lg text-on-surface font-semibold">
                                        {{ $k->nama }}
                                    </td>
                                    <td class="py-3.5 px-4 text-on-surface-variant font-mono text-xs">
                                        {{ $k->satuan ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-3 py-1 rounded-full {{ $k->tipe == 'Benefit' ? 'bg-primary-fixed/40 text-on-primary-fixed-variant' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' }} font-label-sm text-label-sm inline-flex items-center gap-1 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $k->tipe == 'Benefit' ? 'bg-primary' : 'bg-tertiary' }}"></span>
                                            {{ $k->tipe }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-headline-sm text-headline-sm font-bold text-on-surface">
                                        {{ number_format($w, 6) }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-full bg-surface-container-high rounded-full h-2 overflow-hidden">
                                                <div class="{{ $k->tipe == 'Benefit' ? 'bg-primary' : 'bg-tertiary' }} h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $pct) }}%"></div>
                                            </div>
                                            <span class="font-label-sm text-label-sm text-on-surface-variant w-14 font-semibold">{{ number_format($pct, 2) }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" onclick="openEditModal({{ json_encode($k) }})" class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-high transition-colors" title="Edit Kriteria">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>
                                            <button type="button" 
                                                    onclick="confirmDeleteKriteria({{ $k->id }}, '{{ $k->kode }}', '{{ addslashes($k->nama) }}')" 
                                                    class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error/10 transition-colors" 
                                                    title="Hapus Kriteria">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-on-surface-variant">Belum ada data kriteria. Klik "+ Tambah Kriteria" untuk menambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Total Footer Table Banner -->
                <div class="mt-space-md p-space-md rounded-xl bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-sm border border-outline-variant/10">
                    <div class="flex items-center gap-space-sm">
                        <span class="w-3 h-3 rounded-full bg-primary animate-pulse"></span>
                        <span class="font-label-lg text-label-lg text-on-surface font-semibold">Akumulasi Bobot Keseluruhan (&Sigma; w<sub>i</sub>)</span>
                    </div>
                    <div class="flex items-center gap-space-md">
                        <div class="font-numeric-metric text-numeric-metric font-bold text-primary">
                            {{ number_format(array_sum($ahp['weights']), 6) }} <span class="font-label-md text-label-md font-normal text-on-surface-variant">({{ number_format(array_sum($ahp['weights']) * 100, 1) }}%)</span>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                            Valid untuk Normalisasi TOPSIS
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Matriks Perbandingan Berpasangan (AHP Pairwise) Form -->
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10">
                <form action="{{ route('spk.kriteria.perbandingan') }}" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm mb-space-md">
                        <div>
                            <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Matriks Perbandingan Berpasangan (Pairwise Matrix)</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Ubah nilai perbandingan antar kriteria (skala 1-9 atau pecahan). Sisi kebalikan akan otomatis terhitung (a<sub>ji</sub> = 1 / a<sub>ij</sub>).</p>
                        </div>
                        @if(count($kriterias) > 1)
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-label-md text-xs font-bold hover:bg-primary/90 shadow-sm transition-all">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan & Hitung AHP</span>
                        </button>
                        @endif
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-center border-collapse text-xs">
                            <thead>
                                <tr class="bg-surface-container-low text-on-surface-variant font-label-sm">
                                    <th class="py-2.5 px-3 text-left">Kriteria</th>
                                    @foreach($kriterias as $k)
                                        <th class="py-2.5 px-3 font-bold text-primary">{{ $k->kode }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10 font-mono">
                                @foreach($kriterias as $kRow)
                                    <tr class="hover:bg-surface-container-low/50">
                                        <td class="py-2.5 px-3 text-left font-sans font-semibold text-on-surface whitespace-nowrap">
                                            {{ $kRow->kode }} - {{ $kRow->nama }}
                                        </td>
                                        @foreach($kriterias as $kCol)
                                            @php
                                                $val = $ahp['pairwise_matrix'][$kRow->kode][$kCol->kode] ?? 1.0;
                                                $isDiagonal = ($kRow->id === $kCol->id);
                                                $isUpper = ($kRow->id < $kCol->id);
                                            @endphp
                                            <td class="py-2 px-2 {{ $isDiagonal ? 'bg-primary-fixed/20 font-bold text-primary' : '' }}">
                                                @if($isDiagonal)
                                                    <span class="font-bold text-primary">1</span>
                                                @elseif($isUpper)
                                                    <input type="number" step="any" min="0.0001"
                                                        name="matrix[{{ $kRow->kode }}][{{ $kCol->kode }}]"
                                                        id="input_{{ $kRow->kode }}_{{ $kCol->kode }}"
                                                        value="{{ round($val, 4) }}"
                                                        oninput="handleMatrixChange('{{ $kRow->kode }}', '{{ $kCol->kode }}', this.value)"
                                                        class="w-16 px-1.5 py-1 text-center bg-surface-container-low border border-outline-variant/40 rounded-lg text-xs font-mono font-bold text-on-surface focus:outline-primary"
                                                        required />
                                                @else
                                                    <span id="label_{{ $kRow->kode }}_{{ $kCol->kode }}" class="text-on-surface-variant font-mono">
                                                        {{ round($val, 4) }}
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="bg-surface-container font-bold font-sans">
                                    <td class="py-2.5 px-3 text-left">Jumlah Kolom (&Sigma;)</td>
                                    @foreach($kriterias as $k)
                                        <td class="py-2.5 px-3 font-mono text-primary">
                                            {{ number_format($ahp['col_sums'][$k->kode] ?? 0, 4) }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if(count($kriterias) > 1)
                    <div class="mt-space-md flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-label-md text-sm font-bold hover:bg-primary/90 shadow-md shadow-primary/20 transition-all">
                            <span class="material-symbols-outlined text-[20px]">calculate</span>
                            <span>Simpan Perubahan & Perbarui Bobot AHP</span>
                        </button>
                    </div>
                    @endif
                </form>
            </div>

            <!-- 3. Matriks Ternormalisasi AHP (Transparency Table) -->
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10">
                <div class="flex items-center justify-between mb-space-sm">
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Matriks Normalisasi AHP & Rata-rata Baris</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Tiap elemen dibagi dengan jumlah kolomnya, lalu dirata-rata per baris untuk menghasilkan bobot (w)</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-surface-container-high text-xs font-mono font-semibold">w<sub>i</sub> = &Sigma; r<sub>ij</sub> / n</span>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-center border-collapse text-xs">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm">
                                <th class="py-2.5 px-3 text-left">Kriteria</th>
                                @foreach($kriterias as $k)
                                    <th class="py-2.5 px-3 font-bold text-primary">{{ $k->kode }}</th>
                                @endforeach
                                <th class="py-2.5 px-3 font-bold text-on-surface bg-primary-fixed/30">Bobot (w)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10 font-mono">
                            @foreach($kriterias as $kRow)
                                <tr class="hover:bg-surface-container-low/50">
                                    <td class="py-2.5 px-3 text-left font-sans font-semibold text-on-surface whitespace-nowrap">
                                        {{ $kRow->kode }}
                                    </td>
                                    @foreach($kriterias as $kCol)
                                        <td class="py-2.5 px-3">
                                            {{ number_format($ahp['normalized_matrix'][$kRow->kode][$kCol->kode] ?? 0, 4) }}
                                        </td>
                                    @endforeach
                                    <td class="py-2.5 px-3 font-bold text-primary bg-primary-fixed/20">
                                        {{ number_format($ahp['weights'][$kRow->kode] ?? 0, 6) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Section (4 cols): Consistency Test & Summary -->
        <div class="lg:col-span-4 flex flex-col gap-space-md">
            <!-- Uji Konsistensi AHP Card -->
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10">
                <div class="flex items-center gap-2 text-primary mb-space-sm">
                    <span class="material-symbols-outlined text-[24px]">verified</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Hasil Uji Konsistensi AHP</h3>
                </div>

                <div class="p-space-md rounded-xl bg-surface-container-low space-y-2.5 border border-outline-variant/10">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant font-medium">Jumlah Elemen (n):</span>
                        <span class="font-bold text-on-surface">{{ count($kriterias) }} Kriteria</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant font-medium">Nilai Eigen Terbesar (&lambda;max):</span>
                        <span class="font-bold font-mono text-on-surface">{{ number_format($ahp['lambda_max'], 6) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant font-medium">Consistency Index (CI):</span>
                        <span class="font-bold font-mono text-on-surface">{{ number_format($ahp['ci'], 6) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant font-medium">Random Index (RI, n={{ count($kriterias) }}):</span>
                        <span class="font-bold font-mono text-on-surface">{{ number_format($ahp['ri'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs pt-1 border-t border-outline-variant/20">
                        <span class="text-on-surface font-bold">Consistency Ratio (CR):</span>
                        <span class="font-bold font-mono {{ $ahp['is_consistent'] ? 'text-primary' : 'text-error' }} text-sm">{{ number_format($ahp['cr'], 6) }}</span>
                    </div>
                </div>

                <div class="mt-space-md p-space-sm rounded-xl {{ $ahp['is_consistent'] ? 'bg-primary-fixed/30 border-primary/20 text-primary' : 'bg-error/15 border-error/30 text-error' }} border flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">
                        {{ $ahp['is_consistent'] ? 'check_circle' : 'error' }}
                    </span>
                    <span class="font-label-sm text-xs font-bold">
                        Kesimpulan: {{ $ahp['is_consistent'] ? 'KONSISTEN (CR ≤ 0.10)' : 'TIDAK KONSISTEN (CR > 0.10). Silakan sesuaikan matriks.' }}
                    </span>
                </div>
            </div>

            <!-- Petunjuk Skala Saaty Card -->
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10">
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2">Panduan Skala Saaty (1-9)</h3>
                <div class="space-y-1.5 text-xs text-on-surface-variant leading-relaxed">
                    <div class="flex items-center justify-between py-1 border-b border-outline-variant/10">
                        <span class="font-bold text-primary">1</span>
                        <span>Sama penting (Equal importance)</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-outline-variant/10">
                        <span class="font-bold text-primary">3</span>
                        <span>Sedikit lebih penting (Moderate)</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-outline-variant/10">
                        <span class="font-bold text-primary">5</span>
                        <span>Lebih penting (Strong importance)</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-outline-variant/10">
                        <span class="font-bold text-primary">7</span>
                        <span>Sangat lebih penting (Very strong)</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-outline-variant/10">
                        <span class="font-bold text-primary">9</span>
                        <span>Mutlak lebih penting (Extreme)</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="font-bold text-on-surface">2, 4, 6, 8</span>
                        <span>Nilai antara pertimbangan</span>
                    </div>
                </div>
            </div>

            <!-- Link to Next Step -->
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2">Langkah Selanjutnya</h3>
                    <p class="font-body-sm text-xs text-on-surface-variant leading-relaxed">
                        Setelah bobot prioritas kriteria ditentukan, tahap selanjutnya adalah mengelola nilai alternatif pada Matriks Keputusan (X) untuk evaluasi TOPSIS.
                    </p>
                </div>
                <div class="mt-space-md pt-space-sm border-t border-outline-variant/10">
                    <a href="{{ route('spk.alternatif') }}" class="w-full justify-center px-4 py-2.5 bg-primary text-on-primary rounded-xl font-label-md text-xs font-bold hover:bg-primary/90 flex items-center gap-2 shadow-sm">
                        <span>Buka Matriks Alternatif</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kriteria -->
<div id="modalAddKriteria" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full p-6 shadow-xl border border-outline-variant/20 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/10">
            <h3 class="font-headline-sm text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">add_box</span>
                Tambah Kriteria Baru
            </h3>
            <button type="button" onclick="closeAddModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('spk.kriteria.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kode Kriteria</label>
                <input type="text" name="kode" placeholder="Misal: K7" required maxlength="10" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm font-mono uppercase focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Nama Parameter Kriteria</label>
                <input type="text" name="nama" placeholder="Misal: Ketersediaan Air Bersih" required class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Satuan Pengukuran</label>
                <input type="text" name="satuan" placeholder="Misal: Km, Jiwa, Juta Rp, Skor (1-5)" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Tipe Kriteria (Benefit / Cost)</label>
                <select name="tipe" required class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary">
                    <option value="Benefit">Benefit (Semakin tinggi nilai semakin baik)</option>
                    <option value="Cost">Cost (Semakin rendah nilai semakin baik / biaya)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Urutan Tampilan</label>
                <input type="number" name="urutan" value="{{ count($kriterias) + 1 }}" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline-variant/10">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary/90 shadow-sm">Simpan Kriteria</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kriteria -->
<div id="modalEditKriteria" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full p-6 shadow-xl border border-outline-variant/20 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/10">
            <h3 class="font-headline-sm text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">edit</span>
                Edit Kriteria
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="formEditKriteria" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kode Kriteria</label>
                <input type="text" name="kode" id="edit_kode" required maxlength="10" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm font-mono uppercase focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Nama Parameter Kriteria</label>
                <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Satuan Pengukuran</label>
                <input type="text" name="satuan" id="edit_satuan" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Tipe Kriteria (Benefit / Cost)</label>
                <select name="tipe" id="edit_tipe" required class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary">
                    <option value="Benefit">Benefit (Semakin tinggi semakin baik)</option>
                    <option value="Cost">Cost (Semakin rendah semakin baik)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Urutan</label>
                <input type="number" name="urutan" id="edit_urutan" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline-variant/10">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary/90 shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalAddKriteria').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('modalAddKriteria').classList.add('hidden');
    }

    function openEditModal(kriteria) {
        document.getElementById('formEditKriteria').action = `/kriteria/${kriteria.id}`;
        document.getElementById('edit_kode').value = kriteria.kode;
        document.getElementById('edit_nama').value = kriteria.nama;
        document.getElementById('edit_satuan').value = kriteria.satuan || '';
        document.getElementById('edit_tipe').value = kriteria.tipe;
        document.getElementById('edit_urutan').value = kriteria.urutan || 1;
        document.getElementById('modalEditKriteria').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('modalEditKriteria').classList.add('hidden');
    }

    function confirmDeleteKriteria(id, kode, nama) {
        openDeleteConfirm(
            `/kriteria/${id}`,
            `Hapus Kriteria ${kode}`,
            `Apakah Anda yakin ingin menghapus kriteria ${kode} (${nama})? Data perbandingan AHP dan seluruh nilai alternatif terkait kriteria ini akan ikut terhapus secara permanen.`
        );
    }

    function handleMatrixChange(k1, k2, val) {
        const v = parseFloat(val);
        const label = document.getElementById(`label_${k2}_${k1}`);
        if (label && v > 0) {
            label.innerText = (1 / v).toFixed(4);
        }
    }
</script>
@endsection
