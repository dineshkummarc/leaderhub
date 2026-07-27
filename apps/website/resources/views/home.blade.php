@extends('layouts.app')

@section('title', 'Komersial Fitness Distributor')

@section('content')
<!-- Hero Section (Piku SaaS Option 1 Style with Fixed Content & Background Slider) -->
<section class="relative w-full bg-slate-950 text-white overflow-hidden border-b border-border min-h-[620px] lg:min-h-[680px] flex items-center">
    
    <!-- Background Swiper Slider (Only Background Image Slides) -->
    <div class="swiper hero-swiper absolute inset-0 z-0 w-full h-full">
        <div class="swiper-wrapper">
            @forelse($banners as $banner)
                @php
                    $bannerImg = $banner->image;
                    $bannerUrl = $bannerImg ? (str_starts_with($bannerImg, 'http') ? $bannerImg : (file_exists(public_path($bannerImg)) ? asset($bannerImg) : Storage::url($bannerImg))) : null;
                @endphp
                <div class="swiper-slide relative w-full h-full">
                    @if($bannerUrl)
                        <img src="{{ $bannerUrl }}" alt="{{ $banner->title }}" loading="lazy" class="w-full h-full object-cover opacity-35">
                    @else
                        <div class="w-full h-full bg-slate-950 opacity-40"></div>
                    @endif
                </div>
            @empty
                <div class="swiper-slide relative w-full h-full">
                    <div class="w-full h-full bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 opacity-50"></div>
                </div>
            @endforelse
        </div>
        <!-- Gradient Overlays for High Legibility -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/85 to-slate-950/50 pointer-events-none z-10"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_75%_25%,_rgba(220,38,38,0.18),_transparent_60%)] pointer-events-none z-10"></div>
    </div>

    <!-- Fixed Hero Content Container (Stays Fixed Over Background Slider) -->
    <div class="relative z-20 max-w-[1280px] mx-auto px-4 sm:px-6 py-12 lg:py-20 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Copy & Metrics Column -->
            <div class="lg:col-span-7">
                <!-- Eyebrow Pill -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold text-accent bg-accent-soft border border-accent/20 mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                    <span class="uppercase tracking-wider">Distributor Commercial Fitness</span>
                </div>

                <!-- Display Headline -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold font-display leading-[1.1] tracking-tight text-white mb-6">
                    Peralatan Gym Komersial <span class="text-accent underline decoration-accent/30 underline-offset-8">Kelas Dunia</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-slate-300 leading-relaxed mb-8 max-w-xl">
                    Menyediakan mesin strength, cardio, dan aksesoris fitness komersial berstandar internasional untuk hotel, commercial gym, dan fitness center di seluruh Indonesia.
                </p>

                <!-- Primary CTA Action Buttons -->
                <div class="flex flex-wrap gap-4 mb-10">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-[10px] shadow-lg hover:shadow-accent/25 transition-all duration-200">
                        <span>Jelajahi Katalog</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                    <a href="https://wa.me/6281234567890?text={{ urlencode('Halo LeaderHub, saya ingin berkonsultasi mengenai peralatan gym.') }}" target="_blank" class="inline-flex items-center gap-2 px-7 py-3.5 bg-surface-2/80 hover:bg-surface-2 text-text text-sm font-semibold rounded-[10px] border border-border-strong backdrop-blur-md transition-all duration-200">
                        <span>Konsultasi Gratis</span>
                    </a>
                </div>

                <!-- Integrated Hero Metrics / Stats Bar -->
                <div class="grid grid-cols-3 gap-6 pt-6 border-t border-slate-800/80">
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-white font-display tracking-tight">500+</div>
                        <div class="text-xs text-slate-400 font-medium mt-0.5">Commercial Gyms</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-white font-display tracking-tight">50+</div>
                        <div class="text-xs text-slate-400 font-medium mt-0.5">Model Peralatan</div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-accent font-display tracking-tight">100%</div>
                        <div class="text-xs text-slate-400 font-medium mt-0.5">Garansi Resmi</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Admin-Managed Floating Hero Product Deck -->
            <div class="lg:col-span-5 relative">
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase tracking-wider mb-2">
                        <span>Produk Unggulan Hero</span>
                        <span class="text-accent font-mono text-[11px] bg-accent-soft px-2 py-0.5 rounded-full">Admin Selected</span>
                    </div>

                    @forelse($hero_products as $index => $hProduct)
                        @php
                            $hImg = $hProduct->featured_image;
                            $hImgUrl = $hImg ? (str_starts_with($hImg, 'http') ? $hImg : (file_exists(public_path($hImg)) ? asset($hImg) : Storage::url($hImg))) : asset('products-image/Cardio/RCT-900A.webp');
                        @endphp
                        <a href="{{ route('products.show', $hProduct->slug) }}" class="group block relative bg-slate-900/90 backdrop-blur-md border border-slate-800/90 hover:border-accent/50 rounded-[14px] p-3.5 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex items-center gap-4">
                                <!-- Product Rank / Badge -->
                                <div class="shrink-0 w-7 h-7 rounded-full bg-accent/15 border border-accent/30 text-accent font-bold font-mono text-xs flex items-center justify-center">
                                    #{{ $index + 1 }}
                                </div>
                                
                                <!-- Thumbnail -->
                                <div class="relative w-16 h-16 rounded-[10px] bg-slate-950 overflow-hidden shrink-0 border border-slate-800">
                                    <img src="{{ $hImgUrl }}" alt="{{ $hProduct->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                </div>

                                <!-- Meta & Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        @if($hProduct->category)
                                            <span class="text-[10px] font-semibold uppercase tracking-wider text-accent bg-accent-soft px-2 py-0.5 rounded-full">
                                                {{ $hProduct->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-sm font-semibold text-white truncate group-hover:text-accent transition-colors">
                                        {{ $hProduct->name }}
                                    </h3>
                                    <div class="flex items-center justify-between mt-1">
                                        @if($hProduct->price)
                                            <span class="text-xs font-bold text-slate-200 font-mono">${{ number_format($hProduct->price, 0) }}</span>
                                        @else
                                            <span class="text-[11px] text-slate-400 italic">Distributor Class</span>
                                        @endif
                                        <span class="text-[11px] font-medium text-accent group-hover:underline flex items-center gap-1">
                                            <span>Lihat Detail</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <!-- Default Fallback Card Deck if no products selected -->
                        <div class="bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-[14px] p-6 text-center">
                            <p class="text-xs text-slate-400 mb-3">Kelola produk hero melalui Admin Panel dengan menandai produk sebagai Featured.</p>
                            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-accent hover:underline">Lihat Semua Produk</a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Shop by Category Section (Design System Bento Grid Layout) -->
<section class="py-20 bg-bg transition-colors duration-200 border-b border-border">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-accent mb-2 block">Kategori Produk</span>
                <h2 class="text-2xl sm:text-3xl font-bold font-heading text-text">Pilih Berdasarkan Kategori</h2>
            </div>
            <a href="{{ route('products.index') }}" class="mt-4 md:mt-0 text-sm font-semibold text-accent hover:underline flex items-center gap-1">
                <span>Lihat Semua Kategori</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($categories as $category)
                @php
                    $catImg = $category->image;
                    $catUrl = $catImg ? (str_starts_with($catImg, 'http') ? $catImg : (file_exists(public_path($catImg)) ? asset($catImg) : Storage::url($catImg))) : asset('products-image/Cardio/RCT-900A.webp');
                @endphp
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group relative rounded-[14px] overflow-hidden h-72 bg-surface border border-border hover:border-border-strong hover:shadow-lg transition-all duration-300">
                    <img src="{{ $catUrl }}" alt="{{ $category->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent group-hover:from-slate-950/95 transition-all duration-300 flex flex-col justify-end p-6">
                        <h3 class="text-white text-xl font-bold font-heading mb-1 group-hover:text-accent transition-colors flex items-center justify-between">
                            <span>{{ $category->name }}</span>
                            <svg class="w-5 h-5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </h3>
                        <p class="text-xs text-slate-300">Lihat seri produk {{ strtolower($category->name) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-20 bg-surface border-b border-border transition-colors duration-200">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-xs font-semibold uppercase tracking-wider text-accent mb-2 block">Produk Unggulan</span>
            <h2 class="text-2xl sm:text-3xl font-bold font-heading text-text mb-3">Peralatan Gym Komersial Pilihan</h2>
            <p class="text-sm text-text-muted">Koleksi terpopuler dengan standar durabilitas & performa terbaik untuk commercial gym.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($featured_products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-7 py-3 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-[10px] shadow-sm transition-colors">
                <span>Lihat Semua Produk</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- WhatsApp Consultation CTA Section -->
<section class="py-20 bg-bg transition-colors duration-200">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6">
        <div class="relative bg-slate-950 text-white rounded-[18px] p-8 sm:p-12 overflow-hidden border border-slate-800 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="relative z-10 max-w-xl">
                <span class="text-xs font-semibold uppercase tracking-wider text-accent bg-accent-soft px-3 py-1 rounded-full mb-4 inline-block">
                    Konsultasi Gratis
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold font-heading text-white mb-3">
                    Butuh Bantuan Memilih Peralatan Fitness?
                </h2>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Tim konsultan kami siap membantu merencanakan layout gym, pemilihan mesin, hingga estimasi anggaran secara gratis.
                </p>
            </div>

            <div class="relative z-10 shrink-0">
                <a href="https://wa.me/6281234567890?text={{ urlencode('Halo LeaderHub, saya ingin berkonsultasi mengenai peralatan gym.') }}" target="_blank" class="inline-flex items-center gap-3 px-7 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-[10px] shadow-lg transition-all">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Hubungi via WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
