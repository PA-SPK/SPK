@extends('layouts.app')

@section('title', 'Hasil & Rekomendasi Keputusan - SPK Puskesmas')
@section('page_title', 'Hasil & Rekomendasi Keputusan TOPSIS')

@section('content')
<div class="p-margin max-w-[1540px] mx-auto w-full flex flex-col gap-space-lg">
    <!-- Top Header Overview -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    Tahap 3: Keputusan Final TOPSIS
                </span>
                <span class="text-on-surface-variant font-label-sm text-label-sm tracking-wide uppercase">Dinas Kesehatan Kabupaten (Kelompok 6)</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Hasil Evaluasi & Rekomendasi Lokasi Puskesmas</h1>
            <p class="font-body-md text-on-surface-variant mt-0.5">Penetapan rekomendasi lokasi pembangunan Puskesmas Rawat Inap Baru berdasarkan ranking kedekatan relatif (Vi)</p>
        </div>
        <div class="flex flex-wrap items-center gap-space-sm">
            <a href="{{ route('spk.alternatif') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-md text-sm border border-outline-variant/20 hover:bg-surface-container-highest transition-all font-semibold">
                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                <span>Ubah Data Matriks</span>
            </a>
            <button type="button" onclick="window.print()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary/90 shadow-md shadow-primary/20 transition-all font-semibold">
                <span class="material-symbols-outlined text-[20px]">print</span>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    @if(count($ranked) > 0)
    <!-- Top 3 Podium Winners Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-end">
        <!-- Rank #2: Silver Winner (Order 2 on desktop) -->
        <div class="lg:col-span-4 order-2 lg:order-1 bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-outline-variant/10 relative overflow-hidden transition-all hover:-translate-y-1">
            @if($podium['runner_up'])
                <div class="absolute top-0 right-0 w-24 h-24 bg-surface-container rounded-bl-full -mr-4 -mt-4 opacity-50 pointer-events-none"></div>
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-surface-container text-on-surface flex items-center justify-center font-bold text-label-lg shadow-sm">
                            #2
                        </span>
                        <span class="px-space-xs py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm tracking-wide font-semibold">
                            PERINGKAT DUA
                        </span>
                    </div>
                    <span class="px-space-sm py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold flex items-center gap-1">
                        CADANGAN #1
                    </span>
                </div>
                <div class="mt-space-md space-y-1">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider font-mono font-bold">{{ $podium['runner_up']['kode'] }}</span>
                    <h3 class="font-headline-md text-headline-md text-on-surface line-clamp-1 font-bold">{{ $podium['runner_up']['alternatif']->nama }}</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2">{{ $podium['runner_up']['alternatif']->deskripsi ?? '-' }}</p>
                </div>
                <div class="mt-space-lg p-space-sm bg-surface-container-low rounded-xl flex items-center justify-between border border-outline-variant/10">
                    <div>
                        <span class="font-label-sm text-label-sm text-on-surface-variant block">Skor Preferensi (Vi)</span>
                        <span class="font-numeric-metric text-numeric-metric text-on-surface font-bold">{{ number_format($podium['runner_up']['vi'], 4) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-label-sm text-label-sm text-on-surface-variant block">Kelayakan</span>
                        <span class="font-headline-sm text-headline-sm text-secondary font-bold">{{ $podium['runner_up']['kelayakan'] }}</span>
                    </div>
                </div>
                <div class="mt-space-md pt-space-sm flex items-center justify-between font-label-sm text-label-sm text-on-surface-variant border-t border-outline-variant/10">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span>D<sup>+</sup>: <strong class="text-on-surface font-mono">{{ number_format($podium['runner_up']['d_plus'], 4) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-tertiary"></span>
                        <span>D<sup>-</sup>: <strong class="text-on-surface font-mono">{{ number_format($podium['runner_up']['d_minus'], 4) }}</strong></span>
                    </div>
                </div>
            @else
                <div class="py-12 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-2 opacity-40">hourglass_empty</span>
                    <p class="text-xs">Belum ada alternatif untuk Peringkat #2</p>
                </div>
            @endif
        </div>

        <!-- Rank #1: Gold / Emerald Hero Winner (Center, Elevated) -->
        <div class="lg:col-span-4 order-1 lg:order-2 bg-primary text-on-primary p-space-lg rounded-2xl shadow-[0_16px_36px_-6px_rgba(0,105,72,0.32)] relative overflow-hidden transform lg:-translate-y-3 transition-all hover:-translate-y-4">
            @if($podium['juara'])
                <div class="absolute -right-8 -top-8 w-36 h-36 bg-surface-tint rounded-full opacity-40 pointer-events-none"></div>
                <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-primary-fixed-dim rounded-full opacity-20 pointer-events-none"></div>
                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold text-headline-sm shadow-md">
                            #1
                        </div>
                        <span class="px-space-sm py-0.5 rounded-full bg-on-primary/20 text-on-primary font-label-sm text-label-sm uppercase tracking-wider backdrop-blur-sm font-bold">
                            LOKASI TERBAIK
                        </span>
                    </div>
                    <span class="px-space-sm py-1 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">stars</span> REKOMENDASI UTAMA
                    </span>
                </div>
                <div class="mt-space-md space-y-1 relative z-10">
                    <div class="flex items-center gap-2">
                        <span class="font-label-sm text-label-sm text-primary-fixed uppercase tracking-wider font-mono font-bold">{{ $podium['juara']['kode'] }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-fixed"></span>
                        <span class="font-label-sm text-label-sm text-primary-fixed">Skor Tertinggi</span>
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-primary tracking-tight font-bold">{{ $podium['juara']['alternatif']->nama }}</h2>
                    <p class="font-body-sm text-body-sm text-on-primary/90 line-clamp-2">
                        {{ $podium['juara']['alternatif']->deskripsi ?? 'Alternatif dengan skor kedekatan relatif TOPSIS tertinggi.' }}
                    </p>
                </div>
                <!-- Hero Score Box -->
                <div class="mt-space-lg p-space-md bg-on-primary/10 backdrop-blur-md rounded-xl flex items-center justify-between relative z-10 border border-white/10">
                    <div>
                        <span class="font-label-sm text-label-sm text-primary-fixed block font-medium">Skor Kedekatan Relatif (Vi)</span>
                        <span class="font-numeric-metric text-numeric-metric text-on-primary font-bold">{{ number_format($podium['juara']['vi'], 4) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-label-sm text-label-sm text-primary-fixed block font-medium">Indeks Kelayakan</span>
                        <span class="font-headline-md text-headline-md text-primary-fixed font-bold">{{ $podium['juara']['kelayakan'] }}</span>
                    </div>
                </div>
                <!-- Separation Distances -->
                <div class="mt-space-md pt-space-xs flex items-center justify-between font-label-sm text-label-sm text-on-primary/90 relative z-10 border-t border-white/10">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-primary-fixed"></span>
                        <span>D<sup>+</sup> (Positif): <strong class="text-on-primary font-mono">{{ number_format($podium['juara']['d_plus'], 4) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-secondary-fixed"></span>
                        <span>D<sup>-</sup> (Negatif): <strong class="text-on-primary font-mono">{{ number_format($podium['juara']['d_minus'], 4) }}</strong></span>
                    </div>
                </div>
            @else
                <div class="py-12 text-center text-on-primary/80">
                    <span class="material-symbols-outlined text-4xl mb-2 opacity-60">hourglass_empty</span>
                    <p class="text-xs">Belum ada alternatif yang dievaluasi.</p>
                </div>
            @endif
        </div>

        <!-- Rank #3: Bronze Winner (Order 3 on desktop) -->
        <div class="lg:col-span-4 order-3 lg:order-3 bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-outline-variant/10 relative overflow-hidden transition-all hover:-translate-y-1">
            @if($podium['third'])
                <div class="absolute top-0 right-0 w-24 h-24 bg-tertiary-fixed rounded-bl-full -mr-4 -mt-4 opacity-40 pointer-events-none"></div>
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-tertiary-fixed text-on-tertiary-fixed flex items-center justify-center font-bold text-label-lg shadow-sm">
                            #3
                        </span>
                        <span class="px-space-xs py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm tracking-wide font-semibold">
                            PERINGKAT TIGA
                        </span>
                    </div>
                    <span class="px-space-sm py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold flex items-center gap-1">
                        CADANGAN #2
                    </span>
                </div>
                <div class="mt-space-md space-y-1">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider font-mono font-bold">{{ $podium['third']['kode'] }}</span>
                    <h3 class="font-headline-md text-headline-md text-on-surface line-clamp-1 font-bold">{{ $podium['third']['alternatif']->nama }}</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2">{{ $podium['third']['alternatif']->deskripsi ?? '-' }}</p>
                </div>
                <div class="mt-space-lg p-space-sm bg-surface-container-low rounded-xl flex items-center justify-between border border-outline-variant/10">
                    <div>
                        <span class="font-label-sm text-label-sm text-on-surface-variant block">Skor Preferensi (Vi)</span>
                        <span class="font-numeric-metric text-numeric-metric text-on-surface font-bold">{{ number_format($podium['third']['vi'], 4) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-label-sm text-label-sm text-on-surface-variant block">Kelayakan</span>
                        <span class="font-headline-sm text-headline-sm text-tertiary font-bold">{{ $podium['third']['kelayakan'] }}</span>
                    </div>
                </div>
                <div class="mt-space-md pt-space-sm flex items-center justify-between font-label-sm text-label-sm text-on-surface-variant border-t border-outline-variant/10">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span>D<sup>+</sup>: <strong class="text-on-surface font-mono">{{ number_format($podium['third']['d_plus'], 4) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-tertiary"></span>
                        <span>D<sup>-</sup>: <strong class="text-on-surface font-mono">{{ number_format($podium['third']['d_minus'], 4) }}</strong></span>
                    </div>
                </div>
            @else
                <div class="py-12 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-2 opacity-40">hourglass_empty</span>
                    <p class="text-xs">Belum ada alternatif untuk Peringkat #3</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Analytical Core: Tabs + Full Ranking Table & Side Panel -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
        <!-- Main Analytical Table Area (col-span-8) -->
        <div class="xl:col-span-8 space-y-space-md">
            <!-- Tab Controls & Filter Bar -->
            <div class="bg-surface-container-lowest p-space-sm rounded-2xl shadow-sm border border-outline-variant/10 flex flex-col md:flex-row md:items-center justify-between gap-space-sm">
                <div class="flex items-center gap-1 p-1 bg-surface-container rounded-xl overflow-x-auto">
                    <button class="tab-btn px-space-md py-space-xs rounded-lg font-label-md text-label-md transition-all bg-surface-container-lowest text-primary shadow-sm font-semibold" data-target="tab-ranking" type="button">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">format_list_numbered</span>
                            <span>Peringkat Akhir TOPSIS</span>
                        </span>
                    </button>
                    <button class="tab-btn px-space-md py-space-xs rounded-lg font-label-md text-label-md transition-all text-on-surface-variant hover:text-on-surface" data-target="tab-jarak" type="button">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">straighten</span>
                            <span>Solusi Ideal & Jarak Euclidean</span>
                        </span>
                    </button>
                    <button class="tab-btn px-space-md py-space-xs rounded-lg font-label-md text-label-md transition-all text-on-surface-variant hover:text-on-surface" data-target="tab-matriks-terbobot" type="button">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">grid_on</span>
                            <span>Matriks Terbobot (Y)</span>
                        </span>
                    </button>
                </div>
                <div class="flex items-center gap-space-xs px-space-xs">
                    <div class="relative">
                        <input class="bg-surface-container-low text-on-surface rounded-xl pl-8 pr-3 py-1.5 font-body-sm text-body-sm focus:outline-none focus:bg-surface-container-lowest transition-colors w-48 border border-outline-variant/20" id="search-candidate" placeholder="Cari desa / kode..." type="text"/>
                        <span class="material-symbols-outlined text-outline absolute left-2 top-2 text-[16px]">search</span>
                    </div>
                </div>
            </div>

            <!-- Tab Panel 1: Ranking Table -->
            <div class="tab-content bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden" id="tab-ranking">
                <div class="p-space-md flex items-center justify-between bg-surface-container-low/50 border-b border-outline-variant/10">
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Peringkat Kelayakan Alternatif Lokasi</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Diurutkan berdasarkan rumus kedekatan relatif TOPSIS: V<sub>i</sub> = D<sub>i</sub><sup>-</sup> / (D<sub>i</sub><sup>+</sup> + D<sub>i</sub><sup>-</sup>)</p>
                    </div>
                    <span class="font-label-sm text-label-sm bg-primary/10 text-primary px-space-sm py-1 rounded-full font-semibold">
                        {{ count($ranked) }} Lokasi Dievaluasi
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
                                <th class="py-space-sm px-space-md text-center">Rank</th>
                                <th class="py-space-sm px-space-md">Kode</th>
                                <th class="py-space-sm px-space-md">Lokasi Usulan</th>
                                <th class="py-space-sm px-space-md text-center font-mono">D+</th>
                                <th class="py-space-sm px-space-md text-center font-mono">D-</th>
                                <th class="py-space-sm px-space-md text-right">Skor (V<sub>i</sub>)</th>
                                <th class="py-space-sm px-space-md text-center">Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10 font-body-md text-body-md text-on-surface" id="ranking-table-body">
                            @forelse($ranked as $row)
                                <tr class="hover:bg-surface-container-high/40 transition-colors {{ $row['rank'] == 1 ? 'bg-primary-fixed/20 font-medium' : '' }}">
                                    <td class="py-space-md px-space-md text-center">
                                        <span class="w-7 h-7 rounded-full {{ $row['rank'] == 1 ? 'bg-primary text-on-primary' : ($row['rank'] == 2 ? 'bg-secondary text-on-secondary' : ($row['rank'] == 3 ? 'bg-tertiary text-on-tertiary' : 'bg-surface-container text-on-surface')) }} flex items-center justify-center font-bold text-label-sm mx-auto">
                                            {{ $row['rank'] }}
                                        </span>
                                    </td>
                                    <td class="py-space-md px-space-md font-bold text-primary font-mono font-label-lg">
                                        {{ $row['kode'] }}
                                    </td>
                                    <td class="py-space-md px-space-md">
                                        <div class="font-headline-sm text-sm text-on-surface font-semibold">{{ $row['alternatif']->nama }}</div>
                                        <div class="font-body-sm text-[11px] text-on-surface-variant">{{ $row['alternatif']->kategori }} &bull; {{ $row['alternatif']->deskripsi }}</div>
                                    </td>
                                    <td class="py-space-md px-space-md text-center font-mono text-xs">
                                        {{ number_format($row['d_plus'], 4) }}
                                    </td>
                                    <td class="py-space-md px-space-md text-center font-mono text-xs">
                                        {{ number_format($row['d_minus'], 4) }}
                                    </td>
                                    <td class="py-space-md px-space-md text-right font-numeric-metric text-lg {{ $row['rank'] == 1 ? 'text-primary font-bold' : 'text-on-surface font-semibold' }}">
                                        {{ number_format($row['vi'], 4) }}
                                        <span class="block text-[11px] font-sans font-normal text-on-surface-variant">{{ $row['kelayakan'] }}</span>
                                    </td>
                                    <td class="py-space-md px-space-md text-center">
                                        <span class="px-space-sm py-1 rounded-xl {{ $row['rank'] == 1 ? 'bg-primary text-on-primary shadow-sm font-bold' : ($row['rank'] <= 3 ? 'bg-secondary-fixed text-on-secondary-fixed font-semibold' : 'bg-surface-container text-on-surface-variant') }} font-label-sm text-[11px]">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-on-surface-variant">
                                        Belum ada alternatif yang dievaluasi. Silakan tambahkan alternatif di menu Alternatif & Matriks.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Panel 2: Jarak Solusi Ideal (A+ & A-) -->
            <div class="tab-content bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden hidden" id="tab-jarak">
                <div class="p-space-md bg-surface-container-low/50 border-b border-outline-variant/10">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Solusi Ideal Positif (A+) dan Negatif (A-)</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Titik referensi optimal untuk menghitung jarak Euclidean D+ dan D-</p>
                </div>
                <div class="p-space-lg space-y-space-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                        <div class="p-space-md rounded-xl bg-primary-fixed/20 border border-primary/20 space-y-2">
                            <span class="font-label-md text-primary font-bold block">Solusi Ideal Positif (A+)</span>
                            <div class="space-y-1 text-xs font-mono">
                                @forelse($topsis['ideal_positive'] as $kKode => $val)
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">{{ $kKode }}:</span>
                                        <span class="font-bold text-primary">{{ number_format($val, 6) }}</span>
                                    </div>
                                @empty
                                    <div class="text-on-surface-variant text-xs">Belum ada data kriteria.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="p-space-md rounded-xl bg-tertiary-fixed/30 border border-tertiary/20 space-y-2">
                            <span class="font-label-md text-tertiary font-bold block">Solusi Ideal Negatif (A-)</span>
                            <div class="space-y-1 text-xs font-mono">
                                @forelse($topsis['ideal_negative'] as $kKode => $val)
                                    <div class="flex justify-between">
                                        <span class="text-on-surface-variant">{{ $kKode }}:</span>
                                        <span class="font-bold text-tertiary">{{ number_format($val, 6) }}</span>
                                    </div>
                                @empty
                                    <div class="text-on-surface-variant text-xs">Belum ada data kriteria.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Panel 3: Matriks Terbobot (Y) -->
            <div class="tab-content bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden hidden" id="tab-matriks-terbobot">
                <div class="p-space-md bg-surface-container-low/50 border-b border-outline-variant/10">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Matriks Ternormalisasi Terbobot (Y)</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Hasil perkalian bobot AHP (w<sub>j</sub>) dengan elemen matriks ternormalisasi (r<sub>ij</sub>): y<sub>ij</sub> = w<sub>j</sub> &times; r<sub>ij</sub></p>
                </div>
                <div class="overflow-x-auto p-space-md">
                    <table class="w-full text-center border-collapse text-xs font-mono">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-sans font-bold">
                                <th class="py-2.5 px-3 text-left">Kode</th>
                                <th class="py-2.5 px-3 text-left">Lokasi</th>
                                @foreach($topsis['kriterias'] as $k)
                                    <th class="py-2.5 px-3 text-primary">{{ $k->kode }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($topsis['matrix_y'] as $altKode => $cols)
                                <tr class="hover:bg-surface-container-low/50 {{ ($podium['juara'] && $altKode == $podium['juara']['kode']) ? 'bg-primary-fixed/20 font-bold' : '' }}">
                                    <td class="py-2.5 px-3 text-left font-bold text-primary">{{ $altKode }}</td>
                                    <td class="py-2.5 px-3 text-left font-sans">{{ $topsis['alternatifs']->where('kode', $altKode)->first()->nama ?? '' }}</td>
                                    @foreach($cols as $kKode => $val)
                                        <td class="py-2.5 px-3">{{ number_format($val, 4) }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($topsis['kriterias']) + 2 }}" class="py-6 text-center text-on-surface-variant">
                                        Belum ada data matriks.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side Panel (col-span-4): Quota & Kesimpulan -->
        <div class="xl:col-span-4 space-y-space-md">
            <!-- Keputusan Akhir Card -->
            <div class="bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-outline-variant/10">
                <div class="flex items-center gap-2 text-primary mb-space-sm">
                    <span class="material-symbols-outlined text-[24px]">verified</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Keputusan Rekomendasi Utama</h3>
                </div>
                @if($podium['juara'])
                <div class="p-space-md bg-primary-fixed/20 rounded-xl space-y-2 border border-primary/20">
                    <div class="text-xs text-on-surface-variant font-medium">Rekomendasi 1 Lokasi Terpilih:</div>
                    <div class="font-headline-md text-lg text-primary font-bold">{{ $podium['juara']['kode'] }} - {{ $podium['juara']['alternatif']->nama }}</div>
                    <div class="text-xs text-on-surface space-y-1 pt-1 border-t border-primary/20">
                        <div class="flex justify-between">
                            <span>Skor Preferensi (Vi):</span>
                            <span class="font-bold font-mono">{{ number_format($podium['juara']['vi'], 6) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Indeks Kelayakan:</span>
                            <span class="font-bold text-primary">{{ $podium['juara']['kelayakan'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Jarak Positif (D+):</span>
                            <span class="font-bold font-mono">{{ number_format($podium['juara']['d_plus'], 4) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Jarak Negatif (D-):</span>
                            <span class="font-bold font-mono">{{ number_format($podium['juara']['d_minus'], 4) }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-space-md">
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Lokasi <strong>{{ $podium['juara']['alternatif']->nama }} ({{ $podium['juara']['kode'] }})</strong> terpilih sebagai prioritas tertinggi pembangunan Puskesmas Rawat Inap Baru dengan nilai kedekatan relatif <strong>{{ number_format($podium['juara']['vi'], 4) }}</strong> terhadap solusi ideal positif (A+) dan negatif (A-).
                    </p>
                </div>
                @else
                <div class="p-space-md bg-surface-container-low rounded-xl text-center text-xs text-on-surface-variant">
                    Belum ada alternatif yang dievaluasi.
                </div>
                @endif
            </div>

            <!-- Profil Tim Kelompok 6 -->
            <div class="bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-outline-variant/10">
                <div class="flex items-center gap-2 text-primary mb-2">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                    <h4 class="font-label-lg text-on-surface font-bold">Tim Penyusun Kelompok 6</h4>
                </div>
                <ul class="text-xs text-on-surface-variant space-y-1.5 font-mono">
                    <li>&bull; Darel Prasetya Fawwaz (2409116064)</li>
                    <li>&bull; Dinathan Fahrezi (2409116050)</li>
                    <li>&bull; Muhammad Romadhoni Alfatih (2409116104)</li>
                    <li>&bull; Rizky Yunia (2409116089)</li>
                    <li>&bull; Nurhidayah (2409116002)</li>
                </ul>
                <div class="mt-space-md pt-space-sm border-t border-outline-variant/10">
                    <a href="{{ route('spk.dashboard') }}" class="w-full py-2.5 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high font-label-md text-xs font-semibold transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">home</span>
                        <span>Kembali ke Beranda SPK</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');

                tabBtns.forEach(b => {
                    b.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm', 'font-semibold');
                    b.classList.add('text-on-surface-variant');
                });
                this.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm', 'font-semibold');
                this.classList.remove('text-on-surface-variant');

                tabContents.forEach(tc => {
                    if (tc.id === targetId) {
                        tc.classList.remove('hidden');
                    } else {
                        tc.classList.add('hidden');
                    }
                });
            });
        });

        const searchInput = document.getElementById('search-candidate');
        const rankingBody = document.getElementById('ranking-table-body');
        if (searchInput && rankingBody) {
            searchInput.addEventListener('input', function(e) {
                const val = e.target.value.toLowerCase();
                const rows = rankingBody.getElementsByTagName('tr');
                for (let row of rows) {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(val) ? '' : 'none';
                }
            });
        }
    });
</script>
@endsection
