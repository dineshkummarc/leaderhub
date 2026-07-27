<footer class="bg-surface border-t border-border mt-24 py-16 transition-colors duration-200">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-4 gap-10">
        <!-- Col 1: Brand & Tagline -->
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="block">
                <img src="{{ asset('logo/main-logo/main-blHD.png') }}" alt="LeaderHub" class="h-8 w-auto dark:hidden object-contain">
                <img src="{{ asset('logo/main-logo/main-whHD.png') }}" alt="LeaderHub" class="h-8 w-auto hidden dark:block object-contain">
            </a>
            <p class="text-sm text-text-muted leading-relaxed">
                Distributor alat fitness komersial dan profesional kualitas terbaik di Indonesia. Solusi lengkap kebutuhan gym & fitness center.
            </p>
        </div>

        <!-- Col 2: Navigation -->
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wider text-text-subtle mb-4">Navigasi</h4>
            <ul class="space-y-2.5 text-sm text-text-muted">
                <li><a href="{{ route('products.index') }}" class="hover:text-accent transition-colors">Katalog Produk</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-accent transition-colors">Blog</a></li>
                <li><a href="{{ route('gallery.index') }}" class="hover:text-accent transition-colors">Galeri Proyek</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-accent transition-colors">Tentang Kami</a></li>
            </ul>
        </div>

        <!-- Col 3: Company -->
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wider text-text-subtle mb-4">Perusahaan</h4>
            <ul class="space-y-2.5 text-sm text-text-muted">
                <li><a href="{{ route('about') }}" class="hover:text-accent transition-colors">Profil Perusahaan</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-accent transition-colors">Hubungi Kami</a></li>
                <li><a href="/terms" class="hover:text-accent transition-colors">Syarat & Ketentuan</a></li>
                <li><a href="/privacy" class="hover:text-accent transition-colors">Kebijakan Privasi</a></li>
            </ul>
        </div>

        <!-- Col 4: Contact Info with SVG Icons -->
        <div>
            <h4 class="text-xs font-semibold uppercase tracking-wider text-text-subtle mb-4">Kontak Kami</h4>
            <ul class="space-y-3 text-sm text-text-muted">
                <li class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>info@leaderhub.id</span>
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span>+62 812 3456 7890</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Jakarta, Indonesia</span>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 mt-12 pt-6 border-t border-border flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-text-subtle">
        <p>&copy; {{ date('Y') }} LeaderHub. All rights reserved.</p>
        <p>Modern Premium Commercial Fitness Equipment</p>
    </div>
</footer>
