@php
    $certifications = \App\Models\Certification::published()->ordered()->get();
@endphp
@if($certifications->isNotEmpty())
    <section class="bg-gray-50 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">{{ settings('certifications_section_title', 'Certifications') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($certifications as $certification)
                    <article class="bg-white rounded-lg border border-gray-200 p-4 flex items-center justify-center h-28 shadow-sm">
                        @if($certification->image)
                            <img src="{{ Storage::disk('public')->url($certification->image) }}" alt="{{ $certification->name }}" class="max-h-20 w-auto object-contain">
                        @else
                            <span class="text-gray-600 text-sm">{{ $certification->name }}</span>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
