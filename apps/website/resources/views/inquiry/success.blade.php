@extends('layouts.app')

@section('title', 'Inquiry Terkirim — LeaderHub')

@section('content')
<div class="max-w-[600px] mx-auto px-4 sm:px-6 py-20 text-center">
    <!-- Success Icon -->
    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
        </svg>
    </div>

    <h1 class="text-2xl sm:text-3xl font-bold font-heading text-text mb-3">Inquiry Terkirim!</h1>
    <p class="text-text-muted mb-8 max-w-md mx-auto">Terima kasih, inquiry Anda telah kami terima. Tim LeaderHub akan menghubungi Anda segera.</p>

    <!-- Inquiry Number -->
    <div class="inline-block bg-elevated border border-border rounded-[14px] p-6 mb-8">
        <p class="text-xs text-text-muted mb-1">Nomor Inquiry</p>
        <p class="text-2xl font-bold font-mono text-accent tracking-wider">{{ $inquiry->inquiry_number }}</p>
    </div>

    <!-- Items Summary -->
    @if($inquiry->items->count() > 0)
        <div class="bg-elevated border border-border rounded-[14px] p-6 mb-8 text-left">
            <h2 class="text-sm font-semibold text-text mb-4">Produk yang Diminati</h2>
            <div class="space-y-3">
                @foreach($inquiry->items as $item)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-text">{{ $item->product?->name ?? 'Product #' . $item->product_id }}</span>
                        <span class="text-text-muted">{{ $item->quantity }} unit</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-[10px] shadow-sm transition-colors">
        <span>Kembali ke Beranda</span>
    </a>
</div>
@endsection
