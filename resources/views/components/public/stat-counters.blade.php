@php
    $stats = \App\Models\StatCounter::published()->ordered()->get();
@endphp
@if($stats->isNotEmpty())
    <section class="bg-gray-50 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">{{ settings('stats_section_title', 'Achievements') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($stats as $stat)
                    <article class="bg-white rounded-lg border border-gray-200 p-6 text-center shadow-sm">
                        <p class="text-3xl font-bold text-amber-600">{{ number_format($stat->value) }}{{ $stat->suffix }}</p>
                        <p class="mt-2 text-gray-700 font-medium">{{ $stat->label }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
