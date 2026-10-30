<!-- Minimalist Top Header Navbar -->
<header class="fixed top-0 left-24 right-0 h-16 bg-surface-container-lowest/90 backdrop-blur-md z-40 border-b border-outline-variant/20 shadow-[0_1px_8px_rgba(0,0,0,0.02)]">
    <div class="h-16 w-full px-gutter flex items-center justify-between">
        <!-- Left: Brand Title & Method Badge -->
        <div class="flex items-center gap-space-md">
            <div class="flex items-center gap-2">
                <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight font-bold">SPK Decision Suite</span>
                <span class="text-outline-variant/60 font-light">/</span>
                <span class="text-on-surface-variant font-label-md text-label-md">
                    @yield('page_title', 'Dashboard')
                </span>
            </div>
            
            <div class="hidden md:flex items-center gap-space-xs ml-2">
                <span class="px-2.5 py-0.5 rounded-full bg-primary-fixed/40 text-on-primary-fixed-variant font-label-sm text-label-sm font-semibold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                    Metode: AHP & TOPSIS
                </span>
            </div>
        </div>

        <!-- Right: Minimalist Status & Profile -->
        <div class="flex items-center gap-space-md">
            <!-- Date Display -->
            <div class="hidden sm:flex items-center gap-1.5 text-on-surface-variant font-label-sm text-label-sm bg-surface-container-low px-3 py-1.5 rounded-xl">
                <span class="material-symbols-outlined text-[16px] text-primary">calendar_today</span>
                <span>Sabtu, 28 Juni 2025</span>
            </div>

            <!-- Notification Icon -->
            <button class="p-2 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors relative" title="Notifikasi Sistem" type="button">
                <span class="material-symbols-outlined text-[20px]">notifications</span>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-secondary"></span>
            </button>

            <!-- Minimal Profile Pill -->
            <div class="flex items-center gap-2 pl-2 border-l border-outline-variant/30">
                <div class="hidden lg:flex flex-col text-right">
                    <span class="font-label-sm text-on-surface font-semibold leading-tight">Admin SPK</span>
                    <span class="text-[10px] text-on-surface-variant leading-tight">Pengambil Keputusan</span>
                </div>
                <div class="w-8 h-8 rounded-xl bg-primary text-on-primary font-bold text-xs flex items-center justify-center shadow-sm">
                    A
                </div>
            </div>
        </div>
    </div>
</header>

