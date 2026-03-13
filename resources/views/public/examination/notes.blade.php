@extends('layouts.public')

@section('title', 'Examination Notes - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">Examination Notes</h1>

        @if(settings('examination_notes'))
            <div class="prose prose-lg max-w-none text-gray-700 whitespace-pre-line">
                {{ settings('examination_notes') }}
            </div>
        @else
            <p class="text-gray-600">{{ settings('empty_examination_notes', 'No examination notes have been published yet. Please check back later.') }}</p>
        @endif
    </div>
@endsection
