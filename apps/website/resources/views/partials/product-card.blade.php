@php
    $imagePath = $product->featured_image;
    $imageUrl = null;
    if ($imagePath) {
        if (str_starts_with($imagePath, 'http')) {
            $imageUrl = $imagePath;
        } elseif (file_exists(public_path($imagePath))) {
            $imageUrl = asset($imagePath);
        } else {
            $imageUrl = Storage::url($imagePath);
        }
    }
@endphp

<div class="bg-elevated rounded-[14px] border border-border overflow-hidden hover:border-border-strong hover:shadow-md transition-all duration-200 group flex flex-col h-full" data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-image="{{ $imageUrl ?? '' }}">
    <!-- Image Container (4:5 Portrait Aspect Ratio) -->
    <a href="{{ route('products.show', $product->slug) }}" class="block relative aspect-[4/5] bg-surface overflow-hidden">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center text-text-subtle p-4 bg-surface">
                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-xs font-medium">Product Image</span>
            </div>
        @endif

        <!-- Category Pill Badge -->
        @if($product->category)
            <span class="absolute top-3 left-3 bg-accent-soft text-accent text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-sm backdrop-blur-sm">
                {{ $product->category->name }}
            </span>
        @endif
    </a>

    <!-- Details -->
    <div class="p-4 flex flex-col flex-1 justify-between gap-3">
        <div>
            @if($product->brand)
                <p class="text-[11px] font-medium text-text-subtle uppercase tracking-wider mb-1">{{ $product->brand->name }}</p>
            @endif
            <h3 class="text-sm font-semibold text-text group-hover:text-accent transition-colors line-clamp-2 leading-snug">
                <a href="{{ route('products.show', $product->slug) }}">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-border/50">
            @php $_fp = format_price($product->price); @endphp
            @if($_fp)
                <div>
                    <span class="text-[10px] text-text-subtle block">Harga Est.</span>
                    <span class="text-base font-bold text-accent font-mono tracking-tight">{{ $_fp }}</span>
                </div>
            @else
                <span class="text-xs text-text-muted italic">Hubungi kami</span>
            @endif

            <a href="{{ route('products.show', $product->slug) }}" class="p-2 text-text-muted hover:text-accent hover:bg-accent-soft rounded-md transition-colors" aria-label="Lihat Detail">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
