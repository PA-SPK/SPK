@extends('layouts.app')

@section('title', 'Hasil & Rekomendasi Keputusan - Sistem Pendukung Keputusan')
@section('page_title', 'Hasil & Rekomendasi Keputusan SPK')

@section('content')
<div class="p-margin max-w-[1540px] mx-auto w-full flex flex-col gap-space-lg">
    <!-- Top Header Overview -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    Tahap 3: Keputusan & Evaluasi Akhir
                </span>
                <span class="text-on-surface-variant font-label-sm text-label-sm tracking-wide uppercase">
                    {{ $selectedMethod === 'komparasi' ? 'Analisis Komparasi Multimetode' : ($methods[$selectedMethod]['short'] ?? 'SPK') }}
                </span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Hasil Evaluasi & Rekomendasi Keputusan</h1>
            <p class="font-body-md text-on-surface-variant mt-0.5">
                @if($selectedMethod === 'komparasi')
                    Perbandingan perankingan alternatif antara metode TOPSIS, SAW, dan WP untuk melihat konsistensi rekomendasi keputusan.
                @else
                    Penetapan rekomendasi alternatif terbaik berdasarkan perankingan metode <strong>{{ $methods[$selectedMethod]['name'] ?? 'TOPSIS' }}</strong>.
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-space-sm">
            <a href="{{ route('spk.alternatif') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-md text-sm border border-outline-variant/20 hover:bg-surface-container-highest transition-all font-semibold">
                <span class="material-symbols-outlined text-[18px]">edit_note</span>
                <span>Ubah Data Matriks</span>
            </a>
            <a href="{{ route('spk.keputusan.export', ['metode' => $selectedMethod === 'komparasi' ? $activeMethod : $selectedMethod]) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-md text-sm border border-outline-variant/20 hover:bg-surface-container-highest transition-all font-semibold" title="Unduh hasil perankingan ke file CSV">
                <span class="material-symbols-outlined text-[18px] text-primary">download</span>
                <span>Export CSV</span>
            </a>
            <button type="button" onclick="window.print()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-label-md hover:bg-primary/90 shadow-md shadow-primary/20 transition-all font-semibold">
                <span class="material-symbols-outlined text-[20px]">print</span>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- Multi-Method Switcher Tabs Bar -->
    <div class="bg-surface-container-lowest p-2 rounded-2xl shadow-sm border border-outline-variant/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-xs font-bold text-on-surface-variant px-3 uppercase tracking-wider">Metode:</span>
            
            <!-- TOPSIS -->
            <a href="{{ route('spk.keputusan', ['metode' => 'topsis']) }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $selectedMethod === 'topsis' ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[17px]">military_tech</span>
                <span>TOPSIS</span>
                @if($activeMethod === 'topsis')
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400" title="Metode Utama"></span>
                @endif
            </a>

            <!-- SAW -->
            <a href="{{ route('spk.keputusan', ['metode' => 'saw']) }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $selectedMethod === 'saw' ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[17px]">calculate</span>
                <span>SAW</span>
                @if($activeMethod === 'saw')
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400" title="Metode Utama"></span>
                @endif
            </a>

            <!-- WP -->
            <a href="{{ route('spk.keputusan', ['metode' => 'wp']) }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $selectedMethod === 'wp' ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[17px]">functions</span>
                <span>WP</span>
                @if($activeMethod === 'wp')
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400" title="Metode Utama"></span>
                @endif
            </a>

            <!-- Komparasi -->
            <a href="{{ route('spk.keputusan', ['metode' => 'komparasi']) }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $selectedMethod === 'komparasi' ? 'bg-secondary text-on-secondary shadow-sm' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[17px]">compare</span>
                <span>Komparasi Multimetode</span>
            </a>
        </div>

        <div class="flex items-center gap-2 px-3">
            <a href="{{ route('spk.settings') }}" class="text-xs text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1 font-semibold">
                <span class="material-symbols-outlined text-[16px]">tune</span>
                <span>Atur Metode Utama di Pengaturan</span>
            </a>
        </div>
    </div>

    @if($isEmpty)
    <!-- Empty State -->
    <div class="rounded-3xl bg-surface-container-lowest border-2 border-dashed border-outline-variant/40 p-10 md:p-14 flex flex-col items-center justify-center text-center gap-space-md">
        <div class="w-16 h-16 rounded-3xl bg-primary/10 flex items-center justify-center text-primary mb-2">
            <span class="material-symbols-outlined text-[36px]">military_tech</span>
        </div>
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Belum Ada Hasil Keputusan</h2>
            <p class="font-body-md text-on-surface-variant mt-2 max-w-lg mx-auto leading-relaxed">
                Hasil perankingan SPK akan muncul di sini setelah Anda mengisi data kriteria dan alternatif, 
                serta mengatur bobot AHP. Ikuti langkah-langkah berikut:
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-sm mt-2 w-full max-w-2xl">
            <a href="{{ route('spk.import') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-primary/10 hover:bg-primary/20 transition-colors group">
                <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm">1</div>
                <span class="font-label-sm text-sm text-on-surface font-semibold group-hover:text-primary">Import Data</span>
                <span class="font-body-sm text-xs text-on-surface-variant text-center">Upload Excel/CSV kriteria dan alternatif</span>
            </a>
            <a href="{{ route('spk.kriteria') }}" class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-surface-container-low hover:bg-surface-container transition-colors group">
                <div class="w-10 h-10 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center font-bold text-sm">2</div>
                <span class="font-label-sm text-sm text-on-surface font-semibold group-hover:text-primary">Atur Bobot AHP</span>
                <span class="font-body-sm text-xs text-on-surface-variant text-center">Sesuaikan matriks perbandingan kriteria</span>
            </a>
            <div class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-surface-container-low">
                <div class="w-10 h-10 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center font-bold text-sm">3</div>
                <span class="font-label-sm text-sm text-on-surface font-semibold">Lihat Hasil</span>
                <span class="font-body-sm text-xs text-on-surface-variant text-center">Perankingan tampil otomatis di sini</span>
            </div>
        </div>
        <a href="{{ route('spk.import') }}" class="flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-on-primary font-label-md text-sm hover:bg-primary/90 shadow-md shadow-primary/20 transition-all font-semibold mt-2">
            <span class="material-symbols-outlined text-[20px]">upload_file</span>
            <span>Mulai dengan Import Data</span>
        </a>
    </div>
    @else

    @if($selectedMethod === 'komparasi')
    <!-- ==================== TAMPILAN KOMPARASI MULTIMETODE ==================== -->
    <div class="space-y-space-md">
        <!-- Konsensus Banner -->
        @php
            $topsisWinner = $comparisonData['topsis']['ranked_results'][0] ?? null;
            $sawWinner    = $comparisonData['saw']['ranked_results'][0] ?? null;
            $wpWinner     = $comparisonData['wp']['ranked_results'][0] ?? null;
            $isSameWinner = ($topsisWinner && $sawWinner && $wpWinner && ($topsisWinner['kode'] === $sawWinner['kode']) && ($sawWinner['kode'] === $wpWinner['kode']));
        @endphp

        <div class="p-space-lg rounded-2xl border {{ $isSameWinner ? 'bg-primary-fixed/20 border-primary/30' : 'bg-surface-container-lowest border-outline-variant/10 shadow-sm' }}">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                <div class="flex items-center gap-space-md">
                    <div class="w-12 h-12 rounded-2xl {{ $isSameWinner ? 'bg-primary text-on-primary' : 'bg-surface-container text-primary' }} flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-[26px]">{{ $isSameWinner ? 'verified' : 'balance' }}</span>
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                            {{ $isSameWinner ? 'Konsensus Penuh Terdeteksi!' : 'Perbandingan Hasil 3 Metode' }}
                        </h3>
                        <p class="font-body-sm text-xs text-on-surface-variant mt-0.5 leading-relaxed">
                            @if($isSameWinner)
                                Seluruh metode (TOPSIS, SAW, WP) sepakat merekomendasikan <strong class="text-primary font-bold">{{ $topsisWinner['kode'] }} ({{ $topsisWinner['alternatif']->nama }})</strong> sebagai Juara 1!
                            @else
                                Peringkat teratas antar metode: TOPSIS (#1 {{ $topsisWinner['kode'] ?? '-' }}), SAW (#1 {{ $sawWinner['kode'] ?? '-' }}), WP (#1 {{ $wpWinner['kode'] ?? '-' }}).
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap shrink-0">
                    <span class="px-3 py-1 rounded-xl bg-surface-container text-xs font-mono font-bold text-on-surface">
                        TOPSIS: #1 {{ $topsisWinner['kode'] ?? '-' }}
                    </span>
                    <span class="px-3 py-1 rounded-xl bg-surface-container text-xs font-mono font-bold text-on-surface">
                        SAW: #1 {{ $sawWinner['kode'] ?? '-' }}
                    </span>
                    <span class="px-3 py-1 rounded-xl bg-surface-container text-xs font-mono font-bold text-on-surface">
                        WP: #1 {{ $wpWinner['kode'] ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tabel Komparasi -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden">
            <div class="p-space-md bg-surface-container-low/50 border-b border-outline-variant/10 flex items-center justify-between">
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Tabel Perbandingan Ranking (TOPSIS vs SAW vs WP)</h3>
                    <p class="font-body-sm text-xs text-on-surface-variant">Diurutkan berdasarkan rata-rata peringkat dari ketiga metode</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-primary/10 text-primary">
                    {{ count($comparisonData['comparison']) }} Alternatif
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-container text-on-surface-variant font-label-md text-label-md uppercase tracking-wider text-xs">
                            <th class="py-3 px-4 text-center">No</th>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Nama Alternatif</th>
                            <th class="py-3 px-4 text-center">Rank TOPSIS (Vi)</th>
                            <th class="py-3 px-4 text-center">Rank SAW (Vi)</th>
                            <th class="py-3 px-4 text-center">Rank WP (Vi)</th>
                            <th class="py-3 px-4 text-center">Rata-rata Rank</th>
                            <th class="py-3 px-4 text-center">Status Konsistensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10 text-sm">
                        @foreach($comparisonData['comparison'] as $idx => $comp)
                        <tr class="hover:bg-surface-container-high/30 transition-colors {{ $comp['is_consistent'] ? 'bg-primary-fixed/15 font-semibold' : '' }}">
                            <td class="py-3 px-4 text-center font-bold text-on-surface-variant">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-primary">{{ $comp['kode'] }}</td>
                            <td class="py-3 px-4 font-medium text-on-surface">{{ $comp['nama'] }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg {{ $comp['topsis_rank'] == 1 ? 'bg-primary text-on-primary font-bold' : 'bg-surface-container' }}">
                                    #{{ $comp['topsis_rank'] }}
                                </span>
                                <span class="text-xs text-on-surface-variant font-mono ml-1">({{ number_format($comp['topsis_vi'], 4) }})</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg {{ $comp['saw_rank'] == 1 ? 'bg-primary text-on-primary font-bold' : 'bg-surface-container' }}">
                                    #{{ $comp['saw_rank'] }}
                                </span>
                                <span class="text-xs text-on-surface-variant font-mono ml-1">({{ number_format($comp['saw_vi'], 4) }})</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg {{ $comp['wp_rank'] == 1 ? 'bg-primary text-on-primary font-bold' : 'bg-surface-container' }}">
                                    #{{ $comp['wp_rank'] }}
                                </span>
                                <span class="text-xs text-on-surface-variant font-mono ml-1">({{ number_format($comp['wp_vi'], 4) }})</span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-on-surface">
                                {{ number_format($comp['avg_rank'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($comp['is_consistent'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 font-bold text-xs">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Konsisten Penuh
                                </span>
                                @elseif(abs($comp['topsis_rank'] - $comp['saw_rank']) <= 1 && abs($comp['saw_rank'] - $comp['wp_rank']) <= 1)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 font-semibold text-xs">
                                    <span class="material-symbols-outlined text-[14px]">compare_arrows</span>
                                    Selisih Kecil (&le;1)
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant text-xs">
                                    Bervariasi
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <!-- ==================== TAMPILAN SINGLE METODE (TOPSIS / SAW / WP) ==================== -->

    @if(count($ranked) > 0)
    <!-- Top 3 Podium Winners Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-end">
        <!-- Rank #2: Silver Winner -->
        <div class="lg:col-span-4 order-2 lg:order-1 bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-outline-variant/10 relative overflow-hidden transition-all hover:-translate-y-1">
            @if($podium['runner_up'])
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-surface-container text-on-surface flex items-center justify-center font-bold text-label-lg shadow-sm">
                            #2
                        </span>
                        <span class="px-space-xs py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm tracking-wide font-semibold">
                            PERINGKAT DUA
                        </span>
                    </div>
                    <span class="px-space-sm py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold">
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
            @endif
        </div>

        <!-- Rank #1: Gold Hero Winner -->
        <div class="lg:col-span-4 order-1 lg:order-2 bg-primary text-on-primary p-space-lg rounded-2xl shadow-[0_16px_36px_-6px_rgba(0,105,72,0.32)] relative overflow-hidden transform lg:-translate-y-3 transition-all hover:-translate-y-4">
            @if($podium['juara'])
                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold text-headline-sm shadow-md">
                            #1
                        </div>
                        <span class="px-space-sm py-0.5 rounded-full bg-on-primary/20 text-on-primary font-label-sm text-label-sm uppercase tracking-wider backdrop-blur-sm font-bold">
                            ALTERNATIF TERBAIK
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
                        <span class="font-label-sm text-label-sm text-primary-fixed">Skor Tertinggi ({{ $methods[$selectedMethod]['short'] ?? 'SPK' }})</span>
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-on-primary tracking-tight font-bold">{{ $podium['juara']['alternatif']->nama }}</h2>
                    <p class="font-body-sm text-body-sm text-on-primary/90 line-clamp-2">
                        {{ $podium['juara']['alternatif']->deskripsi ?? 'Alternatif dengan skor preferensi tertinggi.' }}
                    </p>
                </div>
                <div class="mt-space-lg p-space-md bg-on-primary/10 backdrop-blur-md rounded-xl flex items-center justify-between relative z-10 border border-white/10">
                    <div>
                        <span class="font-label-sm text-label-sm text-primary-fixed block">Nilai Preferensi (Vi)</span>
                        <span class="font-numeric-hero text-numeric-hero text-on-primary font-bold">{{ number_format($podium['juara']['vi'], 4) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-label-sm text-label-sm text-primary-fixed block">Persentase</span>
                        <span class="font-headline-lg text-headline-lg text-primary-fixed font-bold">{{ $podium['juara']['kelayakan'] }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Rank #3: Bronze Winner -->
        <div class="lg:col-span-4 order-3 bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-outline-variant/10 relative overflow-hidden transition-all hover:-translate-y-1">
            @if($podium['third'])
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-surface-container text-on-surface flex items-center justify-center font-bold text-label-lg shadow-sm">
                            #3
                        </span>
                        <span class="px-space-xs py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm tracking-wide font-semibold">
                            PERINGKAT TIGA
                        </span>
                    </div>
                    <span class="px-space-sm py-1 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold">
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
            @endif
        </div>
    </div>
    @endif

    <!-- Analytical Core: Full Ranking Table & Side Panel -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
        <div class="xl:col-span-8 space-y-space-md">
            <!-- Ranking Table Card -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden">
                <div class="p-space-md flex items-center justify-between bg-surface-container-low/50 border-b border-outline-variant/10">
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Peringkat Alternatif — {{ $methods[$selectedMethod]['name'] ?? 'SPK' }}</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">{{ $methods[$selectedMethod]['desc'] ?? '' }}</p>
                    </div>
                    <span class="font-label-sm text-label-sm bg-primary/10 text-primary px-space-sm py-1 rounded-full font-semibold">
                        {{ count($ranked) }} Alternatif
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container text-on-surface-variant font-label-md text-label-md uppercase tracking-wider text-xs">
                                <th class="py-space-sm px-space-md text-center">Rank</th>
                                <th class="py-space-sm px-space-md">Kode</th>
                                <th class="py-space-sm px-space-md">Nama Alternatif</th>
                                @if($selectedMethod === 'topsis')
                                    <th class="py-space-sm px-space-md text-center font-mono">D+</th>
                                    <th class="py-space-sm px-space-md text-center font-mono">D-</th>
                                @elseif($selectedMethod === 'wp')
                                    <th class="py-space-sm px-space-md text-center font-mono">Vektor S</th>
                                @endif
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
                                    <td class="py-space-md px-space-md font-mono font-bold text-primary">{{ $row['kode'] }}</td>
                                    <td class="py-space-md px-space-md font-medium">{{ $row['alternatif']->nama }}</td>
                                    @if($selectedMethod === 'topsis')
                                        <td class="py-space-md px-space-md text-center font-mono text-xs">{{ number_format($row['d_plus'], 4) }}</td>
                                        <td class="py-space-md px-space-md text-center font-mono text-xs">{{ number_format($row['d_minus'], 4) }}</td>
                                    @elseif($selectedMethod === 'wp')
                                        <td class="py-space-md px-space-md text-center font-mono text-xs">{{ number_format($row['vector_s'] ?? 0, 4) }}</td>
                                    @endif
                                    <td class="py-space-md px-space-md text-right font-bold font-mono text-primary">{{ number_format($row['vi'], 4) }}</td>
                                    <td class="py-space-md px-space-md text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full {{ $row['rank'] == 1 ? 'bg-primary text-on-primary' : ($row['rank'] <= 3 ? 'bg-secondary-fixed text-on-secondary-fixed' : 'bg-surface-container text-on-surface-variant') }} font-label-sm text-xs font-semibold">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-on-surface-variant">Belum ada alternatif yang dievaluasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Right (4 cols) -->
        <div class="xl:col-span-4 space-y-space-md">
            <!-- Ringkasan Metode Card -->
            <div class="bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-outline-variant/10">
                <div class="flex items-center gap-2 text-primary mb-2">
                    <span class="material-symbols-outlined text-[22px]">info</span>
                    <h4 class="font-label-lg text-on-surface font-bold">Tentang Metode {{ $methods[$selectedMethod]['short'] ?? 'SPK' }}</h4>
                </div>
                <p class="text-xs text-on-surface-variant leading-relaxed mb-space-md">
                    {{ $methods[$selectedMethod]['desc'] ?? '' }}
                </p>

                <div class="pt-space-sm border-t border-outline-variant/10 space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant">Metode Pembobotan</span>
                        <span class="font-bold text-primary font-mono">AHP (Analytical Hierarchy Process)</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant">Metode Perankingan</span>
                        <span class="font-bold text-primary font-mono">{{ $methods[$selectedMethod]['short'] ?? 'TOPSIS' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant">Jumlah Kriteria</span>
                        <span class="font-bold text-on-surface font-mono">{{ count($currentResult['kriterias'] ?? []) }} Kriteria</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-on-surface-variant">Alternatif Pemenang</span>
                        <span class="font-bold text-emerald-600 font-mono">{{ $podium['juara']['kode'] ?? '-' }} ({{ $podium['juara']['alternatif']->nama ?? '-' }})</span>
                    </div>
                </div>

                <div class="mt-space-md pt-space-sm border-t border-outline-variant/10">
                    <a href="{{ route('spk.settings') }}" class="w-full py-2.5 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high font-label-md text-xs font-semibold transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">settings</span>
                        <span>Ubah Metode Utama di Pengaturan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
