@extends('layouts.app')

@section('title', 'Alternatif & Matriks Keputusan - SPK Puskesmas')
@section('page_title', 'Alternatif & Matriks Keputusan (X)')

@section('content')
<div class="p-margin max-w-[1540px] mx-auto w-full flex flex-col gap-space-lg">
    <!-- Top Header Overview -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    Tahap 2: Dataset Matriks Keputusan (X)
                </span>
                <span class="text-on-surface-variant font-label-sm text-label-sm tracking-wide uppercase">Dinas Kesehatan Kabupaten</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Matriks Keputusan Awal Alternatif</h1>
            <p class="font-body-md text-on-surface-variant mt-0.5">Nilai evaluasi riil 8 kandidat desa/kelurahan usulan terhadap 6 kriteria sebelum normalisasi TOPSIS</p>
        </div>
        <div class="flex items-center gap-space-sm">
            <a href="{{ route('spk.keputusan') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-sm hover:bg-primary-container shadow-md shadow-primary/20 transition-all font-semibold">
                <span>Hitung Ranking TOPSIS</span>
                <span class="material-symbols-outlined text-[20px]">play_arrow</span>
            </a>
        </div>
    </div>

    <!-- 4 Top KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter items-stretch">
        <!-- Card 1: Total Alternatif -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <span class="p-2.5 rounded-xl bg-primary-fixed/40 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[24px]">location_on</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    8 Lokasi
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">8 Desa/Kel</div>
                <div class="font-label-lg text-label-lg text-on-surface-variant font-medium mt-0.5">Total Alternatif Usulan</div>
                <p class="font-body-sm text-body-sm text-outline mt-1">L1 s.d L8 Terdaftar Lengkap</p>
            </div>
        </div>

        <!-- Card 2: Kelengkapan Matriks -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <span class="p-2.5 rounded-xl bg-primary-fixed/40 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[24px]">fact_check</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm font-semibold">
                    100% Valid
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">48 Nilai (8x6)</div>
                <div class="font-label-lg text-label-lg text-on-surface-variant font-medium mt-0.5">Kelengkapan Sel Matriks</div>
                <p class="font-body-sm text-body-sm text-outline mt-1">Seluruh data kriteria terisi penuh</p>
            </div>
        </div>

        <!-- Card 3: Benefit vs Cost -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <span class="p-2.5 rounded-xl bg-secondary-fixed/50 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[24px]">balance</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-secondary font-label-sm text-label-sm font-semibold">
                    5 Benefit / 1 Cost
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">K5 (Cost)</div>
                <div class="font-label-lg text-label-lg text-on-surface-variant font-medium mt-0.5">Biaya Lahan Terendah</div>
                <p class="font-body-sm text-body-sm text-outline mt-1">L4 Tanjung Harapan (250 Juta)</p>
            </div>
        </div>

        <!-- Card 4: Jarak Terjauh -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <span class="p-2.5 rounded-xl bg-tertiary-fixed flex items-center justify-center text-tertiary">
                    <span class="material-symbols-outlined text-[24px]">distance</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-semibold">
                    K2 Tertinggi
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">25 Km (L4)</div>
                <div class="font-label-lg text-label-lg text-on-surface-variant font-medium mt-0.5">Jarak Faskes Terjauh</div>
                <p class="font-body-sm text-body-sm text-outline mt-1">Paling membutuhkan Puskesmas</p>
            </div>
        </div>
    </div>

    <!-- Interactive Decision Matrix (Matriks X) Container -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden flex flex-col">
        <!-- Header of the Table Card -->
        <div class="p-space-lg flex flex-col lg:flex-row lg:items-center justify-between gap-space-md border-b border-outline-variant/10">
            <div class="flex items-center gap-space-sm">
                <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-on-primary-fixed">
                    <span class="material-symbols-outlined text-[22px]">table_chart</span>
                </div>
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Tabel Matriks Keputusan (Alternatif X)</h2>
                    <p class="font-body-sm text-body-sm text-outline">Nilai murni evaluasi sebelum normalisasi Euclidean TOPSIS (r<sub>ij</sub> = x<sub>ij</sub> / &radic;&Sigma;x<sub>ij</sub>&sup2;)</p>
                </div>
            </div>

            <!-- Quick Legend & Action Badges -->
            <div class="flex flex-wrap items-center gap-space-sm">
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm font-medium">
                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                    <span>Benefit (Maksimum)</span>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-medium">
                    <span class="w-2 h-2 rounded-full bg-tertiary"></span>
                    <span>Cost (Minimum)</span>
                </div>
                <div class="flex items-center bg-surface-container-low rounded-xl px-space-sm py-1.5 text-on-surface-variant border border-outline-variant/20 ml-2">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    <input type="text" id="alternatif-filter" placeholder="Cari desa/kel..." class="bg-transparent border-0 outline-none text-on-surface font-body-sm text-body-sm px-1.5 w-32 focus:w-44 transition-all"/>
                </div>
            </div>
        </div>

        <!-- Matrix Table -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm">
                        <th class="py-3.5 px-4 w-14 sticky left-0 z-20 bg-surface-container-low text-center">
                            Kode
                        </th>
                        <th class="py-3.5 px-4 min-w-[220px] sticky left-14 z-20 bg-surface-container-low font-label-md text-label-md">
                            Lokasi Usulan
                        </th>
                        @foreach($kriterias as $k)
                            <th class="py-3.5 px-4 text-center {{ $k->tipe == 'Cost' ? 'bg-tertiary-fixed/30 text-tertiary' : 'text-primary' }}">
                                <div class="flex flex-col items-center">
                                    <span class="font-bold text-sm">{{ $k->kode }}</span>
                                    <span class="font-label-sm text-[11px] text-on-surface font-semibold">{{ $k->nama }}</span>
                                    <span class="text-[10px] text-outline font-normal">({{ $k->tipe }}) &bull; {{ $k->satuan }}</span>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10 text-on-surface" id="alternatif-table-body">
                    @foreach($alternatifs as $alt)
                        <tr class="hover:bg-surface-container-low/70 transition-colors group {{ $alt->kode == 'L4' ? 'bg-primary-fixed/15 font-semibold' : '' }}">
                            <td class="py-3.5 px-4 text-center font-bold font-mono text-primary sticky left-0 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/70">
                                {{ $alt->kode }}
                            </td>
                            <td class="py-3.5 px-4 sticky left-14 z-10 bg-surface-container-lowest group-hover:bg-surface-container-low/70">
                                <div class="flex flex-col">
                                    <span class="font-headline-sm text-sm text-on-surface font-semibold group-hover:text-primary transition-colors">
                                        {{ $alt->nama }}
                                    </span>
                                    <span class="font-body-sm text-[11px] text-outline">
                                        {{ $alt->kategori }} &bull; {{ $alt->deskripsi }}
                                    </span>
                                </div>
                            </td>
                            @foreach($kriterias as $k)
                                @php
                                    $score = $matrixX[$alt->kode]['scores'][$k->kode] ?? 0;
                                    $isCost = $k->tipe === 'Cost';
                                    $isTopL4 = ($alt->kode === 'L4' && ($k->kode === 'K2' || $k->kode === 'K5'));
                                @endphp
                                <td class="py-3.5 px-4 text-center font-mono font-medium {{ $isCost ? 'bg-tertiary-fixed/10 text-tertiary font-bold' : '' }} {{ $isTopL4 ? 'bg-primary-fixed/30 text-primary font-bold' : '' }}">
                                    {{ $score }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    <!-- Row Pembagi Vektor: sqrt(sum(x^2)) -->
                    <tr class="bg-surface-container font-bold font-mono text-xs">
                        <td class="py-3.5 px-4 text-center text-primary sticky left-0 z-10 bg-surface-container">&radic;&Sigma;(x<sub>ij</sub>&sup2;)</td>
                        <td class="py-3.5 px-4 font-sans text-on-surface sticky left-14 z-10 bg-surface-container">
                            Pembagi Vektor Normalisasi
                        </td>
                        @foreach($kriterias as $k)
                            <td class="py-3.5 px-4 text-center text-primary">
                                {{ number_format($divisors[$k->kode] ?? 0, 4) }}
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bottom Action Strip -->
        <div class="p-space-md bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-md border-t border-outline-variant/10">
            <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                <span>Dataset riil Kelompok 6: Dinas Kesehatan (Puskesmas Rawat Inap Baru)</span>
            </div>
            <a href="{{ route('spk.keputusan') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-sm hover:bg-primary-container shadow-md shadow-primary/20 transition-all font-semibold">
                <span>Lihat Hasil & Rekomendasi Keputusan</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterInput = document.getElementById('alternatif-filter');
        const tableBody = document.getElementById('alternatif-table-body');
        if (filterInput && tableBody) {
            filterInput.addEventListener('input', function(e) {
                const val = e.target.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');
                for (let i = 0; i < rows.length - 1; i++) { // Kecualikan baris pembagi terakhir
                    const text = rows[i].innerText.toLowerCase();
                    rows[i].style.display = text.includes(val) ? '' : 'none';
                }
            });
        }
    });
</script>
@endsection
