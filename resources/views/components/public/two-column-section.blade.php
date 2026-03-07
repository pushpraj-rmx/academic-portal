@props([
    'image',
    'imageAlt' => '',
    'title' => null,
    'imageFirst' => true,
    'sectionClass' => 'bg-white py-12',
])

@php
    $imageUrl = $image;
    if (! str_starts_with($image, 'http') && ! str_starts_with($image, '/')) {
        $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($image);
    }
@endphp
<section {{ $attributes->merge(['class' => $sectionClass]) }}>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div class="{{ $imageFirst ? 'md:order-1' : 'md:order-2' }}">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $imageAlt }}"
                    class="w-full h-auto rounded-lg object-cover shadow-md"
                />
            </div>
            <div class="{{ $imageFirst ? 'md:order-2' : 'md:order-1' }}">
                @if($title)
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
                        {{ $title }}
                    </h2>
                @endif
                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! $slot !!}
                </div>
            </div>
        </div>
    </div>
</section>
