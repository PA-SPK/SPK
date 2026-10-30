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
            <p class="font-body-md text-on-surface-variant mt-0.5">Kelola alternatif lokasi dan nilai evaluasi terhadap seluruh kriteria sebelum normalisasi TOPSIS</p>
        </div>
        <div class="flex flex-wrap items-center gap-space-sm">
            <button type="button" onclick="openAddAltModal()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-label-md text-sm border border-outline-variant/20 shadow-xs transition-all font-semibold">
                <span class="material-symbols-outlined text-[20px] text-primary">add_location_alt</span>
                <span>Tambah Alternatif</span>
            </button>
            <a href="{{ route('spk.keputusan') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-sm hover:bg-primary/90 shadow-md shadow-primary/20 transition-all font-semibold">
                <span>Hitung Ranking TOPSIS</span>
                <span class="material-symbols-outlined text-[20px]">play_arrow</span>
            </a>
        </div>
    </div>

    @php
        $totalAlt = count($alternatifs);
        $totalKrit = count($kriterias);
        $totalCells = $totalAlt * $totalKrit;
        $benefitCount = $kriterias->where('tipe', 'Benefit')->count();
        $costCount = $kriterias->where('tipe', 'Cost')->count();
    @endphp

    <!-- 4 Top KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter items-stretch">
        <!-- Card 1: Total Alternatif -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <span class="p-2.5 rounded-xl bg-primary-fixed/40 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[24px]">location_on</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    {{ $totalAlt }} Lokasi
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">{{ $totalAlt }} Alternatif</div>
                <div class="font-label-lg text-label-lg text-on-surface-variant font-medium mt-0.5">Total Alternatif Usulan</div>
                <p class="font-body-sm text-body-sm text-outline mt-1">Dapat ditambah/dikurangi secara dinamis</p>
            </div>
        </div>

        <!-- Card 2: Kelengkapan Matriks -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <span class="p-2.5 rounded-xl bg-primary-fixed/40 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[24px]">fact_check</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm font-semibold">
                    {{ $totalCells }} Data Sel
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">{{ $totalAlt }} &times; {{ $totalKrit }} Sel</div>
                <div class="font-label-lg text-label-lg text-on-surface-variant font-medium mt-0.5">Dimensi Matriks Keputusan</div>
                <p class="font-body-sm text-body-sm text-outline mt-1">Nilai matriks dapat diedit langsung</p>
            </div>
        </div>

        <!-- Card 3: Benefit vs Cost -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <span class="p-2.5 rounded-xl bg-secondary-fixed/50 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-[24px]">balance</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-secondary font-label-sm text-label-sm font-semibold">
                    {{ $benefitCount }} Benefit / {{ $costCount }} Cost
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">{{ $benefitCount }} B &bull; {{ $costCount }} C</div>
                <div class="font-label-lg text-label-lg text-on-surface-variant font-medium mt-0.5">Komposisi Parameter Kriteria</div>
                <p class="font-body-sm text-body-sm text-outline mt-1">Sesuai klasifikasi AHP</p>
            </div>
        </div>

        <!-- Card 4: Normalisasi TOPSIS Status -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-outline-variant/10 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <span class="p-2.5 rounded-xl bg-tertiary-fixed flex items-center justify-center text-tertiary">
                    <span class="material-symbols-outlined text-[24px]">calculate</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-semibold">
                    Auto-Sync
                </span>
            </div>
            <div class="mt-space-md">
                <div class="font-headline-lg text-headline-lg text-on-surface font-bold">Vektor &radic;&Sigma;x&sup2;</div>
                <div class="font-label-lg text-label-lg text-on-surface-variant font-medium mt-0.5">Pembagi Normalisasi Euclidean</div>
                <p class="font-body-sm text-body-sm text-outline mt-1">Otomatis update saat nilai diubah</p>
            </div>
        </div>
    </div>

    <!-- Interactive Decision Matrix (Matriks X) Container with Form -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden flex flex-col">
        <form action="{{ route('spk.alternatif.nilai') }}" method="POST">
            @csrf
            <!-- Header of the Table Card -->
            <div class="p-space-lg flex flex-col lg:flex-row lg:items-center justify-between gap-space-md border-b border-outline-variant/10">
                <div class="flex items-center gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-on-primary-fixed">
                        <span class="material-symbols-outlined text-[22px]">table_chart</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Tabel Matriks Keputusan (Alternatif X)</h2>
                        <p class="font-body-sm text-body-sm text-outline">Anda dapat langsung mengedit nilai di dalam tabel dan menekan tombol simpan di bawah/atas.</p>
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
                    @if(count($alternatifs) > 0 && count($kriterias) > 0)
                    <button type="submit" class="flex items-center gap-1.5 px-4 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary/90 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan Nilai Matriks</span>
                    </button>
                    @endif
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
                            <th class="py-3.5 px-4 min-w-[200px] sticky left-14 z-20 bg-surface-container-low font-label-md text-label-md">
                                Lokasi Usulan
                            </th>
                            @foreach($kriterias as $k)
                                <th class="py-3.5 px-3 text-center {{ $k->tipe == 'Cost' ? 'bg-tertiary-fixed/30 text-tertiary' : 'text-primary' }}">
                                    <div class="flex flex-col items-center">
                                        <span class="font-bold text-sm">{{ $k->kode }}</span>
                                        <span class="font-label-sm text-[11px] text-on-surface font-semibold">{{ $k->nama }}</span>
                                        <span class="text-[10px] text-outline font-normal">({{ $k->tipe }}) &bull; {{ $k->satuan ?? '-' }}</span>
                                    </div>
                                </th>
                            @endforeach
                            <th class="py-3.5 px-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10 text-on-surface" id="alternatif-table-body">
                        @forelse($alternatifs as $alt)
                            <tr class="hover:bg-surface-container-low/70 transition-colors group">
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
                                        $isCost = ($k->tipe === 'Cost');
                                    @endphp
                                    <td class="py-2 px-2 text-center {{ $isCost ? 'bg-tertiary-fixed/5' : '' }}">
                                        <input type="number" step="any"
                                            name="nilai[{{ $alt->id }}][{{ $k->id }}]"
                                            value="{{ $score }}"
                                            class="w-20 px-2 py-1 text-center bg-surface-container-low border border-outline-variant/30 rounded-lg text-xs font-mono font-bold text-on-surface focus:outline-primary focus:bg-white"
                                            required />
                                    </td>
                                @endforeach
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button" onclick="openEditAltModal({{ json_encode($alt) }})" class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-high transition-colors" title="Edit Alternatif">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button type="button" onclick="confirmDeleteAlt({{ $alt->id }}, '{{ $alt->kode }}', '{{ addslashes($alt->nama) }}')" class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error/10 transition-colors" title="Hapus Alternatif">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($kriterias) + 3 }}" class="py-8 text-center text-on-surface-variant">
                                    Belum ada alternatif yang ditambahkan. Silakan klik tombol "Tambah Alternatif".
                                </td>
                            </tr>
                        @endforelse

                        @if(count($alternatifs) > 0 && count($kriterias) > 0)
                        <!-- Row Pembagi Vektor: sqrt(sum(x^2)) -->
                        <tr class="bg-surface-container font-bold font-mono text-xs">
                            <td class="py-3.5 px-4 text-center text-primary sticky left-0 z-10 bg-surface-container">&radic;&Sigma;(x<sub>ij</sub>&sup2;)</td>
                            <td class="py-3.5 px-4 font-sans text-on-surface sticky left-14 z-10 bg-surface-container">
                                Pembagi Vektor Normalisasi Euclidean
                            </td>
                            @foreach($kriterias as $k)
                                <td class="py-3.5 px-3 text-center text-primary">
                                    {{ number_format($divisors[$k->kode] ?? 0, 4) }}
                                </td>
                            @endforeach
                            <td class="py-3.5 px-4"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Bottom Action Strip -->
            <div class="p-space-md bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-md border-t border-outline-variant/10">
                <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-sm">
                    <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                    <span>Setiap perubahan nilai di atas akan otomatis memperbarui perhitungan normalisasi & ranking TOPSIS.</span>
                </div>
                <div class="flex items-center gap-3">
                    @if(count($alternatifs) > 0 && count($kriterias) > 0)
                    <button type="submit" class="flex items-center gap-1.5 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-label-md text-sm font-bold hover:bg-primary/90 shadow-md shadow-primary/20 transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Perubahan Nilai Matriks</span>
                    </button>
                    @endif
                    <a href="{{ route('spk.keputusan') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-md text-sm hover:bg-surface-container-highest transition-all font-semibold">
                        <span>Hasil Keputusan</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Alternatif -->
