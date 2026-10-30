<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SPK Decision Suite - AHP & TOPSIS')</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <style>
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(109, 122, 114, 0.2); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(109, 122, 114, 0.4); }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background font-sans text-on-surface antialiased min-h-screen">
    <!-- Sidebar Navigation -->
    @include('layouts.sidebar')

    <!-- Main Content Shell -->
    <div class="pl-24 flex flex-col min-h-screen">
        <!-- Minimalist Navbar -->
        @include('layouts.navbar')

        <!-- Page Content -->
        <main class="pt-16 w-full flex-1">
            @yield('content')
        </main>

        <!-- Minimal Footer -->
        <footer class="py-4 px-gutter text-center text-on-surface-variant font-body-sm text-[11px] border-t border-outline-variant/10">
            Sistem Pendukung Keputusan (SPK) Multi-Kriteria &bull; Metode AHP & TOPSIS &copy; {{ date('Y') }}
        </footer>
    </div>

    <!-- Pop-up Konfirmasi Setelah Ubah Data (Success / Warning / Error Alert Modal) -->
    @if(session('success') || session('warning') || session('error'))
    <div id="feedbackModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-outline-variant/20 flex flex-col items-center text-center animate-fade-in">
            @if(session('success'))
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[36px]">check_circle</span>
                </div>
                <h3 class="font-headline-sm text-lg font-bold text-on-surface">Data Berhasil Diperbarui!</h3>
                <p class="font-body-sm text-xs text-on-surface-variant mt-2 leading-relaxed">
                    {{ session('success') }}
                </p>
            @elseif(session('warning'))
                <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[36px]">warning</span>
                </div>
                <h3 class="font-headline-sm text-lg font-bold text-on-surface">Perhatian</h3>
                <p class="font-body-sm text-xs text-on-surface-variant mt-2 leading-relaxed">
                    {{ session('warning') }}
                </p>
            @else
                <div class="w-16 h-16 rounded-2xl bg-error/10 text-error flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[36px]">error</span>
                </div>
                <h3 class="font-headline-sm text-lg font-bold text-on-surface">Terjadi Kesalahan</h3>
                <p class="font-body-sm text-xs text-on-surface-variant mt-2 leading-relaxed">
                    {{ session('error') ?? 'Silakan periksa input data Anda.' }}
                </p>
            @endif

            <div class="mt-6 w-full">
                <button type="button" onclick="closeFeedbackModal()" class="w-full py-2.5 px-4 bg-primary text-on-primary font-bold text-xs rounded-xl hover:bg-primary/90 transition-all shadow-md shadow-primary/20">
                    Oke, Mengerti
                </button>
            </div>
        </div>
    </div>
    <script>
        function closeFeedbackModal() {
            const modal = document.getElementById('feedbackModal');
            if (modal) modal.remove();
        }
    </script>
    @endif

    <!-- Pop-up Konfirmasi Mau Hapus Data (Delete Confirmation Modal) -->
    <div id="globalDeleteModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-outline-variant/20 flex flex-col items-center text-center animate-fade-in">
            <div class="w-16 h-16 rounded-2xl bg-error/10 text-error flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[36px]">delete_forever</span>
            </div>
            <h3 id="globalDeleteTitle" class="font-headline-sm text-lg font-bold text-on-surface">
                Konfirmasi Hapus Data
            </h3>
            <p id="globalDeleteDesc" class="font-body-sm text-xs text-on-surface-variant mt-2 leading-relaxed">
                Apakah Anda yakin ingin menghapus data ini? Tindakan ini bersifat permanen dan data terkait akan ikut terhapus.
            </p>

            <form id="globalDeleteForm" method="POST" class="mt-6 w-full flex items-center gap-2">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteConfirm()" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-semibold text-on-surface-variant bg-surface-container hover:bg-surface-container-high transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-error hover:bg-error/90 transition-colors shadow-sm">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

    <script>
        window.openDeleteConfirm = function(url, title, message) {
            const modal = document.getElementById('globalDeleteModal');
            const form = document.getElementById('globalDeleteForm');
            const titleEl = document.getElementById('globalDeleteTitle');
            const descEl = document.getElementById('globalDeleteDesc');
            if (form && modal) {
                form.action = url;
                if (titleEl && title) titleEl.textContent = title;
                if (descEl && message) descEl.textContent = message;
                modal.classList.remove('hidden');
            }
        };

        window.closeDeleteConfirm = function() {
            const modal = document.getElementById('globalDeleteModal');
            if (modal) modal.classList.add('hidden');
        };
    </script>
</body>
</html>
