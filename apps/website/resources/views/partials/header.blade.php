<header class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
    <div class="container mx-auto px-4 h-16 flex items-center justify-between">
        <a href="/" class="text-2xl font-bold text-primary-600 dark:text-primary-500">LeaderHub<span class="text-slate-900 dark:text-white">.</span></a>
        
        <nav class="hidden md:flex gap-8">
            <a href="#" class="hover:text-primary-600 transition-colors">Produk</a>
            <a href="#" class="hover:text-primary-600 transition-colors">Gallery</a>
            <a href="#" class="hover:text-primary-600 transition-colors">Blog</a>
            <a href="/about" class="hover:text-primary-600 transition-colors">Tentang</a>
            <a href="/contact" class="hover:text-primary-600 transition-colors">Kontak</a>
        </nav>

        <div class="flex items-center gap-4">
            <!-- Toggles (handled in T04) -->
            <button id="theme-toggle" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="dark:hidden">🌙</span>
                <span class="hidden dark:inline">☀️</span>
            </button>
            <button id="lang-toggle" class="p-2 text-sm font-semibold hover:text-primary-600">
                {{ app()->getLocale() === 'id' ? 'ID' : 'EN' }}
            </button>
            <div class="relative cursor-pointer">
                🛒
                <span class="absolute -top-2 -right-2 bg-primary-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
            </div>
        </div>
    </div>
</header>