<div id="modalAddAlternatif" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-6 shadow-xl border border-outline-variant/20 max-h-[90vh] overflow-y-auto animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/10">
            <h3 class="font-headline-sm text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">add_location_alt</span>
                Tambah Alternatif Baru
            </h3>
            <button type="button" onclick="closeAddAltModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('spk.alternatif.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kode Alternatif</label>
                <input type="text" name="kode" placeholder="Misal: L9" required maxlength="10" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm font-mono uppercase focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Nama Lokasi / Desa / Kelurahan</label>
                <input type="text" name="nama" placeholder="Misal: Desa Harapan Makmur" required class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kategori Wilayah</label>
                <input type="text" name="kategori" value="Desa" placeholder="Desa / Kelurahan" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Deskripsi / Catatan Lokasi</label>
                <textarea name="deskripsi" rows="2" placeholder="Catatan kondisi lokasi, akses, dsb" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary"></textarea>
            </div>

            <!-- Initial Criteria Values Input -->
            @if(count($kriterias) > 0)
            <div class="pt-2 border-t border-outline-variant/10">
                <h4 class="text-xs font-bold text-on-surface mb-2">Nilai Awal Kriteria Alternatif Ini:</h4>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($kriterias as $k)
                    <div>
                        <label class="block text-[11px] font-semibold text-on-surface-variant mb-0.5">{{ $k->kode }} - {{ $k->nama }} ({{ $k->satuan ?? '-' }})</label>
                        <input type="number" step="any" name="nilai[{{ $k->kode }}]" value="0" required class="w-full px-2.5 py-1.5 bg-surface-container-low border border-outline-variant/30 rounded-lg text-xs font-mono font-bold focus:outline-primary" />
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline-variant/10">
                <button type="button" onclick="closeAddAltModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary/90 shadow-sm">Simpan Alternatif</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Alternatif -->
