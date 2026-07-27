@extends('layouts.app')

@section('title', 'Daftar Minat Saya — LeaderHub')

@section('content')
<div class="max-w-[720px] mx-auto px-4 sm:px-6 py-12">
    <h1 class="text-2xl sm:text-3xl font-bold font-heading text-text mb-8">Daftar Minat Saya</h1>

    <!-- Inquiry List (populated by JS) -->
    <div id="inquiry-list" class="space-y-4 mb-8"></div>

    <!-- Empty State (hidden by default, shown by JS if cart is empty) -->
    <div id="empty-state" class="hidden text-center py-16 bg-elevated border border-border rounded-[14px] p-8">
        <svg class="w-14 h-14 text-text-subtle mx-auto mb-4 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path>
        </svg>
        <p class="text-lg font-semibold text-text mb-2">Belum ada produk di daftar minat</p>
        <p class="text-sm text-text-muted mb-6">Jelajahi katalog produk kami dan tambahkan produk yang Anda minati.</p>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-[10px] shadow-sm transition-colors">
            <span>Lihat Katalog Produk</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
        </a>
    </div>

    <!-- Form (hidden by default, shown by JS if cart has items) -->
    <div id="inquiry-form" class="hidden">
        <div class="bg-elevated border border-border rounded-[14px] p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-text mb-6">Data Diri</h2>

            <form action="{{ route('inquiry.store') }}" method="POST" id="inquiry-submit-form">
                @csrf

                <!-- Honeypot field -->
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

                <!-- Hidden items container (populated by JS) -->
                <div id="inquiry-hidden-items"></div>

                <div class="space-y-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-semibold text-text mb-1">Nama Lengkap <span class="text-accent">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" required minlength="3"
                            class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                            placeholder="Masukkan nama Anda">
                        @error('customer_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-sm font-semibold text-text mb-1">Nomor WhatsApp <span class="text-accent">*</span></label>
                        <input type="tel" name="customer_phone" id="customer_phone" required minlength="8"
                            class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                            placeholder="08xxxxxxxxxx">
                        @error('customer_phone')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_email" class="block text-sm font-semibold text-text mb-1">Email (opsional)</label>
                        <input type="email" name="customer_email" id="customer_email"
                            class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                            placeholder="email@example.com">
                        @error('customer_email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-text mb-1">Pesan (opsional)</label>
                        <textarea name="message" id="message" rows="3"
                            class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                            placeholder="Tulis pesan Anda..."></textarea>
                    </div>

                    @if(session('error'))
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-[10px] text-sm text-red-700 dark:text-red-300">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" name="channel" value="email"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-[10px] shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                            </svg>
                            <span>Kirim via Email</span>
                        </button>
                        <button type="submit" name="channel" value="wa"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-[10px] shadow-sm transition-colors">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span>Kirim via WhatsApp</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('head')
<script>
// Product image map from server (fallback for old cart items without images)
var PRODUCT_IMAGE_MAP = @json($productImageMap);

// Helper: get best available image for a cart item
function getItemImage(item) {
    if (item.image) return item.image;
    if (PRODUCT_IMAGE_MAP && PRODUCT_IMAGE_MAP[item.product_id]) return PRODUCT_IMAGE_MAP[item.product_id];
    return '';
}

// Inline script untuk render cart items dan populate hidden inputs
document.addEventListener('DOMContentLoaded', function () {
    // We use the module's functions via the global window
    // The inquiry.js module is already loaded via app.js

    const STORAGE_KEY = 'leaderhub_inquiry';
    const listEl = document.getElementById('inquiry-list');
    const formEl = document.getElementById('inquiry-form');
    const emptyEl = document.getElementById('empty-state');
    const hiddenItems = document.getElementById('inquiry-hidden-items');

    function getCart() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || { items: [] };
        } catch {
            return { items: [] };
        }
    }

    function renderCart() {
        const cart = getCart();

        if (cart.items.length === 0) {
            listEl.innerHTML = '';
            formEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            return;
        }

        emptyEl.classList.add('hidden');
        formEl.classList.remove('hidden');

        let html = '';
        cart.items.forEach(function (item, idx) {
            var imgSrc = getItemImage(item);
            var detailUrl = '/products/' + (item.slug || '');
            html += `
            <div class="inquiry-item flex items-center gap-4 bg-elevated border border-border rounded-[12px] p-4" data-index="${idx}">
                <!-- Product Image Thumbnail -->
                <a href="${detailUrl}" class="shrink-0 w-16 h-16 rounded-[10px] overflow-hidden bg-surface border border-border">
                    ${imgSrc
                        ? '<img src="' + imgSrc + '" alt="' + item.name + '" class="w-full h-full object-cover">'
                        : '<div class="w-full h-full flex items-center justify-center text-text-subtle"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>'
                    }
                </a>
                <!-- Product Info -->
                <div class="flex-1 min-w-0">
                    <a href="${detailUrl}" class="text-sm font-semibold text-text hover:text-accent transition-colors truncate block">${item.name}</a>
                    ${item.slug ? '<p class="text-[11px] text-text-subtle mt-0.5">SKU: ' + item.slug.toUpperCase() + '</p>' : ''}
                </div>
                <!-- Quantity Controls -->
                <div class="flex items-center gap-1 shrink-0">
                    <button onclick="window.inquiryUpdateQty(${item.product_id}, ${item.qty - 1})"
                        class="qty-btn qty-minus p-1.5 text-text-muted hover:text-text hover:bg-surface-2 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"></path>
                        </svg>
                    </button>
                    <span class="qty-value w-8 text-center text-sm font-semibold text-text" data-product-id="${item.product_id}">${item.qty}</span>
                    <button onclick="window.inquiryUpdateQty(${item.product_id}, ${item.qty + 1})"
                        class="qty-btn qty-plus p-1.5 text-text-muted hover:text-text hover:bg-surface-2 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"></path>
                        </svg>
                    </button>
                    <button onclick="window.inquiryRemoveItem(${item.product_id})"
                        class="p-1.5 ml-2 text-text-muted hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                        </svg>
                    </button>
                </div>
            </div>`;
        });
        listEl.innerHTML = html;

        // Generate hidden inputs for form submission
        let hiddenHtml = '';
        cart.items.forEach(function (item, idx) {
            hiddenHtml += `<input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">`;
            hiddenHtml += `<input type="hidden" name="items[${idx}][qty]" value="${item.qty}">`;
        });
        hiddenItems.innerHTML = hiddenHtml;
    }

    window.inquiryRemoveItem = function (productId) {
        const { removeItem, updateBar } = window;
        if (typeof removeItem === 'function') {
            removeItem(productId);
        }
        renderCart();
        if (typeof updateBar === 'function') updateBar();
    };

    window.inquiryUpdateQty = function (productId, qty) {
        qty = Math.max(1, parseInt(qty) || 1);
        const { updateQty, updateBar } = window;
        if (typeof updateQty === 'function') {
            updateQty(productId, qty);
        }
        // Update inline display
        const qtySpan = document.querySelector(`.qty-value[data-product-id="${productId}"]`);
        if (qtySpan) qtySpan.textContent = qty;
        // Regenerate hidden inputs with updated qty
        const cart = getCart();
        let hiddenHtml = '';
        cart.items.forEach(function (item, idx) {
            hiddenHtml += `<input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">`;
            hiddenHtml += `<input type="hidden" name="items[${idx}][qty]" value="${item.qty}">`;
        });
        hiddenItems.innerHTML = hiddenHtml;
        if (typeof updateBar === 'function') updateBar();
    };

    renderCart();
});
</script>
@endsection
