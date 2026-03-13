@php
    $testimonials = \App\Models\Testimonial::published()->ordered()->get();
    $title = settings('testimonial_section_title');
    $subtitle = settings('testimonial_section_subtitle');
    $count = $testimonials->count();
@endphp
@if($testimonials->isNotEmpty())
    <section class="bg-white py-16" aria-labelledby="testimonials-heading">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <header class="mb-10">
                @if($title)
                    <h2 id="testimonials-heading" class="text-2xl sm:text-3xl font-bold text-gray-800 uppercase tracking-tight">{{ $title }}</h2>
                @endif
                @if($subtitle)
                    <p class="mt-2 text-gray-500 max-w-2xl">{{ $subtitle }}</p>
                @endif
                <div class="mt-3 h-1 w-16 bg-red-500 rounded" aria-hidden="true"></div>
            </header>

            <div class="relative"
                x-data="{
                    index: 0,
                    total: {{ $count }},
                    visible: 2,
                    get maxIndex() { return Math.max(0, this.total - this.visible) },
                    prev() { this.index = Math.max(0, this.index - 1) },
                    next() {
                        if (this.index >= this.maxIndex) { this.index = 0; }
                        else { this.index = this.index + 1; }
                    },
                    get translateX() { return this.total > 0 ? -this.index * (100 * this.visible / this.total) : 0 },
                    init() {
                        const update = () => { this.visible = window.innerWidth >= 768 ? 2 : 1 };
                        update();
                        window.addEventListener('resize', update);
                        setInterval(() => { this.next(); }, 5000);
                    }
                }"
                :style="'--visible: ' + visible + '; --total: ' + total"
            >
                <button type="button"
                    @click="prev()"
                    x-show="index > 0"
                    x-transition
                    class="absolute left-0 top-1/2 -translate-y-1/2 z-10 flex items-center justify-center w-12 h-12 rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    aria-label="Previous testimonial"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button type="button"
                    @click="next()"
                    x-show="index < maxIndex"
                    x-transition
                    class="absolute right-0 top-1/2 -translate-y-1/2 z-10 flex items-center justify-center w-12 h-12 rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    aria-label="Next testimonial"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>

                <div class="overflow-hidden">
                    <div class="flex flex-nowrap transition-transform duration-300 ease-out"
                        :style="'width: calc(var(--total) * (100% / var(--visible))); transform: translateX(' + translateX + '%)'"
                    >
                        @foreach($testimonials as $testimonial)
                            <div class="flex-shrink-0 px-2 sm:px-3" :style="'width: calc(100% / var(--total))'">
                                <article class="relative bg-gray-100 rounded-lg shadow-md p-6 sm:p-8 pt-20 pb-12 pr-10 text-gray-700 h-full overflow-visible">
                                    <span class="absolute top-4 left-4 text-5xl sm:text-6xl text-red-500 font-serif leading-none select-none" aria-hidden="true">"</span>
                                    <span class="absolute bottom-6 right-6 text-5xl sm:text-6xl text-red-500 font-serif leading-none select-none" aria-hidden="true">"</span>
                                    <div class="flex flex-col items-center text-center">
                                        @if($testimonial->avatar)
                                            <img src="{{ Storage::disk('public')->url($testimonial->avatar) }}" alt="" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md -mt-16 mb-3" loading="lazy">
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-gray-300 border-4 border-white shadow-md -mt-16 mb-3 flex items-center justify-center text-gray-500 text-2xl font-semibold" aria-hidden="true">
                                                {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <h3 class="font-bold text-gray-800">{{ $testimonial->name }}</h3>
                                        <p class="mt-3 text-sm sm:text-base">{{ $testimonial->body }}</p>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
