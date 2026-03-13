@extends('layouts.public')

@section('title', 'Notices & Announcements - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">{{ settings('section_notices_announcements', 'Notices & Announcements') }}</h1>

        @if($announcements->isEmpty())
            <p class="text-gray-600">{{ settings('empty_notices', 'No announcements at the moment.') }}</p>
        @else
            <ul class="space-y-6">
                @foreach($announcements as $announcement)
                    <li class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow transition">
                        <a href="{{ route('notices.show', $announcement) }}" class="block group">
                            <h2 class="text-lg font-semibold text-gray-900 group-hover:text-amber-600 transition">{{ $announcement->title }}</h2>
                            @if($announcement->published_at)
                                <p class="text-sm text-gray-500 mt-1">{{ $announcement->published_at->format('F j, Y') }}</p>
                            @endif
                            @if($announcement->body)
                                <p class="text-gray-600 mt-2 line-clamp-2">{{ Str::limit(strip_tags($announcement->body), 120) }}</p>
                            @endif
                            @if($announcement->attachment)
                                <span class="inline-block mt-2 text-sm text-amber-600 font-medium">{{ __('public.pdf_attached') }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-8">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
@endsection
