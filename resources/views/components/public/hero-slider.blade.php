@php
    $slides = \App\Models\HeroSlide::published()->ordered()->get();
@endphp
@if($slides->isNotEmpty())
    <section class="relative bg-gray-900 text-white" x-data="{ index: 0, total: {{ $slides->count() }} }" x-init="setInterval(() => { index = (index + 1) % total }, 6000)">
        <div class="relative overflow-hidden">
            @foreach($slides as $slide)
                <div x-show="index === {{ $loop->index }}" x-transition.opacity.duration.700ms class="relative">
                    @if($slide->image)
                        <img src="{{ Storage::disk('public')->url($slide->image) }}" alt="{{ $slide->title }}" class="h-[24rem] w-full object-cover opacity-60">
                    @else
                        <div class="h-[24rem] w-full bg-gradient-to-r from-gray-900 to-amber-700"></div>
                    @endif
                    <div class="absolute inset-0 flex items-center">
                        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="max-w-2xl">
                                <h1 class="text-3xl sm:text-5xl font-bold">{{ $slide->title }}</h1>
                                @if($slide->subtitle)
                                    <p class="mt-3 text-base sm:text-lg text-gray-100">{{ $slide->subtitle }}</p>
                                @endif
                                @if($slide->button_text && $slide->button_link)
                                    <a href="{{ $slide->button_link }}" class="inline-flex mt-6 px-5 py-3 rounded-md bg-amber-600 text-white font-semibold hover:bg-amber-700">
                                        {{ $slide->button_text }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($slides->count() > 1)
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2">
                @foreach($slides as $slide)
                    <button type="button" @click="index = {{ $loop->index }}" class="h-2 w-6 rounded-full bg-white/40" :class="{ 'bg-white': index === {{ $loop->index }} }" aria-label="Slide {{ $loop->iteration }}"></button>
                @endforeach
            </div>
        @endif
    </section>
@endif
