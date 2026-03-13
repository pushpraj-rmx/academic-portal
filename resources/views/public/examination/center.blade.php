@extends('layouts.public')

@section('title', 'Examination Center - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Examination Center</h1>
        @if(settings('examination_center_description'))
            <div class="prose prose-sm sm:prose max-w-none text-gray-700 mb-6">
                {!! settings('examination_center_description') !!}
            </div>
        @endif

        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Address</h2>
            <p class="text-gray-700 whitespace-pre-line">{{ settings('examination_center_address', settings('footer_address', 'Address will be updated soon.')) }}</p>
        </div>

        @if(settings('examination_center_map_embed'))
            <div class="mt-8 bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                <div class="aspect-video w-full overflow-hidden rounded-md">
                    {!! settings('examination_center_map_embed') !!}
                </div>
            </div>
        @endif
    </div>
@endsection
