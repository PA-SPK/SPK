@extends('layouts.app')

@section('title', 'Dashboard SPK - Lokasi Pembangunan Puskesmas')
@section('page_title', 'Ringkasan Dashboard')

@section('content')
<div class="p-margin max-w-[1540px] mx-auto w-full flex flex-col gap-space-lg">
    <!-- Top Header Overview -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    Kelompok 6
                </span>
                <span class="text-on-surface-variant font-label-sm text-label-sm tracking-wide uppercase">Dinas Kesehatan Kabupaten</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">SPK Lokasi Pembangunan Puskesmas</h1>
            <p class="font-body-md text-on-surface-variant mt-0.5">Penentuan 1 rekomendasi lokasi terbaik Puskesmas Rawat Inap Baru berbasis metode AHP & TOPSIS</p>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-space-sm self-start md:self-auto">
            <div class="flex items-center gap-space-xs px-3.5 py-2 rounded-xl bg-surface-container-lowest shadow-sm border border-outline-variant/20">
                <span class="material-symbols-outlined text-primary text-[18px]">verified</span>
                <span class="font-label-md text-label-md text-on-surface">Uji AHP: KONSISTEN (CR = {{ number_format($metrics['cr'], 4) }})</span>
            </div>
            <div class="flex items-center gap-space-xs px-3.5 py-2 rounded-xl bg-surface-container-high text-on-surface-variant border border-outline-variant/10">
                <span class="material-symbols-outlined text-[18px] text-primary">location_city</span>
                <span class="font-label-md text-label-md">8 Lokasi Usulan</span>
            </div>
        </div>
    </div>

    <!-- Top KPI Grid: 5 Cards Deck -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-gutter items-stretch">
        <!-- Card 1: Featured Primary Hero Card -->
        <div class="lg:col-span-3 rounded-2xl bg-primary text-on-primary p-space-lg flex flex-col justify-between shadow-xl shadow-primary/20 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-start justify-between relative z-10">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-on-primary">
                    <span class="material-symbols-outlined text-[22px]">domain</span>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-white/20 text-on-primary font-label-sm text-label-sm backdrop-blur-sm">
                    Status: Selesai
                </span>
            </div>
            <div class="mt-space-md relative z-10">
                <p class="font-label-sm text-label-sm text-primary-fixed tracking-wide uppercase">Total Alternatif</p>
                <div class="font-numeric-metric text-numeric-metric text-on-primary mt-0.5 tracking-tight flex items-baseline gap-1.5">
                    <span>{{ $metrics['total_alternatif'] }}</span>
                    <span class="font-headline-sm text-headline-sm font-medium text-white/90">Lokasi Desa/Kel</span>
                </div>
                <p class="font-body-sm text-body-sm text-white/80 mt-1">Dievaluasi untuk 1 lokasi terpilih</p>
            </div>
        </div>

        <!-- Card 2: Total Kriteria Penilaian -->
        <div class="lg:col-span-2 rounded-2xl bg-surface-container-lowest p-space-lg flex flex-col justify-between shadow-sm border border-outline-variant/10 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary-fixed/40 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[22px]">tune</span>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    100%
                </span>
            </div>
            <div class="mt-space-md">
                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Kriteria Bobot</p>
                <div class="font-numeric-metric text-numeric-metric text-on-surface mt-0.5 tracking-tight">{{ $metrics['total_kriteria'] }} Kriteria</div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 truncate">K1 - K6 Pembobotan AHP</p>
            </div>
        </div>

        <!-- Card 3: Model Penilaian Aktif -->
        <div class="lg:col-span-2 rounded-2xl bg-surface-container-lowest p-space-lg flex flex-col justify-between shadow-sm border border-outline-variant/10 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-xl bg-secondary-fixed/50 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[22px]">account_tree</span>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-semibold">
                    AHP + TOPSIS
                </span>
            </div>
            <div class="mt-space-md">
                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Prioritas Utama</p>
                <div class="font-numeric-metric text-numeric-metric text-secondary mt-0.5 tracking-tight text-xl">Jarak Faskes</div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 truncate">Bobot K2: 35.01%</p>
            </div>
        </div>

        <!-- Card 4: Rata-rata Skor Vi -->
        <div class="lg:col-span-2 rounded-2xl bg-surface-container-lowest p-space-lg flex flex-col justify-between shadow-sm border border-outline-variant/10 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[22px]">military_tech</span>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    Pemenang
                </span>
            </div>
            <div class="mt-space-md">
                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Lokasi Rekomendasi #1</p>
                <div class="font-numeric-metric text-numeric-metric text-primary mt-0.5 tracking-tight">
                    {{ $ranked[0]['kode'] ?? '-' }} ({{ isset($ranked[0]) ? round($ranked[0]['vi'] * 100, 2) . '%' : '-' }})
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 truncate">
                    {{ $ranked[0]['alternatif']->nama ?? 'Belum ada alternatif' }}
                </p>
            </div>
        </div>

        <!-- Card 5: Quick Actions Panel -->
        <div class="lg:col-span-3 rounded-2xl bg-surface-container-lowest p-space-lg flex flex-col justify-between shadow-sm border border-outline-variant/10">
            <div class="flex items-center justify-between">
                <span class="font-headline-sm text-headline-sm text-on-surface">Aksi Cepat</span>
                <span class="text-primary font-label-sm text-xs font-semibold">Navigasi SPK</span>
            </div>
            <div class="grid grid-cols-3 gap-2.5 mt-space-md">
                <a href="{{ route('spk.alternatif') }}" class="flex flex-col items-center justify-center py-2.5 px-1.5 rounded-xl bg-primary-fixed/30 hover:bg-primary-fixed/50 transition-colors text-primary group">
                    <div class="w-8 h-8 rounded-lg bg-surface-container-lowest flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform mb-1">
                        <span class="material-symbols-outlined text-[18px]">table_chart</span>
                    </div>
                    <span class="font-label-sm text-[11px] font-semibold truncate">Matriks X</span>
                </a>
                <a href="{{ route('spk.kriteria') }}" class="flex flex-col items-center justify-center py-2.5 px-1.5 rounded-xl bg-secondary-fixed/30 hover:bg-secondary-fixed/50 transition-colors text-secondary group">
                    <div class="w-8 h-8 rounded-lg bg-surface-container-lowest flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform mb-1">
                        <span class="material-symbols-outlined text-[18px]">balance</span>
                    </div>
                    <span class="font-label-sm text-[11px] font-semibold truncate">Bobot AHP</span>
                </a>
                <a href="{{ route('spk.keputusan') }}" class="flex flex-col items-center justify-center py-2.5 px-1.5 rounded-xl bg-surface-container hover:bg-surface-container-high transition-colors text-primary group">
                    <div class="w-8 h-8 rounded-lg bg-primary text-on-primary flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform mb-1">
                        <span class="material-symbols-outlined text-[18px]">military_tech</span>
                    </div>
                    <span class="font-label-sm text-[11px] font-semibold truncate">Keputusan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Analytics Section (3-Column Visual Layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-stretch">
        <!-- Column 1: Smooth Spline Splendor Chart (5 cols) -->
        <div class="lg:col-span-5 rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Distribusi Nilai Preferensi (Vi)</h2>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-primary"></span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Skor TOPSIS</span>
                    </div>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Perbandingan nilai Vi dari 8 alternatif lokasi desa</p>
            </div>

            <!-- Bar Chart of 8 Alternatives -->
            <div class="mt-space-md w-full space-y-2.5">
                @foreach($ranked as $item)
                    <div class="space-y-1">
                        <div class="flex justify-between items-center text-xs font-label-md">
                            <span class="font-semibold text-on-surface">{{ $item['kode'] }} - {{ $item['alternatif']->nama }}</span>
                            <span class="font-bold {{ $item['rank'] == 1 ? 'text-primary' : 'text-on-surface-variant' }}">{{ number_format($item['vi'], 4) }} ({{ $item['kelayakan'] }})</span>
                        </div>
                        <div class="w-full bg-surface-container-high rounded-full h-2.5 overflow-hidden">
                            <div class="{{ $item['rank'] == 1 ? 'bg-primary' : ($item['rank'] <= 3 ? 'bg-secondary' : 'bg-surface-dim') }} h-full rounded-full transition-all duration-500" style="width: {{ $item['vi'] * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-space-sm pt-space-xs flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm border-t border-outline-variant/10">
                <span>Rekomendasi Utama: <strong class="text-primary font-bold">{{ $ranked[0]['kode'] ?? '-' }} ({{ $ranked[0]['alternatif']->nama ?? '-' }})</strong></span>
                <span class="text-on-surface-variant">V = {{ isset($ranked[0]) ? number_format($ranked[0]['vi'], 4) : '0.0000' }}</span>
            </div>
        </div>

        <!-- Column 2: Bobot Kriteria AHP Overview (3 cols) -->
        <div class="lg:col-span-3 rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Prioritas Kriteria AHP</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Hasil sintesis matriks perbandingan</p>
            </div>

            <!-- Kriteria Weights Breakdown -->
            <div class="my-space-md flex flex-col gap-2 max-h-[360px] overflow-y-auto pr-1">
                @php
                    $sortedKriterias = $ahp['kriterias']->sortByDesc(fn($k) => $ahp['weights'][$k->kode] ?? $k->bobot);
                @endphp
                @forelse($sortedKriterias as $k)
                    @php
                        $w = $ahp['weights'][$k->kode] ?? $k->bobot;
                        $isTop = ($loop->first);
                    @endphp
                    <div class="flex items-center justify-between p-2 rounded-xl {{ $isTop ? 'bg-primary-fixed/30 border border-primary/20' : ($k->tipe == 'Cost' ? 'bg-tertiary-fixed/30 border border-tertiary/20' : 'bg-surface-container-low border border-outline-variant/10') }}">
                        <div class="min-w-0 pr-2">
                            <span class="font-label-sm font-bold {{ $isTop ? 'text-primary' : ($k->tipe == 'Cost' ? 'text-tertiary' : 'text-on-surface') }} block truncate">{{ $k->kode }}: {{ $k->nama }}</span>
                            <span class="text-[10px] text-on-surface-variant">{{ $k->tipe }} ({{ $k->satuan ?? '-' }})</span>
                        </div>
                        <span class="font-headline-sm text-xs font-bold shrink-0 {{ $isTop ? 'text-primary' : ($k->tipe == 'Cost' ? 'text-tertiary' : 'text-on-surface') }}">{{ number_format($w * 100, 2) }}%</span>
                    </div>
                @empty
                    <div class="text-xs text-on-surface-variant text-center py-4">Belum ada kriteria.</div>
                @endforelse
            </div>

            <div class="pt-space-xs text-center border-t border-outline-variant/10">
                <span class="font-label-sm text-label-sm text-primary font-bold">Total Akumulasi: {{ number_format(array_sum($ahp['weights']) * 100, 1) }}%</span>
            </div>
        </div>

        <!-- Column 3: Top Ranked Alternatives List (4 cols) -->
        <div class="lg:col-span-4 rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Peringkat Teratas (Top 5)</h2>
                    <a href="{{ route('spk.keputusan') }}" class="text-primary hover:text-primary-container font-label-sm text-label-sm font-semibold flex items-center gap-0.5">
                        <span>Lihat Semua</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Hasil kalkulasi kedekatan relatif TOPSIS (Vi)</p>
            </div>

            <!-- Ranked List Items -->
            <div class="flex flex-col gap-space-sm my-space-md">
                @foreach($topKandidat as $kandidat)
                    <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-surface-container-low transition-colors group {{ $kandidat['rank'] == 1 ? 'bg-primary-fixed/20 border border-primary/20' : '' }}">
                        <div class="flex items-center gap-space-sm min-w-0">
                            <div class="w-9 h-9 rounded-xl {{ $kandidat['rank'] == 1 ? 'bg-primary text-on-primary shadow-sm' : ($kandidat['rank'] == 2 ? 'bg-secondary text-on-secondary' : ($kandidat['rank'] == 3 ? 'bg-tertiary text-on-tertiary' : 'bg-surface-container text-on-surface-variant')) }} flex items-center justify-center font-bold text-label-md shrink-0">
                                #{{ $kandidat['rank'] }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-label-md text-label-md text-on-surface truncate group-hover:text-primary transition-colors font-semibold">
                                    {{ $kandidat['kode'] }} ({{ $kandidat['alternatif']->nama }})
                                </h3>
                                <span class="font-body-sm text-[11px] text-on-surface-variant">D+: {{ number_format($kandidat['d_plus'], 4) }} &bull; D-: {{ number_format($kandidat['d_minus'], 4) }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0 ml-2">
                            <div class="font-headline-sm text-sm text-on-surface font-bold">{{ number_format($kandidat['vi'], 4) }}</div>
                            <span class="inline-block px-2 py-0.5 rounded-full {{ $kandidat['rank'] == 1 ? 'bg-primary text-on-primary' : 'bg-primary-fixed text-on-primary-fixed' }} font-label-sm text-[10px] font-semibold">
                                {{ $kandidat['status'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('spk.keputusan') }}" class="w-full py-2.5 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors font-label-md text-label-md flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">military_tech</span>
                <span>Buka Detail Keputusan Final</span>
            </a>
        </div>
    </div>

    <!-- Profil Tim & Studi Kasus Banner -->
    <div class="rounded-2xl bg-surface-container-low p-space-md flex flex-col md:flex-row md:items-center justify-between gap-space-md shadow-sm border border-outline-variant/10">
        <div class="flex items-center gap-space-md">
            <div class="w-10 h-10 rounded-xl bg-surface-container-lowest flex items-center justify-center text-primary shadow-sm border border-outline-variant/10">
                <span class="material-symbols-outlined text-[20px]">groups</span>
            </div>
            <div>
                <h4 class="font-label-lg text-label-lg text-on-surface font-bold">Kelompok 6 - Soal 6: Dinas Kesehatan (Lokasi Pembangunan Puskesmas)</h4>
                <p class="font-body-sm text-xs text-on-surface-variant mt-0.5">
                    Anggota: Darel Prasetya Fawwaz, Dinathan Fahrezi, Muhammad Romadhoni Alfatih, Rizky Yunia, Nurhidayah
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('spk.kriteria') }}" class="px-4 py-2 rounded-xl bg-surface-container-lowest hover:bg-white text-on-surface font-label-md text-label-md transition-all shadow-sm border border-outline-variant/20">
                Matriks AHP
            </a>
            <a href="{{ route('spk.keputusan') }}" class="px-4 py-2 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-label-md text-label-md transition-all shadow-md shadow-primary/20">
                Hasil TOPSIS
            </a>
        </div>
    </div>
</div>
@endsection
