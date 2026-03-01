@extends('layouts.public')

@section('title', $page->meta_description ?: $page->title . ' - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>
            <div class="prose prose-lg max-w-none text-gray-700">
                {!! $page->body !!}
            </div>
        </article>
    </div>
@endsection
