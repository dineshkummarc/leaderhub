@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-20">
    <h1 class="text-4xl font-bold mb-8 text-primary-600 dark:text-primary-500">{{ $title }}</h1>
    <div class="prose dark:prose-invert max-w-none text-slate-800 dark:text-slate-200">
        {!! $content !!}
    </div>
</div>
@endsection
