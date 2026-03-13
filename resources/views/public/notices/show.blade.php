@extends('layouts.public')

@section('title', $announcement->title . ' - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article>
            <header class="mb-6">
                @if($announcement->published_at)
                    <p class="text-sm text-gray-500 mb-2">{{ $announcement->published_at->format('F j, Y') }}</p>
                @endif
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $announcement->title }}</h1>
            </header>

            @if($announcement->body)
                <div class="prose prose-lg max-w-none text-gray-700 mb-8">
                    {!! $announcement->body !!}
                </div>
            @endif

            @if($announcement->attachment)
                <p class="mt-6">
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($announcement->attachment) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-medium">
                        {{ __('public.download_pdf') }}
                    </a>
                </p>
            @endif
        </article>

        <p class="mt-8">
            <a href="{{ route('notices.index') }}" class="text-amber-600 hover:text-amber-700 font-medium">&larr; {{ settings('back_to_notices', 'Back to Notices') }}</a>
        </p>
    </div>
@endsection
