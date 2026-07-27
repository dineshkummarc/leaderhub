@extends('layouts.app')

@section('title', 'Hubungi Kami — LeaderHub')

@section('content')
<!-- Header Banner -->
<div class="bg-surface border-b border-border py-12 transition-colors duration-200">
    <div class="max-w-[1120px] mx-auto px-4 sm:px-6">
        <span class="text-xs font-semibold uppercase tracking-wider text-accent mb-2 block">Contact</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold font-display text-text mb-2">Hubungi Kami</h1>
        <p class="text-sm text-text-muted max-w-xl">Tim LeaderHub siap membantu Anda. Silakan hubungi melalui form atau kontak di bawah.</p>
    </div>
</div>

<div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
        <!-- Contact Info -->
        <div class="md:col-span-5 space-y-8">
            <div>
                <h2 class="text-xl font-bold font-heading text-text mb-6">Informasi Kontak</h2>
                <ul class="space-y-5">
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-accent-soft rounded-[10px] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-text">Email</p>
                            <a href="mailto:info@leaderhub.id" class="text-sm text-text-muted hover:text-accent transition-colors">info@leaderhub.id</a>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-accent-soft rounded-[10px] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-text">Telepon</p>
                            <a href="tel:+6281234567890" class="text-sm text-text-muted hover:text-accent transition-colors">+62 812 3456 7890</a>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-accent-soft rounded-[10px] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-text">Alamat</p>
                            <p class="text-sm text-text-muted">Jakarta, Indonesia</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="bg-elevated border border-border rounded-[14px] p-6">
                <h3 class="text-sm font-bold text-text mb-3">Jam Operasional</h3>
                <ul class="space-y-2 text-sm text-text-muted">
                    <li class="flex justify-between">
                        <span>Senin - Jumat</span>
                        <span class="font-medium text-text">08:00 - 17:00</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Sabtu</span>
                        <span class="font-medium text-text">08:00 - 14:00</span>
                    </li>
                    <li class="flex justify-between">
                        <span>Minggu & Hari Libur</span>
                        <span class="font-medium text-text-muted">Tutup</span>
                    </li>
                </ul>
            </div>

            <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-3 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-[10px] shadow-sm transition-colors">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <span>Hubungi via WhatsApp</span>
            </a>
        </div>

        <!-- Contact Form -->
        <div class="md:col-span-7">
            <div class="bg-elevated border border-border rounded-[14px] p-6 sm:p-8">
                <h2 class="text-lg font-bold font-heading text-text mb-6">Kirim Pesan</h2>
                <form action="https://formspree.io/f/your-form-id" method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-text mb-1">Nama Lengkap <span class="text-accent">*</span></label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                                placeholder="Nama Anda">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-text mb-1">Email <span class="text-accent">*</span></label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                                placeholder="email@example.com">
                        </div>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-text mb-1">Nomor Telepon</label>
                        <input type="tel" id="phone" name="phone"
                            class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-semibold text-text mb-1">Subjek</label>
                        <input type="text" id="subject" name="subject"
                            class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                            placeholder="Subjek pesan">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-semibold text-text mb-1">Pesan <span class="text-accent">*</span></label>
                        <textarea id="message" name="message" rows="5" required
                            class="w-full px-4 py-2.5 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all"
                            placeholder="Tulis pesan Anda..."></textarea>
                    </div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-[10px] shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                        </svg>
                        <span>Kirim Pesan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
