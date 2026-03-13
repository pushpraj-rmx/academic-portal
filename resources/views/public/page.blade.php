@extends('layouts.public')

@section('title', $page->meta_description ?: $page->title . ' - ' . config('app.name'))

@section('content')
    <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <article>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>
            <div class="prose prose-lg max-w-none text-gray-700">
                {!! $page->body !!}
            </div>
        </article>
    </div>
    @if($page->slug === 'about')
        @include('components.public.employers-carousel')
    @endif
@endsection
