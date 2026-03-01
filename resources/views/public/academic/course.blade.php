@extends('layouts.public')

@section('title', $course->name . ' - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <p class="mb-4">
            <a href="{{ route('academic.category.show', $course->courseCategory) }}" class="text-amber-600 hover:text-amber-700 font-medium">&larr; Back to {{ $course->courseCategory->name }}</a>
        </p>

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $course->name }}</h1>
            @if($course->duration || $course->intake || $course->eligibility)
                <ul class="mt-2 text-gray-600 space-y-1">
                    @if($course->duration)<li>{{ __('public.duration') }}: {{ $course->duration }}</li>@endif
                    @if($course->intake)<li>{{ __('public.intake') }}: {{ $course->intake }}</li>@endif
                    @if($course->eligibility)<li>{{ __('public.eligibility') }}: {{ $course->eligibility }}</li>@endif
                </ul>
            @endif
            @if($course->description)
                <div class="prose prose-lg max-w-none text-gray-700 mt-4">
                    {!! $course->description !!}
                </div>
            @endif
        </header>

        @if($course->specializations->isNotEmpty())
            <section class="mb-10">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('public.specializations') }}</h2>
                <ul class="space-y-4">
                    @foreach($course->specializations as $spec)
                        <li class="bg-white rounded-lg border border-gray-200 p-4">
                            <h3 class="font-semibold text-gray-900">{{ $spec->name }}</h3>
                            @if($spec->description)
                                <p class="text-gray-600 text-sm mt-1">{{ $spec->description }}</p>
                            @endif
                            @if($spec->industry_relevance)
                                <p class="text-gray-600 text-sm mt-1"><span class="font-medium">{{ __('public.industry_relevance') }}:</span> {{ Str::limit($spec->industry_relevance, 200) }}</p>
                            @endif
                            @if($spec->career_outcomes)
                                <p class="text-gray-600 text-sm mt-1"><span class="font-medium">{{ __('public.career_outcomes') }}:</span> {{ Str::limit($spec->career_outcomes, 200) }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if($course->syllabi->isNotEmpty())
            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('public.syllabus') }}</h2>
                <ul class="space-y-2">
                    @foreach($course->syllabi as $syllabus)
                        <li>
                            <a href="{{ route('academic.syllabus.download', $syllabus) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium">
                                @if($syllabus->academic_year || $syllabus->version)
                                    {{ $syllabus->academic_year ?: '' }}{{ $syllabus->academic_year && $syllabus->version ? ' — ' : '' }}{{ $syllabus->version ?: __('public.syllabus') }}
                                @else
                                    {{ __('public.download_syllabus_pdf') }}
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
@endsection
