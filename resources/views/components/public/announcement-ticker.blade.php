@php
    $announcements = \App\Models\Announcement::published()->latest('published_at')->limit(5)->get();
@endphp
@if($announcements->isNotEmpty())
    <div class="bg-amber-50 border-y border-amber-200 py-2 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 flex items-center gap-4">
            <span class="text-amber-800 font-semibold shrink-0">{{ settings('ticker_label', 'Notices:') }}</span>
            <div class="flex gap-6 overflow-x-auto">
                @foreach($announcements as $announcement)
                    <a href="{{ route('notices.show', $announcement) }}" class="text-gray-700 hover:text-amber-700 hover:underline">
                        {{ $announcement->title }}
                        @if($announcement->published_at)
                            <span class="text-gray-500 text-sm">({{ $announcement->published_at->format('M d, Y') }})</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
