@extends('layouts.public')

@section('title', 'Download Exam Form - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">Download Exam Form</h1>

        @if($forms->isEmpty())
            <p class="text-gray-600">{{ settings('empty_exam_forms', 'No exam forms available right now.') }}</p>
        @else
            <ul class="space-y-4">
                @foreach($forms as $form)
                    <li class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">{{ $form->name }}</h2>
                                @if($form->description)
                                    <p class="text-gray-600 mt-1">{{ $form->description }}</p>
                                @endif
                            </div>
                            <a href="{{ route('downloads.form', $form) }}" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded-md hover:bg-amber-700">
                                Download
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
