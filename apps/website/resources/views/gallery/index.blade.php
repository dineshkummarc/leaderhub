@extends('layouts.app')

@section('title', 'Galeri Proyek Gym')

@section('content')
<!-- Header Banner -->
<div class="bg-surface border-b border-border py-12 transition-colors duration-200">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 text-center">
        <span class="text-xs font-semibold uppercase tracking-wider text-accent mb-2 block">Portfolio & Showcase</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold font-display text-text mb-3">Galeri Proyek</h1>
        <p class="text-sm text-text-muted max-w-xl mx-auto">Dokumentasi instalasi peralatan fitness LeaderHub di berbagai fitness center, hotel, dan commercial gym.</p>
    </div>
</div>

<div class="max-w-[1280px] mx-auto px-4 sm:px-6 py-12">
    <!-- Album Filters (Segmented Control) -->
    <div class="flex flex-wrap justify-center gap-2 mb-12" id="gallery-filters">
        <button type="button" class="filter-btn active px-5 py-2 bg-accent text-white text-xs font-semibold rounded-[10px] shadow-sm transition-all duration-180" data-filter="all">
            Semua Foto
        </button>
        @foreach($albums as $album)
            <button type="button" class="filter-btn px-5 py-2 bg-surface hover:bg-surface-2 text-text-muted hover:text-text border border-border text-xs font-semibold rounded-[10px] transition-all duration-180" data-filter=".album-{{ $album->id }}">
                {{ $album->title }}
            </button>
        @endforeach
    </div>

    <!-- Masonry Grid -->
    <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-6 space-y-6" id="gallery-grid">
        @foreach($albums as $album)
            @foreach($album->items as $item)
                @php
                    $itemImg = $item->path;
                    $itemUrl = str_starts_with($itemImg, 'http') ? $itemImg : (file_exists(public_path($itemImg)) ? asset($itemImg) : Storage::url($itemImg));
                @endphp
                <div class="break-inside-avoid gallery-item album-{{ $album->id }} transition-opacity duration-300">
                    <a href="{{ $itemUrl }}" class="glightbox block relative group rounded-[14px] overflow-hidden bg-surface border border-border hover:border-border-strong shadow-sm hover:shadow-md transition-all duration-300" data-gallery="gallery" data-title="{{ $item->caption }}">
                        <img src="{{ $itemUrl }}" alt="{{ $item->alt ?? $item->caption }}" loading="lazy" class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center p-4 text-center">
                            <!-- SVG Zoom Icon -->
                            <div class="p-3 bg-accent/90 text-white rounded-full mb-2 transform translate-y-2 group-hover:translate-y-0 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                            @if($item->caption)
                                <span class="text-xs font-semibold text-white line-clamp-2">{{ $item->caption }}</span>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        @endforeach
    </div>
</div>
@endsection
