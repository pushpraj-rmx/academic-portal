@extends('layouts.public')

@section('title', $page->meta_description ?: $page->title . ' - ' . config('app.name'))

@section('content')
    @include('components.public.announcement-ticker')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <section class="mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $page->title }}</h1>
            <div class="prose prose-lg max-w-none text-gray-600">
                {!! $page->body !!}
            </div>
        </section>

        <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('pages.show', ['page' => 'about']) }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:border-amber-300 hover:shadow transition">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">About Us</h2>
                <p class="text-gray-600 text-sm">Learn about our history and mission.</p>
            </a>
            <a href="{{ route('notices.index') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:border-amber-300 hover:shadow transition">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Notices</h2>
                <p class="text-gray-600 text-sm">Latest announcements and notices.</p>
            </a>
            <a href="{{ route('pages.show', ['page' => 'director-message']) }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:border-amber-300 hover:shadow transition">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Director's Message</h2>
                <p class="text-gray-600 text-sm">A message from our Director.</p>
            </a>
        </section>
    </div>
@endsection
