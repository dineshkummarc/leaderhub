@extends('layouts.app')

@section('title', 'Blog — LeaderHub')

@section('head')
<meta property="og:title" content="Blog — LeaderHub">
<meta property="og:description" content="Tips, panduan, dan informasi terbaru seputar fitness equipment dan gaya hidup sehat dari LeaderHub.">
@endsection

@section('content')
<!-- Header Banner -->
<div class="bg-surface border-b border-border py-12 transition-colors duration-200">
    <div class="max-w-[1120px] mx-auto px-4 sm:px-6">
        <span class="text-xs font-semibold uppercase tracking-wider text-accent mb-2 block">Content</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold font-display text-text mb-2">Blog</h1>
        <p class="text-sm text-text-muted max-w-xl">Tips, panduan, dan informasi terbaru seputar fitness equipment dan gaya hidup sehat.</p>
    </div>
</div>

<div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-12">
    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row gap-4 mb-10">
        <form action="{{ route('blog.index') }}" method="GET" class="flex-1 flex flex-col sm:flex-row gap-3">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('tag'))
                <input type="hidden" name="tag" value="{{ request('tag') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel..." class="w-full pl-9 pr-3 py-2 bg-surface border border-border rounded-[10px] text-sm text-text placeholder:text-text-subtle focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                <svg class="w-4 h-4 text-text-subtle absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-[10px] transition-colors">Cari</button>
            @if(request()->anyFilled(['q', 'category', 'tag']))
                <a href="{{ route('blog.index') }}" class="px-4 py-2 bg-elevated border border-border rounded-[10px] text-xs font-semibold text-text hover:bg-surface transition-colors flex items-center">Reset</a>
            @endif
        </form>
    </div>

    <!-- Blog Grid -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($posts as $post)
                <article class="bg-elevated rounded-[14px] border border-border overflow-hidden hover:border-border-strong hover:shadow-md transition-all duration-200 group flex flex-col">
                    <a href="{{ route('blog.show', $post->slug) }}" class="block relative aspect-[16/10] bg-surface overflow-hidden">
                        @if($post->featured_image)
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-text-subtle bg-surface">
                                <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                </svg>
                            </div>
                        @endif

                        @if($post->category)
                            <span class="absolute top-3 left-3 bg-accent-soft text-accent text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-sm backdrop-blur-sm">
                                {{ $post->category->name }}
                            </span>
                        @endif
                    </a>

                    <div class="p-5 flex flex-col flex-1 gap-3">
                        @if($post->category)
                            <span class="text-[11px] font-medium text-text-subtle uppercase tracking-wider">
                                {{ $post->category->name }}
                            </span>
                        @endif

                        <h2 class="text-lg font-semibold font-heading text-text leading-snug line-clamp-2">
                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-accent transition-colors">
                                {{ $post->title }}
                            </a>
                        </h2>

                        @if($post->excerpt)
                            <p class="text-sm text-text-muted line-clamp-2 leading-relaxed">{{ $post->excerpt }}</p>
                        @endif

                        <div class="flex items-center gap-3 text-xs text-text-subtle mt-auto pt-3 border-t border-border/50">
                            @if($post->author)
                                <span>{{ $post->author->name }}</span>
                                <span class="w-1 h-1 rounded-full bg-text-subtle/40"></span>
                            @endif
                            <span>{{ $post->published_at ? $post->published_at->format('d M Y') : '' }}</span>
                            @if($post->reading_time)
                                <span class="w-1 h-1 rounded-full bg-text-subtle/40"></span>
                                <span>{{ $post->reading_time }} min read</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @else
        <div class="col-span-full py-16 text-center bg-elevated border border-border rounded-[14px] p-8">
            <svg class="w-12 h-12 text-text-subtle mx-auto mb-3 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
            </svg>
            <p class="text-base font-semibold text-text mb-1">Tidak ada artikel yang cocok</p>
            <p class="text-xs text-text-muted mb-4">Coba ubah kata kunci pencarian atau filter Anda.</p>
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-surface hover:bg-surface-2 border border-border rounded-[10px] text-xs font-semibold text-text transition-colors">
                Tampilkan Semua
            </a>
        </div>
    @endif
</div>
@endsection
