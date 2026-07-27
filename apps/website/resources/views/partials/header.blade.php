<header class="sticky top-0 z-50 bg-bg/90 backdrop-blur-md border-b border-border transition-colors duration-200">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('logo/main-logo/main-blHD.png') }}" alt="LeaderHub" class="h-8 w-auto dark:hidden object-contain">
            <img src="{{ asset('logo/main-logo/main-whHD.png') }}" alt="LeaderHub" class="h-8 w-auto hidden dark:block object-contain">
        </a>
        
        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-text-muted">
            <a href="{{ route('products.index') }}" class="hover:text-accent transition-colors {{ request()->routeIs('products.*') ? 'text-accent font-semibold' : '' }}">Produk</a>
            <a href="{{ route('gallery.index') }}" class="hover:text-accent transition-colors {{ request()->routeIs('gallery.*') ? 'text-accent font-semibold' : '' }}">Gallery</a>
            <a href="/about" class="hover:text-accent transition-colors">Tentang Kami</a>
            <a href="/contact" class="hover:text-accent transition-colors">Kontak</a>
        </nav>

        <!-- Action Controls -->
        <div class="flex items-center gap-3">
            <!-- Theme Toggle with SVG Icons -->
            <button id="theme-toggle" type="button" aria-label="Toggle Theme" class="p-2 rounded-md hover:bg-surface-2 text-text-muted hover:text-text transition-colors">
                <!-- Moon SVG (for light mode -> switch to dark) -->
                <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <!-- Sun SVG (for dark mode -> switch to light) -->
                <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </button>

            <!-- Language Toggle -->
            <button id="lang-toggle" type="button" class="px-2.5 py-1 text-xs font-semibold rounded-md border border-border hover:border-border-strong text-text-muted hover:text-text transition-colors">
                {{ strtoupper(app()->getLocale()) }}
            </button>

            <!-- Inquiry Cart Icon with SVG -->
            <div class="relative p-2 rounded-md hover:bg-surface-2 text-text-muted hover:text-text transition-colors cursor-pointer" id="cart-toggle-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="absolute -top-1 -right-1 bg-accent text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center shadow-sm">0</span>
            </div>
        </div>
    </div>
</header>
