@php
    $welcomeTitle = settings('welcome_section_title');
    $welcomeBody = settings('welcome_section_body');
    $hasTitle = $welcomeTitle !== null && $welcomeTitle !== '';
    $hasBody = $welcomeBody !== null && $welcomeBody !== '';
    $hasContent = $hasTitle || $hasBody;
    $bodyHtml = $hasBody ? '<p>' . nl2br(e($welcomeBody)) . '</p>' : '';
@endphp
@if($hasContent)
<section class="bg-white py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($hasTitle)
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
            {{ $welcomeTitle }}
        </h2>
        @endif
        @if($hasBody)
        <div class="mt-4 prose prose-lg max-w-none text-gray-700">
            {!! $bodyHtml !!}
        </div>
        @endif
    </div>
</section>
@endif
