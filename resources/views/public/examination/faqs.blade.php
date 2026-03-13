@extends('layouts.public')

@section('title', 'Examination FAQs - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">Examination FAQs</h1>

        @if($faqs->isEmpty())
            <p class="text-gray-600">{{ settings('empty_examination_faqs', 'No FAQs available right now.') }}</p>
        @else
            <div class="space-y-4">
                @foreach($faqs as $faq)
                    <article class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $faq->question }}</h2>
                        <p class="text-gray-700 mt-2 whitespace-pre-line">{{ $faq->answer }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
