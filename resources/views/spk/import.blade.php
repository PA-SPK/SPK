@extends('layouts.app')

@section('title', 'Import Data SPK - Muat Dataset Baru')
@section('page_title', 'Import & Setup Data SPK')

@section('content')
<div class="p-margin max-w-[1540px] mx-auto w-full flex flex-col gap-space-lg">

    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
                    Setup Awal / Muat Ulang Dataset
                </span>
                <span class="text-on-surface-variant font-label-sm text-label-sm tracking-wide uppercase">Sistem Pendukung Keputusan</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Import Data SPK</h1>
            <p class="font-body-md text-on-surface-variant mt-0.5">Muat dataset kriteria dan alternatif dari file Excel (.xlsx) atau CSV secara otomatis — struktur tabel dibaca langsung dari file Anda.</p>
        </div>
        <div class="flex items-center gap-space-sm">
            <a href="{{ route('spk.import.template') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-label-md text-sm border border-outline-variant/20 shadow-xs transition-all font-semibold">
                <span class="material-symbols-outlined text-[19px] text-primary">download</span>
                <span>Unduh Template Excel</span>
            </a>
            @if($hasData)
            <a href="{{ route('spk.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-md text-sm border border-outline-variant/20 shadow-xs transition-all font-semibold">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali ke Dashboard</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Main 2-col grid -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">

        <!-- Left: Upload Form (8 cols) -->
        <div class="xl:col-span-8 space-y-space-md">

            <!-- Upload Form Card -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden">
                <form action="{{ route('spk.import') }}" method="POST" enctype="multipart/form-data" id="form-import">
                    @csrf
                    <!-- Card Header -->
                    <div class="p-space-lg border-b border-outline-variant/10 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[24px]">upload_file</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Unggah File Dataset</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Format yang didukung: <strong>.xlsx</strong> (Excel) atau <strong>.csv</strong></p>
                        </div>
                    </div>

                    <div class="p-space-lg space-y-space-md">
                        <!-- Drop Zone -->
                        <div id="drop-zone" class="relative border-2 border-dashed border-outline-variant/50 rounded-2xl p-10 text-center hover:border-primary/50 hover:bg-primary/5 transition-all cursor-pointer group">
                            <input type="file" name="file_spk" id="file-input" accept=".xlsx,.xls,.csv,.txt" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                            <div id="drop-idle" class="pointer-events-none">
                                <span class="material-symbols-outlined text-[52px] text-primary/50 group-hover:text-primary transition-colors">drive_folder_upload</span>
                                <p class="font-headline-sm text-headline-sm text-on-surface mt-3 font-semibold">Klik atau Seret File ke Sini</p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Mendukung: <code class="px-1.5 py-0.5 rounded bg-surface-container text-primary font-mono text-xs font-bold">.xlsx</code> <code class="px-1.5 py-0.5 rounded bg-surface-container text-primary font-mono text-xs font-bold">.xls</code> <code class="px-1.5 py-0.5 rounded bg-surface-container text-primary font-mono text-xs font-bold">.csv</code></p>
                                <p class="font-label-sm text-label-sm text-outline mt-2">Maksimum ukuran file: 10 MB</p>
                            </div>
                            <div id="drop-selected" class="hidden pointer-events-none">
                                <span class="material-symbols-outlined text-[48px] text-primary">task_alt</span>
                                <p class="font-headline-sm text-headline-sm text-primary mt-2 font-bold" id="file-name-label">-</p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1" id="file-size-label">-</p>
                            </div>
                        </div>

                        <!-- Mode Radio -->
                        <div>
                            <label class="block text-xs font-bold text-on-surface mb-2">Mode Import</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <label class="mode-card flex items-start gap-3 p-3.5 rounded-xl border-2 border-primary bg-primary-fixed/25 cursor-pointer transition-all">
                                    <input type="radio" name="mode" value="replace" checked class="mt-0.5 accent-primary" />
                                    <div>
                                        <span class="font-label-md text-label-md text-on-surface font-bold block">Ganti Semua Data</span>
                                        <span class="font-body-sm text-xs text-on-surface-variant leading-relaxed">Hapus seluruh data lama (kriteria, alternatif, perbandingan AHP) dan ganti dengan data dari file ini.</span>
                                        <span class="mt-1 inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-error/10 text-error">Reset & Fresh Start</span>
                                    </div>
                                </label>
                                <label class="mode-card flex items-start gap-3 p-3.5 rounded-xl border-2 border-outline-variant/30 bg-surface-container-low cursor-pointer transition-all">
                                    <input type="radio" name="mode" value="merge" class="mt-0.5 accent-primary" />
                                    <div>
                                        <span class="font-label-md text-label-md text-on-surface font-bold block">Tambah / Perbarui</span>
                                        <span class="font-body-sm text-xs text-on-surface-variant leading-relaxed">Data baru ditambahkan. Data yang sudah ada (sama kodenya) akan diperbarui nilainya.</span>
                                        <span class="mt-1 inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Merge & Update</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="pt-2">
                            <button type="submit" id="btn-import" disabled class="w-full flex items-center justify-center gap-2 py-3.5 px-6 bg-primary text-on-primary rounded-xl font-label-lg text-label-lg font-bold hover:bg-primary/90 shadow-lg shadow-primary/25 transition-all disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                                <span class="material-symbols-outlined text-[22px]">upload</span>
                                <span id="btn-label">Pilih file dulu untuk melanjutkan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Steps + Current Stats (4 cols) -->
        <div class="xl:col-span-4 space-y-space-md">

            <!-- Step Guide -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 p-space-lg">
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-space-md">Alur Penggunaan SPK</h3>
                <ol class="space-y-3">
                    @php $steps = [
                        ['icon' => 'upload_file', 'title' => 'Import File Excel', 'desc' => 'Muat kriteria dan alternatif dari satu file Excel.', 'done' => false],
                        ['icon' => 'balance', 'title' => 'Atur Bobot', 'desc' => 'Isi matriks perbandingan berpasangan untuk menghitung bobot kriteria.', 'done' => false],
                        ['icon' => 'table_chart', 'title' => 'Verifikasi Matriks X', 'desc' => 'Periksa dan sesuaikan nilai alternatif pada matriks keputusan.', 'done' => false],
                        ['icon' => 'military_tech', 'title' => 'Lihat Hasil ', 'desc' => 'Buka halaman Keputusan untuk melihat peringkat akhir dan rekomendasi.', 'done' => false],
                    ]; @endphp
                    @foreach($steps as $i => $step)
                    <li class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full {{ $step['done'] ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-on-surface-variant' }} flex items-center justify-center font-bold text-label-sm shrink-0 mt-0.5">
                            {{ $i + 1 }}
                        </div>
                        <div>
                            <span class="font-label-md text-label-md font-semibold text-on-surface block">{{ $step['title'] }}</span>
                            <span class="font-body-sm text-xs text-on-surface-variant leading-relaxed">{{ $step['desc'] }}</span>
                        </div>
                    </li>
                    @endforeach
                </ol>
            </div>

            <!-- Current Data Summary -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 p-space-lg">
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-space-sm">Status Data Saat Ini</h3>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center py-2 border-b border-outline-variant/10">
                        <span class="font-label-md text-label-md text-on-surface-variant">Jumlah Kriteria</span>
                        <span class="font-headline-sm font-bold {{ $stats['kriteria'] > 0 ? 'text-primary' : 'text-on-surface-variant' }}">
                            {{ $stats['kriteria'] }} kriteria
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-outline-variant/10">
                        <span class="font-label-md text-label-md text-on-surface-variant">Jumlah Alternatif</span>
                        <span class="font-headline-sm font-bold {{ $stats['alternatif'] > 0 ? 'text-primary' : 'text-on-surface-variant' }}">
                            {{ $stats['alternatif'] }} alternatif
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="font-label-md text-label-md text-on-surface-variant">Status Data</span>
                        @if($hasData)
                        <span class="px-2.5 py-1 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-xs font-bold">Ada Data</span>
                        @else
                        <span class="px-2.5 py-1 rounded-full bg-error/10 text-error font-label-sm text-xs font-bold">Kosong</span>
                        @endif
                    </div>
                </div>

                @if($hasData)
                <div class="mt-space-md pt-space-sm border-t border-outline-variant/10 space-y-2">
                    <a href="{{ route('spk.kriteria') }}" class="w-full flex items-center justify-center gap-2 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high font-label-md text-xs font-semibold transition-all">
                        <span class="material-symbols-outlined text-[16px]">tune</span>
                        Lihat Kriteria & AHP
                    </a>
                    <a href="{{ route('spk.alternatif') }}" class="w-full flex items-center justify-center gap-2 py-2 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high font-label-md text-xs font-semibold transition-all">
                        <span class="material-symbols-outlined text-[16px]">table_chart</span>
                        Lihat Matriks Alternatif
                    </a>
                    <a href="{{ route('spk.keputusan') }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary text-on-primary hover:bg-primary/90 font-label-md text-xs font-bold shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[16px]">military_tech</span>
                        Lihat Hasil Keputusan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    const fileInput = document.getElementById('file-input');
    const dropZone  = document.getElementById('drop-zone');
    const dropIdle  = document.getElementById('drop-idle');
    const dropSel   = document.getElementById('drop-selected');
    const fileNameLabel = document.getElementById('file-name-label');
    const fileSizeLabel = document.getElementById('file-size-label');
    const btnImport = document.getElementById('btn-import');
    const btnLabel  = document.getElementById('btn-label');

    function handleFile(file) {
        if (!file) { return; }
        dropIdle.classList.add('hidden');
        dropSel.classList.remove('hidden');
        fileNameLabel.textContent = file.name;
        fileSizeLabel.textContent = (file.size / 1024).toFixed(1) + ' KB';
        btnImport.disabled = false;
        btnLabel.textContent = 'Proses Import Sekarang';
        dropZone.classList.add('border-primary', 'bg-primary/5');
        dropZone.classList.remove('border-outline-variant/50');
    }

    fileInput.addEventListener('change', function () {
        handleFile(this.files[0]);
    });

    // Drag & drop
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('border-primary', 'bg-primary/5');
    });
    dropZone.addEventListener('dragleave', function () {
        if (!fileInput.files[0]) {
            dropZone.classList.remove('border-primary', 'bg-primary/5');
        }
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            handleFile(file);
        }
    });

    // Loading state on submit
    document.getElementById('form-import').addEventListener('submit', function () {
        btnImport.disabled = true;
        btnLabel.textContent = 'Sedang memproses file...';
        btnImport.innerHTML = `<span class="material-symbols-outlined text-[20px] animate-spin">refresh</span> <span>Sedang memproses file...</span>`;
    });

    // Dynamic mode radio card selection
    document.querySelectorAll('input[name="mode"]').forEach(radio => {
        radio.addEventListener('change', updateModeCards);
    });

    function updateModeCards() {
        document.querySelectorAll('.mode-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (input && input.checked) {
                card.classList.add('border-primary', 'bg-primary-fixed/25');
                card.classList.remove('border-outline-variant/30', 'bg-surface-container-low');
            } else {
                card.classList.remove('border-primary', 'bg-primary-fixed/25');
                card.classList.add('border-outline-variant/30', 'bg-surface-container-low');
            }
        });
    }
</script>
@endsection

