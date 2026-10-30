<!-- Minimalist Top Header Navbar -->
<header class="fixed top-0 left-24 right-0 h-16 bg-surface-container-lowest/90 backdrop-blur-md z-40 border-b border-outline-variant/20 shadow-[0_1px_8px_rgba(0,0,0,0.02)]">
    <div class="h-16 w-full px-gutter flex items-center justify-between">
        <!-- Left: Brand Title & Method Badge -->
        <div class="flex items-center gap-space-md">
            <div class="flex items-center gap-2">
                <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight font-bold">SPK </span>
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

        <!-- Right: Real-time Live Calendar Date -->
        <div class="flex items-center gap-space-md">
            <!-- Dynamic Calendar Date Display -->
            <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-xs bg-surface-container-low px-3.5 py-1.5 rounded-xl border border-outline-variant/20 shadow-xs">
                <span class="material-symbols-outlined text-[17px] text-primary">calendar_month</span>
                <span id="live-calendar-date" class="font-semibold text-on-surface"></span>
            </div>
        </div>
    </div>
</header>

<script>
    (function() {
        function refreshLiveDate() {
            const dateEl = document.getElementById('live-calendar-date');
            if (!dateEl) return;
            const now = new Date();
            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            dateEl.textContent = now.toLocaleDateString('id-ID', options);
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', refreshLiveDate);
        } else {
            refreshLiveDate();
        }
    })();
</script>