<div id="modalEditAlternatif" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full p-6 shadow-xl border border-outline-variant/20 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/10">
            <h3 class="font-headline-sm text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">edit</span>
                Edit Detail Alternatif
            </h3>
            <button type="button" onclick="closeEditAltModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="formEditAlternatif" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kode Alternatif</label>
                <input type="text" name="kode" id="edit_alt_kode" required maxlength="10" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm font-mono uppercase focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Nama Lokasi</label>
                <input type="text" name="nama" id="edit_alt_nama" required class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kategori Wilayah</label>
                <input type="text" name="kategori" id="edit_alt_kategori" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="edit_alt_deskripsi" rows="3" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant mb-1">Urutan</label>
                <input type="number" name="urutan" id="edit_alt_urutan" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:outline-primary" />
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-outline-variant/10">
                <button type="button" onclick="closeEditAltModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-5 py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary/90 shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddAltModal() {
        document.getElementById('modalAddAlternatif').classList.remove('hidden');
    }
    function closeAddAltModal() {
        document.getElementById('modalAddAlternatif').classList.add('hidden');
    }

    function openEditAltModal(alt) {
        document.getElementById('formEditAlternatif').action = `/alternatif/${alt.id}`;
        document.getElementById('edit_alt_kode').value = alt.kode;
        document.getElementById('edit_alt_nama').value = alt.nama;
        document.getElementById('edit_alt_kategori').value = alt.kategori || 'Desa';
        document.getElementById('edit_alt_deskripsi').value = alt.deskripsi || '';
        document.getElementById('edit_alt_urutan').value = alt.urutan || 1;
        document.getElementById('modalEditAlternatif').classList.remove('hidden');
    }
    function closeEditAltModal() {
        document.getElementById('modalEditAlternatif').classList.add('hidden');
    }

    function confirmDeleteAlt(id, kode, nama) {
        openDeleteConfirm(
            `/alternatif/${id}`,
            `Hapus Alternatif ${kode}`,
            `Apakah Anda yakin ingin menghapus alternatif ${kode} (${nama})? Seluruh data nilai kriteria terkait alternatif ini akan ikut terhapus secara permanen.`
        );
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterInput = document.getElementById('alternatif-filter');
        const tableBody = document.getElementById('alternatif-table-body');
        if (filterInput && tableBody) {
            filterInput.addEventListener('input', function(e) {
                const val = e.target.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');
                const len = rows.length > 1 ? rows.length - 1 : rows.length;
                for (let i = 0; i < len; i++) {
                    const text = rows[i].innerText.toLowerCase();
                    rows[i].style.display = text.includes(val) ? '' : 'none';
                }
            });
        }
    });
</script>
@endsection
