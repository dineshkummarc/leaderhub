@extends('layouts.app')

@section('title', 'Katalog Produk Fitness')

@section('content')
<!-- Header Banner -->
<div class="bg-surface border-b border-border py-12 transition-colors duration-200">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6">
        <span class="text-xs font-semibold uppercase tracking-wider text-accent mb-2 block">Commercial Equipment</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold font-display text-text mb-2">Katalog Produk</h1>
        <p class="text-sm text-text-muted max-w-xl">Jelajahi seluruh rangkaian peralatan gym komersial, cardio, plate-loaded, hingga spareparts resmi.</p>
    </div>
</div>

<div class="max-w-[1280px] mx-auto px-4 sm:px-6 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-1/4 shrink-0">
            <form action="{{ route('products.index') }}" method="GET" class="bg-elevated p-6 rounded-[14px] border border-border shadow-sm sticky top-24 space-y-6">
                <div class="flex items-center justify-between border-b border-border pb-4">
                    <h3 class="text-base font-bold font-heading text-text flex items-center gap-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Filter Produk</span>
                    </h3>
                    @if(request()->anyFilled(['q', 'category', 'brand', 'min_price', 'max_price', 'sort']))
                        <a href="{{ route('products.index') }}" class="text-xs text-accent hover:underline font-medium">Reset</a>
                    @endif
                </div>
                
                <!-- Search Input -->
                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-2">Cari Produk</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, SKU..." class="w-full pl-9 pr-3 py-2 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                        <svg class="w-4 h-4 text-text-subtle absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-2">Kategori</label>
                    <select name="category" class="w-full px-3 py-2 bg-surface border border-border rounded-[10px] text-sm text-text focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand Filter -->
                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-2">Brand / Merek</label>
                    <select name="brand" class="w-full px-3 py-2 bg-surface border border-border rounded-[10px] text-sm text-text focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->slug }}" {{ request('brand') == $brand->slug ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range Filter -->
                <div>
                    <label class="block text-xs font-semibold text-text-muted mb-2">Rentang Harga ($)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full px-3 py-2 bg-surface border border-border rounded-[10px] text-sm text-text focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full px-3 py-2 bg-surface border border-border rounded-[10px] text-sm text-text focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-[10px] shadow-sm transition-colors flex items-center justify-center gap-2">
                    <span>Terapkan Filter</span>
                </button>
            </form>
        </aside>

        <!-- Product Grid Content -->
        <div class="w-full lg:w-3/4">
            <!-- Results bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-border">
                <p class="text-xs text-text-muted">
                    Menampilkan <span class="font-bold text-text">{{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}</span> dari <span class="font-bold text-text">{{ $products->total() }}</span> produk
                </p>

                <!-- Sorting Dropdown -->
                <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2">
                    @foreach(request()->except(['sort', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label class="text-xs font-semibold text-text-muted shrink-0">Urutkan:</label>
                    <select name="sort" onchange="this.form.submit()" class="px-3 py-1.5 bg-elevated border border-border rounded-[10px] text-xs font-medium text-text focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama: A-Z</option>
                    </select>
                </form>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full py-16 text-center bg-elevated border border-border rounded-[14px] p-8">
                        <svg class="w-12 h-12 text-text-subtle mx-auto mb-3 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <p class="text-base font-semibold text-text mb-1">Tidak ada produk ditemukan</p>
                        <p class="text-xs text-text-muted mb-4">Coba ubah kata kunci atau filter pencarian Anda.</p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface hover:bg-surface-2 border border-border rounded-[10px] text-xs font-semibold text-text transition-colors">
                            Reset Filter
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
