@extends('layouts.app')

@section('title', 'Tentang Kami — LeaderHub')

@section('content')
<!-- Header Banner -->
<div class="bg-surface border-b border-border py-12 transition-colors duration-200">
    <div class="max-w-[1120px] mx-auto px-4 sm:px-6">
        <span class="text-xs font-semibold uppercase tracking-wider text-accent mb-2 block">About Us</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold font-display text-text mb-2">Tentang LeaderHub</h1>
        <p class="text-sm text-text-muted max-w-xl">Distributor peralatan fitness komersial terpercaya di Indonesia.</p>
    </div>
</div>

<div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-heading text-text mb-4">Mitra Terpercaya untuk <span class="text-accent">Commercial Gym</span></h2>
            <p class="text-sm text-text-muted leading-relaxed mb-4">
                LeaderHub adalah distributor resmi peralatan fitness komersial yang melayani hotel, fitness center, apartment, 
                dan commercial gym di seluruh Indonesia. Sejak berdiri, kami berkomitmen menghadirkan produk berkualitas 
                internasional dengan standar durabilitas tinggi.
            </p>
            <p class="text-sm text-text-muted leading-relaxed">
                Setiap produk yang kami distribusikan telah melalui proses seleksi ketat untuk memastikan keamanan, 
                kenyamanan, dan performa optimal bagi pengguna akhir. Kami juga menyediakan layanan konsultasi 
                layout gym, instalasi, dan purna jual.
            </p>
        </div>
        <div class="bg-elevated rounded-[14px] border border-border p-8 text-center">
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <div class="text-3xl font-extrabold text-accent font-display">500+</div>
                    <div class="text-xs text-text-muted mt-1">Commercial Gyms</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-accent font-display">50+</div>
                    <div class="text-xs text-text-muted mt-1">Model Peralatan</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-accent font-display">10+</div>
                    <div class="text-xs text-text-muted mt-1">Tahun Pengalaman</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-accent font-display">100%</div>
                    <div class="text-xs text-text-muted mt-1">Garansi Resmi</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-20">
        <h2 class="text-2xl font-bold font-heading text-text mb-8 text-center">Mengapa Memilih LeaderHub?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-elevated border border-border rounded-[14px] p-6 text-center">
                <div class="w-12 h-12 bg-accent-soft rounded-[12px] flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-text mb-2">Produk Berkualitas</h3>
                <p class="text-xs text-text-muted leading-relaxed">Seluruh produk telah memenuhi standar internasional dengan material premium dan durabilitas tinggi.</p>
            </div>
            <div class="bg-elevated border border-border rounded-[14px] p-6 text-center">
                <div class="w-12 h-12 bg-accent-soft rounded-[12px] flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-text mb-2">Layanan Lengkap</h3>
                <p class="text-xs text-text-muted leading-relaxed">Konsultasi layout, instalasi, pelatihan penggunaan alat, hingga purna jual yang responsif.</p>
            </div>
            <div class="bg-elevated border border-border rounded-[14px] p-6 text-center">
                <div class="w-12 h-12 bg-accent-soft rounded-[12px] flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-text mb-2">Tim Profesional</h3>
                <p class="text-xs text-text-muted leading-relaxed">Tim konsultan dan teknisi berpengalaman siap membantu dari perencanaan hingga instalasi.</p>
            </div>
        </div>
    </div>
</div>
@endsection
