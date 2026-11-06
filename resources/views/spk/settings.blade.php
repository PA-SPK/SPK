@extends('layouts.app')

@section('title', 'Pengaturan - SPK Decision Suite')
@section('page_title', 'Pengaturan Aplikasi')

@section('content')
<div class="p-margin max-w-[1200px] mx-auto w-full flex flex-col gap-space-lg">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    Konfigurasi & Manajemen
                </span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Pengaturan Aplikasi</h1>
            <p class="font-body-md text-on-surface-variant mt-0.5">Kelola data, muat contoh studi kasus, dan lihat informasi sistem SPK</p>
        </div>
        <a href="{{ route('spk.import') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-sm hover:bg-primary/90 shadow-md shadow-primary/20 transition-all font-semibold self-start">
            <span class="material-symbols-outlined text-[20px]">upload_file</span>
            <span>Import Data Baru</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
        <!-- Left Column: Danger Zone + Demo Data -->
        <div class="lg:col-span-2 flex flex-col gap-space-lg">

            <!-- Data Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-space-sm">
                <div class="rounded-2xl bg-surface-container-lowest p-space-md shadow-sm border border-outline-variant/10 flex flex-col items-center justify-center text-center">
                    <span class="material-symbols-outlined text-primary text-[28px] mb-1">tune</span>
                    <div class="font-numeric-metric text-2xl text-on-surface font-bold">{{ $stats['kriteria'] }}</div>
                    <span class="font-label-sm text-[11px] text-on-surface-variant mt-0.5">Kriteria</span>
                </div>
                <div class="rounded-2xl bg-surface-container-lowest p-space-md shadow-sm border border-outline-variant/10 flex flex-col items-center justify-center text-center">
                    <span class="material-symbols-outlined text-secondary text-[28px] mb-1">domain</span>
                    <div class="font-numeric-metric text-2xl text-on-surface font-bold">{{ $stats['alternatif'] }}</div>
                    <span class="font-label-sm text-[11px] text-on-surface-variant mt-0.5">Alternatif</span>
                </div>
                <div class="rounded-2xl bg-surface-container-lowest p-space-md shadow-sm border border-outline-variant/10 flex flex-col items-center justify-center text-center">
                    <span class="material-symbols-outlined text-tertiary text-[28px] mb-1">grid_on</span>
                    <div class="font-numeric-metric text-2xl text-on-surface font-bold">{{ $stats['nilai'] }}</div>
                    <span class="font-label-sm text-[11px] text-on-surface-variant mt-0.5">Nilai Matriks</span>
                </div>
                <div class="rounded-2xl bg-surface-container-lowest p-space-md shadow-sm border border-outline-variant/10 flex flex-col items-center justify-center text-center">
                    <span class="material-symbols-outlined text-on-surface-variant text-[28px] mb-1">compare_arrows</span>
                    <div class="font-numeric-metric text-2xl text-on-surface font-bold">{{ $stats['perbandingan'] }}</div>
                    <span class="font-label-sm text-[11px] text-on-surface-variant mt-0.5">Perbandingan AHP</span>
                </div>
            </div>

            <!-- Load Demo Data -->
            <div class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm border border-outline-variant/10">
                <div class="flex items-start gap-space-md">
                    <div class="w-12 h-12 rounded-2xl bg-primary-fixed/30 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[26px]">dataset</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Muat Data Demo</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                            Muat dataset contoh untuk mencoba fitur perhitungan SPK (AHP & TOPSIS). 
                            Data mencakup 5 kriteria dan 8 alternatif dengan matriks perbandingan AHP yang telah terkalibrasi.
                        </p>
                        <div class="flex flex-wrap gap-2 mt-space-sm">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-primary-fixed/30 text-primary font-label-sm text-xs font-semibold">
                                <span class="material-symbols-outlined text-[13px]">tune</span> 5 Kriteria
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-secondary-fixed/30 text-secondary font-label-sm text-xs font-semibold">
                                <span class="material-symbols-outlined text-[13px]">domain</span> 8 Alternatif
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-xs font-semibold">
                                <span class="material-symbols-outlined text-[13px]">verified</span> AHP Terkalibrasi
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-space-md pt-space-sm border-t border-outline-variant/10">
                    <form method="POST" action="{{ route('spk.settings.seed') }}" onsubmit="return confirm('Muat data demo? Data yang ada saat ini akan diganti.')">
                        @csrf
                        @if($stats['kriteria'] > 0 || $stats['alternatif'] > 0)
                            <p class="text-xs text-amber-600 font-semibold mb-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">warning</span>
                                Data yang ada saat ini ({{ $stats['kriteria'] }} kriteria, {{ $stats['alternatif'] }} alternatif) akan digantikan.
                            </p>
                        @endif
                        <button type="submit" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary font-label-md text-sm hover:bg-primary/90 shadow-md shadow-primary/20 transition-all font-semibold">
                            <span class="material-symbols-outlined text-[20px]">play_arrow</span>
                            <span>Muat Data Demo</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Danger Zone: Reset Data -->
            <div class="rounded-2xl bg-error/5 p-space-lg border-2 border-error/20">
                <div class="flex items-start gap-space-md">
                    <div class="w-12 h-12 rounded-2xl bg-error/10 flex items-center justify-center text-error shrink-0">
                        <span class="material-symbols-outlined text-[26px]">delete_sweep</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-headline-sm text-headline-sm text-error font-bold">Zona Berbahaya</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                            Kosongkan seluruh data SPK dari database: kriteria, alternatif, nilai matriks, dan perbandingan AHP. 
                            <strong class="text-error">Tindakan ini tidak dapat dibatalkan.</strong>
                        </p>
                    </div>
                </div>

                <div class="mt-space-md pt-space-sm border-t border-error/10">
                    @if($stats['kriteria'] === 0 && $stats['alternatif'] === 0)
                        <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-sm">
                            <span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>
                            Database sudah kosong — tidak ada data untuk dihapus.
                        </div>
                    @else
                        <form method="POST" action="{{ route('spk.settings.reset') }}" 
                              onsubmit="return confirmReset(event)">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center gap-space-sm">
                                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-error text-white font-label-md text-sm hover:bg-error/90 shadow-md shadow-error/20 transition-all font-semibold">
                                    <span class="material-symbols-outlined text-[20px]">delete_forever</span>
                                    <span>Kosongkan Semua Data</span>
                                </button>
                                <span class="font-body-sm text-xs text-on-surface-variant">
                                    Akan menghapus {{ $stats['kriteria'] }} kriteria, {{ $stats['alternatif'] }} alternatif, {{ $stats['nilai'] }} nilai matriks
                                </span>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column: System Info + Quick Links -->
        <div class="flex flex-col gap-space-lg">

            <!-- System Info -->
            <div class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm border border-outline-variant/10">
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-space-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-primary">info</span>
                    Informasi Sistem
                </h3>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between py-2 border-b border-outline-variant/10">
                        <span class="font-label-sm text-sm text-on-surface-variant">PHP Version</span>
                        <span class="font-mono text-xs text-on-surface font-bold bg-surface-container px-2 py-0.5 rounded-lg">{{ $systemInfo['php_version'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-outline-variant/10">
                        <span class="font-label-sm text-sm text-on-surface-variant">Laravel</span>
                        <span class="font-mono text-xs text-on-surface font-bold bg-surface-container px-2 py-0.5 rounded-lg">v{{ $systemInfo['laravel_version'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-outline-variant/10">
                        <span class="font-label-sm text-sm text-on-surface-variant">Metode SPK</span>
                        <span class="font-mono text-xs text-primary font-bold bg-primary-fixed/30 px-2 py-0.5 rounded-lg">AHP + TOPSIS</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="font-label-sm text-sm text-on-surface-variant">Environment</span>
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-lg {{ $systemInfo['env'] === 'production' ? 'bg-error/10 text-error' : 'bg-surface-container text-on-surface' }}">{{ $systemInfo['env'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="rounded-2xl bg-surface-container-lowest p-space-lg shadow-sm border border-outline-variant/10">
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-space-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-primary">link</span>
                    Navigasi Cepat
                </h3>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('spk.import') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors group">
                        <span class="material-symbols-outlined text-primary text-[20px]">upload_file</span>
                        <div>
                            <div class="font-label-md text-sm text-on-surface font-semibold group-hover:text-primary transition-colors">Import Data Excel/CSV</div>
                            <div class="font-body-sm text-xs text-on-surface-variant">Upload file untuk mengisi data SPK</div>
                        </div>
                    </a>
                    <a href="{{ route('spk.import.template') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors group">
                        <span class="material-symbols-outlined text-secondary text-[20px]">download</span>
                        <div>
                            <div class="font-label-md text-sm text-on-surface font-semibold group-hover:text-primary transition-colors">Unduh Template Excel</div>
                            <div class="font-body-sm text-xs text-on-surface-variant">Format file yang dapat diisi</div>
                        </div>
                    </a>
                    <a href="{{ route('spk.kriteria') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors group">
                        <span class="material-symbols-outlined text-on-surface-variant text-[20px]">tune</span>
                        <div>
                            <div class="font-label-md text-sm text-on-surface font-semibold group-hover:text-primary transition-colors">Kelola Kriteria AHP</div>
                            <div class="font-body-sm text-xs text-on-surface-variant">Tambah, edit, atur bobot kriteria</div>
                        </div>
                    </a>
                    <a href="{{ route('spk.keputusan') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface-container-low transition-colors group">
                        <span class="material-symbols-outlined text-on-surface-variant text-[20px]">military_tech</span>
                        <div>
                            <div class="font-label-md text-sm text-on-surface font-semibold group-hover:text-primary transition-colors">Lihat Hasil Keputusan</div>
                            <div class="font-body-sm text-xs text-on-surface-variant">Perankingan akhir TOPSIS</div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function confirmReset(e) {
    const confirmed = window.confirm(
        'PERHATIAN!\n\nAnda akan menghapus SEMUA data SPK secara permanen:\n' +
        '• Semua kriteria\n• Semua alternatif\n• Semua nilai matriks\n• Semua perbandingan AHP\n\n' +
        'Tindakan ini TIDAK DAPAT DIBATALKAN.\n\nKetik OK untuk melanjutkan.'
    );
    if (!confirmed) {
        e.preventDefault();
        return false;
    }
    return true;
}
</script>
@endsection

