@php
    $recruiters = \App\Models\Recruiter::query()
        ->active()
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
@endphp
@if($recruiters->isNotEmpty())
    <section class="bg-white py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">{{ settings('employers_section_title', settings('section_recruiters', 'Our Recruiters')) }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($recruiters as $recruiter)
                    <a href="{{ $recruiter->website ?: route('placements.recruiters') }}" target="{{ $recruiter->website ? '_blank' : '_self' }}" rel="noopener noreferrer" class="rounded-lg border border-gray-200 p-3 bg-gray-50 hover:border-amber-300 transition h-20 flex items-center justify-center">
                        @if($recruiter->logo_path)
                            <img src="{{ Storage::disk('public')->url($recruiter->logo_path) }}" alt="{{ $recruiter->name }}" class="max-h-12 w-auto object-contain">
                        @else
                            <span class="text-xs text-gray-600 text-center">{{ $recruiter->name }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
