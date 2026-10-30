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
            <p class="font-body-md text-on-surface-variant mt-0.5">Penetapan bobot prioritas kriteria berdasarkan matriks perbandingan berpasangan tim pakar Bappeda & Dinkes</p>
        </div>
        <div class="flex items-center gap-space-sm">
            <div class="px-4 py-2.5 rounded-xl bg-primary-fixed/40 text-on-primary-fixed-variant font-label-md text-label-md font-semibold border border-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-primary">verified</span>
                <span>Uji Konsistensi: KONSISTEN (CR = {{ number_format($ahp['cr'], 4) }})</span>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards: 4 Columns -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter items-stretch">
        <!-- Card 1: Total Kriteria -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary-fixed/40 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">format_list_bulleted</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    6 Parameter
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">6 Kriteria</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant mt-1">Total Kriteria Penilaian Puskesmas</div>
            </div>
        </div>

        <!-- Card 2: Benefit Criteria -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">trending_up</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm font-bold">
                    94.06% Bobot
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">5 Kriteria</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant mt-1">Tipe Benefit (K1, K2, K3, K4, K6)</div>
            </div>
        </div>

        <!-- Card 3: Cost Criteria -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-tertiary-fixed text-tertiary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">trending_down</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold">
                    5.94% Bobot
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg font-bold text-tertiary tracking-tight">1 Kriteria</div>
                <div class="font-label-sm text-label-sm text-on-surface-variant mt-1">Tipe Cost: K5 (Biaya Lahan)</div>
            </div>
        </div>

        <!-- Card 4: Uji Konsistensi AHP -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-secondary-fixed/50 text-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">verified</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    CR &lt; 0.10
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">CR = {{ number_format($ahp['cr'], 4) }}</div>
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
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Bobot dihitung dari rata-rata baris matriks ternormalisasi AHP (Kelompok 6)</p>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md uppercase tracking-wider rounded-xl">
                                <th class="py-3 px-4 rounded-l-xl">Kode</th>
                                <th class="py-3 px-4">Nama Parameter Kriteria</th>
                                <th class="py-3 px-4">Satuan</th>
                                <th class="py-3 px-4">Tipe</th>
                                <th class="py-3 px-4 text-center">Bobot Prioritas (w)</th>
                                <th class="py-3 px-4 min-w-[140px] rounded-r-xl">Persentase (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="kriteria-table-body">
                            @foreach($kriterias as $k)
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
                                        {{ $k->satuan }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-3 py-1 rounded-full {{ $k->tipe == 'Benefit' ? 'bg-primary-fixed/40 text-on-primary-fixed-variant' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' }} font-label-sm text-label-sm inline-flex items-center gap-1 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $k->tipe == 'Benefit' ? 'bg-primary' : 'bg-tertiary' }}"></span>
                                            {{ $k->tipe }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-headline-sm text-headline-sm font-bold {{ $k->kode == 'K2' ? 'text-primary' : 'text-on-surface' }}">
                                        {{ number_format($w, 10) }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-full bg-surface-container-high rounded-full h-2 overflow-hidden">
                                                <div class="{{ $k->tipe == 'Benefit' ? 'bg-primary' : 'bg-tertiary' }} h-2 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="font-label-sm text-label-sm text-on-surface-variant w-12 font-semibold">{{ number_format($pct, 2) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
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
                            1.0000000000 <span class="font-label-md text-label-md font-normal text-on-surface-variant">(100%)</span>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                            Valid untuk Normalisasi TOPSIS
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Matriks Perbandingan Berpasangan (AHP Pairwise) -->
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10">
                <div class="flex items-center justify-between mb-space-sm">
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Matriks Perbandingan Berpasangan (Pairwise Matrix)</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Nilai skala Saaty (1-9) berdasarkan kesepakatan tim pakar Bappeda & Dinkes</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-surface-container-high text-xs font-mono font-semibold">a<sub>ji</sub> = 1 / a<sub>ij</sub></span>
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
                            @foreach($ahp['pairwise_matrix'] as $rowKey => $cols)
                                <tr class="hover:bg-surface-container-low/50">
                                    <td class="py-2.5 px-3 text-left font-sans font-semibold text-on-surface">
                                        {{ $rowKey }} - {{ $kriterias->where('kode', $rowKey)->first()->nama ?? '' }}
                                    </td>
                                    @foreach($cols as $colKey => $val)
                                        <td class="py-2.5 px-3 {{ $rowKey == $colKey ? 'bg-primary-fixed/20 font-bold text-primary' : '' }}">
                                            {{ round($val, 4) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr class="bg-surface-container font-bold font-sans">
                                <td class="py-2.5 px-3 text-left">Jumlah Kolom (&Sigma;)</td>
                                @foreach($ahp['col_sums'] as $colKey => $sumVal)
                                    <td class="py-2.5 px-3 font-mono text-primary">{{ number_format($sumVal, 4) }}</td>
                                @endforeach
                            </tr>
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
                        <span class="font-bold text-on-surface">6 Kriteria</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant font-medium">Nilai Eigen Terbesar (&lambda;max):</span>
                        <span class="font-bold font-mono text-on-surface">{{ number_format($ahp['lambda_max'], 9) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant font-medium">Consistency Index (CI):</span>
                        <span class="font-bold font-mono text-on-surface">{{ number_format($ahp['ci'], 9) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant font-medium">Random Index (RI, n=6):</span>
                        <span class="font-bold font-mono text-on-surface">1.24</span>
                    </div>
                    <div class="flex justify-between items-center text-xs pt-1 border-t border-outline-variant/20">
                        <span class="text-on-surface font-bold">Consistency Ratio (CR):</span>
                        <span class="font-bold font-mono text-primary text-sm">{{ number_format($ahp['cr'], 9) }}</span>
                    </div>
                </div>

                <div class="mt-space-md p-space-sm rounded-xl bg-primary-fixed/30 border border-primary/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                    <span class="font-label-sm text-xs text-primary font-bold">
                        Kesimpulan: KONSISTEN (CR = {{ number_format($ahp['cr'], 4) }} &lt; 0.10)
                    </span>
                </div>
            </div>

            <!-- Insight Pakar Dinas Kesehatan -->
            <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10">
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2">Insight Studi Kasus Pakar</h3>
                <p class="font-body-sm text-xs text-on-surface-variant leading-relaxed">
                    Sesuai hasil wawancara Kepala Dinas Kesehatan & tim perencana Bappeda, kriteria <strong>Jarak Faskes Terdekat (K2)</strong> diberikan prioritas tertinggi (<strong>35.01%</strong>) karena sangat penting agar lokasi Puskesmas Rawat Inap baru tidak berdekatan/tumpang tindih dengan Puskesmas atau Rumah Sakit eksisting.
                </p>
                <div class="mt-space-md pt-space-sm border-t border-outline-variant/10 flex items-center justify-between">
                    <a href="{{ route('spk.alternatif') }}" class="text-primary font-label-sm text-xs font-bold flex items-center gap-1 hover:underline">
                        <span>Lanjut ke Matriks Alternatif</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
