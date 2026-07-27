@extends('layouts.app')

@section('title', $post->title . ' — Blog LeaderHub')
@section('seo_description', $post->seo_description ?? strip_tags($post->excerpt ?? ''))
@section('seo_image', $post->featured_image)

@section('head')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Article",
  "headline": "{{ $post->title }}",
  "description": "{{ $post->seo_description ?? Str::limit(strip_tags($post->content), 150) }}",
  @if($post->featured_image)
  "image": "{{ $post->featured_image }}",
  @endif
  "datePublished": "{{ $post->published_at ? $post->published_at->toIso8601String() : '' }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": {
    "@@type": "Person",
    "name": "{{ $post->author?->name ?? 'LeaderHub' }}"
  },
  "publisher": {
    "@@type": "Organization",
    "name": "LeaderHub",
    "logo": {
      "@@type": "ImageObject",
      "url": "{{ asset('logo/main-logo/main-blHD.png') }}"
    }
  }
}
</script>
@endsection

@section('content')
<div class="max-w-[1120px] mx-auto px-4 sm:px-6 py-10">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-text-muted mb-8 overflow-x-auto pb-2">
        <a href="{{ route('home') }}" class="hover:text-accent transition-colors whitespace-nowrap">Home</a>
        <svg class="w-3.5 h-3.5 text-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
        </svg>
        <a href="{{ route('blog.index') }}" class="hover:text-accent transition-colors whitespace-nowrap">Blog</a>
        @if($post->category)
            <svg class="w-3.5 h-3.5 text-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-accent transition-colors whitespace-nowrap">{{ $post->category->name }}</a>
        @endif
        <svg class="w-3.5 h-3.5 text-text-subtle shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
        </svg>
        <span class="text-text font-semibold truncate">{{ $post->title }}</span>
    </nav>

    <article class="max-w-[720px] mx-auto">
        <!-- Featured Image -->
        @if($post->featured_image)
            <div class="aspect-[16/10] rounded-[14px] overflow-hidden bg-surface mb-8">
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <!-- Category Badge -->
        @if($post->category)
            <span class="inline-block bg-accent-soft text-accent text-xs font-semibold px-3 py-1 rounded-full mb-4">
                {{ $post->category->name }}
            </span>
        @endif

        <!-- Title -->
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold font-heading text-text leading-tight mb-4">{{ $post->title }}</h1>

        <!-- Meta -->
        <div class="flex flex-wrap items-center gap-3 text-sm text-text-subtle mb-8 pb-6 border-b border-border">
            @if($post->author)
                <span class="font-medium text-text">{{ $post->author->name }}</span>
                <span class="w-1 h-1 rounded-full bg-text-subtle/40"></span>
            @endif
            <span>{{ $post->published_at ? $post->published_at->format('d F Y') : '' }}</span>
            @if($post->reading_time)
                <span class="w-1 h-1 rounded-full bg-text-subtle/40"></span>
                <span>{{ $post->reading_time }} min read</span>
            @endif
        </div>

        <!-- Content -->
        <div class="prose prose-slate dark:prose-invert max-w-none text-text leading-relaxed">
            {!! $post->content !!}
        </div>

        <!-- Tags -->
        @if($post->tags->count() > 0)
            <div class="flex flex-wrap items-center gap-2 mt-10 pt-6 border-t border-border">
                <span class="text-xs font-semibold text-text-muted mr-1">Tags:</span>
                @foreach($post->tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="inline-flex px-3 py-1 bg-elevated border border-border rounded-full text-xs font-medium text-text-muted hover:text-accent hover:border-accent transition-colors">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </article>

    <!-- Related Posts -->
    @if($related->count() > 0)
        <section class="mt-16 pt-12 border-t border-border">
            <h2 class="text-xl sm:text-2xl font-bold font-heading text-text mb-8">Artikel Terkait</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($related as $rel)
                    <article class="bg-elevated rounded-[14px] border border-border overflow-hidden hover:border-border-strong hover:shadow-md transition-all duration-200 group flex flex-col">
                        <a href="{{ route('blog.show', $rel->slug) }}" class="block relative aspect-[16/10] bg-surface overflow-hidden">
                            @if($rel->featured_image)
                                <img src="{{ $rel->featured_image }}" alt="{{ $rel->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-text-subtle bg-surface">
                                    <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                    </svg>
                                </div>
                            @endif
                        </a>
                        <div class="p-4 flex flex-col flex-1 gap-2">
                            @if($rel->category)
                                <span class="text-[11px] font-medium text-accent uppercase tracking-wider">{{ $rel->category->name }}</span>
                            @endif
                            <h3 class="text-sm font-semibold text-text line-clamp-2 leading-snug">
                                <a href="{{ route('blog.show', $rel->slug) }}" class="hover:text-accent transition-colors">{{ $rel->title }}</a>
                            </h3>
                            <div class="text-xs text-text-subtle mt-auto pt-2 border-t border-border/50">
                                {{ $rel->published_at ? $rel->published_at->format('d M Y') : '' }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
