@php
    $image = settings('two_column_image');
    $hasContent = $image !== null && $image !== '';
@endphp
@if($hasContent)
    <x-public.two-column-section
        :image="$image"
        :imageAlt="settings('two_column_image_alt', '')"
        :title="settings('two_column_title')"
        :imageFirst="(bool) (settings('two_column_image_first', '1') === '1')"
        sectionClass="bg-gray-50 py-12"
    >
        @php $body = settings('two_column_body'); @endphp
        {!! $body ? '<p>' . nl2br(e($body)) . '</p>' : '' !!}
    </x-public.two-column-section>
@endif
