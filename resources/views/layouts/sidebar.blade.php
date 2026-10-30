<!-- Primary Navigation Sidebar -->
<aside class="fixed left-0 top-0 h-full w-24 bg-surface-container-lowest z-50 flex flex-col items-center justify-between py-space-lg shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-r border-outline-variant/20">
    <div class="flex flex-col items-center gap-space-md w-full">
        <!-- Logo -->
        <a href="{{ route('spk.dashboard') }}" class="flex items-center justify-center p-2 rounded-2xl bg-primary/10 text-primary hover:bg-primary hover:text-on-primary transition-all group" title="SPK Decision Suite">
            <span class="material-symbols-outlined text-[28px] group-hover:scale-110 transition-transform">analytics</span>
        </a>
        
        <div class="w-10 h-px bg-outline-variant/30 my-space-xs"></div>

        <!-- Navigation Menu -->
        <nav class="flex flex-col items-center gap-space-sm w-full px-space-xs">
            <!-- Beranda / Dashboard -->
            <a href="{{ route('spk.dashboard') }}" 
               class="flex flex-col items-center justify-center w-14 h-14 rounded-2xl transition-all group {{ request()->routeIs('spk.dashboard') ? 'bg-primary text-on-primary shadow-[0_8px_16px_-4px_rgba(0,105,72,0.3)] font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}" 
               title="Dashboard Utama">
                <span class="material-symbols-outlined text-[24px]">dashboard</span>
                <span class="font-label-sm text-[10px] mt-0.5 tracking-tight">Beranda</span>
            </a>

            <!-- Kriteria & Bobot -->
            <a href="{{ route('spk.kriteria') }}" 
               class="flex flex-col items-center justify-center w-14 h-14 rounded-2xl transition-all group {{ request()->routeIs('spk.kriteria') ? 'bg-primary text-on-primary shadow-[0_8px_16px_-4px_rgba(0,105,72,0.3)] font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}" 
               title="Kriteria & Bobot Penilaian">
                <span class="material-symbols-outlined text-[24px]">tune</span>
                <span class="font-label-sm text-[10px] mt-0.5 tracking-tight">Kriteria</span>
            </a>

            <!-- Alternatif & Matriks -->
            <a href="{{ route('spk.alternatif') }}" 
               class="flex flex-col items-center justify-center w-14 h-14 rounded-2xl transition-all group {{ request()->routeIs('spk.alternatif') ? 'bg-primary text-on-primary shadow-[0_8px_16px_-4px_rgba(0,105,72,0.3)] font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}" 
               title="Alternatif & Matriks Evaluasi">
                <span class="material-symbols-outlined text-[24px]">table_chart</span>
                <span class="font-label-sm text-[10px] mt-0.5 tracking-tight">Matriks</span>
            </a>

            <!-- Hasil & Keputusan -->
            <a href="{{ route('spk.keputusan') }}" 
               class="flex flex-col items-center justify-center w-14 h-14 rounded-2xl transition-all group {{ request()->routeIs('spk.keputusan') ? 'bg-primary text-on-primary shadow-[0_8px_16px_-4px_rgba(0,105,72,0.3)] font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}" 
               title="Hasil Perankingan & Rekomendasi Keputusan">
                <span class="material-symbols-outlined text-[24px]">military_tech</span>
                <span class="font-label-sm text-[10px] mt-0.5 tracking-tight">Keputusan</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Indicator -->
    <div class="flex flex-col items-center gap-1 w-full text-on-surface-variant/40">
        <span class="text-[9px] font-mono font-bold">SPK</span>
    </div>
</aside>
