@php
    $announcements = \App\Models\Announcement::published()->latest('published_at')->limit(10)->get();
    $count = $announcements->count();
@endphp
@if($announcements->isNotEmpty())
    <div class="bg-amber-50 border-y border-amber-200 py-3 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4">
            {{-- Mobile: title + nav in one row, 1 notice card below --}}
            <div
                class="sm:hidden"
                x-data="{
                    index: 0,
                    total: {{ $count }},
                    init() {
                        setInterval(() => { this.index = (this.index + 1) % this.total }, 4000);
                    }
                }"
            >
                <div class="flex items-center gap-2">
                    <span class="text-amber-800 font-semibold shrink-0">{{ settings('ticker_label', 'Notices:') }}</span>
                    @if($count > 1)
                        <div class="flex items-center gap-1 ml-auto shrink-0">
                            <button type="button" aria-label="Previous notice" class="p-1.5 text-amber-800 hover:text-amber-900 rounded touch-manipulation"
                                @click="index = (index - 1 + total) % total">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="text-xs text-amber-800 tabular-nums min-w-[2rem] text-center" x-text="(index + 1) + '/' + total"></span>
                            <button type="button" aria-label="Next notice" class="p-1.5 text-amber-800 hover:text-amber-900 rounded touch-manipulation"
                                @click="index = (index + 1) % total">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    @endif
                </div>
                <div class="mt-2 w-full min-w-0">
                    @foreach($announcements as $announcement)
                        <a
                            href="{{ route('notices.show', $announcement) }}"
                            class="block rounded-lg border border-amber-200 bg-white p-3 text-gray-700 shadow-sm hover:border-amber-300 hover:text-amber-700 hover:shadow"
                            x-show="index === {{ $loop->index }}"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                        >
                            <span class="block line-clamp-2 text-sm font-medium leading-snug">{{ $announcement->title }}</span>
                            @if($announcement->published_at)
                                <span class="text-gray-500 text-xs mt-1 block">({{ $announcement->published_at->format('M d, Y') }})</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Desktop: title + nav in one row, 3 notice cards below --}}
            <div
                class="hidden sm:block"
                x-data="{
                    index: 0,
                    total: {{ $count }},
                    visible: 3,
                    get maxIndex() { return Math.max(0, this.total - this.visible) },
                    init() {
                        setInterval(() => { this.index = (this.index + 1) % (this.maxIndex + 1) }, 5000);
                    }
                }"
            >
                <div class="flex items-center gap-4">
                    <span class="text-amber-800 font-semibold shrink-0">{{ settings('ticker_label', 'Notices:') }}</span>
                    @if($count > 3)
                        <div class="flex items-center gap-1 ml-auto shrink-0">
                            <button type="button" aria-label="Previous" class="p-1.5 text-amber-800 hover:text-amber-900 rounded"
                                @click="index = (index - 1 + maxIndex + 1) % (maxIndex + 1)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="text-xs text-amber-800 tabular-nums min-w-[2.5rem] text-center" x-text="(index + 1) + '–' + Math.min(index + visible, total) + ' / ' + total"></span>
                            <button type="button" aria-label="Next" class="p-1.5 text-amber-800 hover:text-amber-900 rounded"
                                @click="index = (index + 1) % (maxIndex + 1)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    @endif
                </div>
                <div class="mt-3 w-full overflow-hidden">
                    <div
                        class="flex flex-nowrap transition-transform duration-300 ease-out"
                        :style="'width: {{ $count * 100 / 3 }}%; transform: translate3d(-' + (index * (100 / total)) + '%, 0, 0)'"
                    >
                        @foreach($announcements as $announcement)
                            <a
                                href="{{ route('notices.show', $announcement) }}"
                                class="flex-none rounded-lg border border-amber-200 bg-white p-4 pr-5 shadow-sm hover:border-amber-300 hover:text-amber-700 hover:shadow block box-border min-h-[4.5rem]"
                                style="width: {{ 100 / $count }}%; min-width: {{ 100 / $count }}%;"
                            >
                                <span class="block line-clamp-2 text-sm font-medium text-gray-700">{{ $announcement->title }}</span>
                                @if($announcement->published_at)
                                    <span class="text-gray-500 text-xs mt-1 block">({{ $announcement->published_at->format('M d, Y') }})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
