@extends('layouts.app')

@section('title', $product->name)
@section('seo_description', Str::limit(strip_tags($product->description), 150))
@section('seo_image', $product->featured_image ? (str_starts_with($product->featured_image, 'http') ? $product->featured_image : asset($product->featured_image)) : null)

@section('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Product",
  "name": "{{ $product->name }}",
  "image": [
    @foreach($product->images as $img)
      "{{ str_starts_with($img->path, 'http') ? $img->path : asset($img->path) }}"{{ !$loop->last ? ',' : '' }}
    @endforeach
  ],
  "description": "{{ Str::limit(strip_tags($product->description), 150) }}",
  "sku": "{{ $product->sku }}",
  @if($product->brand)
  "brand": {
    "@@type": "Brand",
    "name": "{{ $product->brand->name }}"
  },
  @endif
  "offers": {
    "@@type": "Offer",
    "url": "{{ request()->url() }}",
    "priceCurrency": "USD",
    "price": "{{ $product->price ?? 0 }}",
    "availability": "https://schema.org/InStock"
  }
}
</script>
@endsection

@section('content')
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 py-10">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-text-muted mb-8 overflow-x-auto pb-2">
        <a href="{{ route('home') }}" class="hover:text-accent transition-colors">Beranda</a>
        <svg class="w-3.5 h-3.5 text-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('products.index') }}" class="hover:text-accent transition-colors">Produk</a>
        @if($product->category)
            <svg class="w-3.5 h-3.5 text-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-accent transition-colors">{{ $product->category->name }}</a>
        @endif
        <svg class="w-3.5 h-3.5 text-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-text font-semibold truncate">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
        <!-- Product Gallery Column -->
        <div class="md:col-span-6 space-y-4">
            @if($product->images->count() > 0)
                @php
                    $firstImg = $product->images->first()->path;
                    $firstUrl = str_starts_with($firstImg, 'http') ? $firstImg : (file_exists(public_path($firstImg)) ? asset($firstImg) : Storage::url($firstImg));
                @endphp
                <div class="bg-surface rounded-[14px] overflow-hidden aspect-[4/3] border border-border flex items-center justify-center p-4">
                    <img id="main-image" src="{{ $firstUrl }}" alt="{{ $product->name }}" class="w-full h-full object-contain max-h-[460px]">
                </div>

                @if($product->images->count() > 1)
                    <div class="grid grid-cols-5 gap-3">
                        @foreach($product->images as $image)
                            @php
                                $thumbImg = $image->path;
                                $thumbUrl = str_starts_with($thumbImg, 'http') ? $thumbImg : (file_exists(public_path($thumbImg)) ? asset($thumbImg) : Storage::url($thumbImg));
                            @endphp
                            <button onclick="document.getElementById('main-image').src='{{ $thumbUrl }}'" type="button" class="bg-surface rounded-[10px] overflow-hidden aspect-square border border-border hover:border-accent focus:outline-none focus:ring-2 focus:ring-accent/30 transition-all p-1">
                                <img src="{{ $thumbUrl }}" alt="{{ $image->alt }}" loading="lazy" class="w-full h-full object-cover rounded-[6px]">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="bg-surface rounded-[14px] border border-border aspect-[4/3] flex flex-col items-center justify-center text-text-subtle p-6">
                    <svg class="w-16 h-16 opacity-40 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-xs">Gambar Produk Belum Tersedia</span>
                </div>
            @endif
        </div>

        <!-- Product Info Column -->
        <div class="md:col-span-6 space-y-6">
            <div>
                @if($product->brand)
                    <span class="inline-block text-xs font-bold text-accent uppercase tracking-wider bg-accent-soft px-3 py-1 rounded-full mb-3">
                        {{ $product->brand->name }}
                    </span>
                @endif
                <h1 class="text-2xl sm:text-3xl font-bold font-heading text-text leading-tight mb-2">{{ $product->name }}</h1>
                <p class="text-xs text-text-subtle font-mono">SKU: {{ $product->sku }}</p>
            </div>

            @php $_sp = format_price($product->price); @endphp
            @if($_sp)
                <div class="p-4 bg-surface rounded-[10px] border border-border inline-block min-w-[200px]">
                    <span class="text-xs text-text-muted block">Estimasi Harga</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-accent font-mono tracking-tight">{{ $_sp }}</span>
                </div>
            @endif

            <div class="prose prose-slate dark:prose-invert max-w-none text-sm text-text-muted leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </div>

            <!-- Specifications Table -->
            @if($product->specs->count() > 0)
                <div class="space-y-3">
                    <h3 class="text-sm font-bold font-heading text-text flex items-center gap-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Spesifikasi Teknis</span>
                    </h3>
                    <div class="bg-elevated border border-border rounded-[10px] overflow-hidden">
                        <table class="w-full text-left text-xs text-text-muted">
                            <tbody>
                                @foreach($product->specs as $index => $spec)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-elevated' : 'bg-surface' }} border-b border-border/50 last:border-0">
                                        <th class="py-2.5 px-4 font-semibold text-text w-1/3 border-r border-border/50">{{ $spec->label }}</th>
                                        <td class="py-2.5 px-4 font-mono">{{ $spec->value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Hidden data element for inquiry cart -->
            @php
                $_inqImg = $product->featured_image;
                $_inqImgUrl = $_inqImg ? (str_starts_with($_inqImg, 'http') ? $_inqImg : (file_exists(public_path($_inqImg)) ? asset($_inqImg) : Storage::url($_inqImg))) : '';
            @endphp
            <span data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-image="{{ $_inqImgUrl }}" class="hidden"></span>

            <!-- Inquiry Action -->
            <div class="pt-2">
                <button onclick="addToInquiry({{ $product->id }})" class="w-full inline-flex items-center justify-center gap-3 px-6 py-3.5 bg-accent hover:bg-accent-hover text-white font-semibold text-sm rounded-[10px] shadow-md transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    <span>Tambah ke Daftar Minat</span>
                </button>
            </div>

            <!-- WhatsApp Action Button (No Emojis) -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="https://wa.me/6281234567890?text={{ urlencode('Halo LeaderHub, saya tertarik dengan produk ' . $product->name . ' (SKU: ' . $product->sku . '). Mohon informasi penawaran harganya.') }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-3 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-[10px] shadow-md transition-colors">
                    <!-- WhatsApp SVG Icon -->
                    <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Tanya via WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
@if($related_products->count() > 0)
    <section class="py-16 bg-surface border-t border-border mt-16 transition-colors duration-200">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6">
            <h2 class="text-xl sm:text-2xl font-bold font-heading text-text mb-8">Produk Terkait</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($related_products as $related)
                    @include('partials.product-card', ['product' => $related])
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
