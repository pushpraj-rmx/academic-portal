@extends('layouts.public')

@section('title', $category->name . ' - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <p class="mb-4">
            <a href="{{ route('academic.index') }}" class="text-amber-600 hover:text-amber-700 font-medium">&larr; {{ settings('back_to_courses', 'Back to Courses') }}</a>
        </p>
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-gray-600 mt-2">{{ $category->description }}</p>
            @endif
        </header>

        @if($courses->isEmpty())
            <p class="text-gray-600">{{ settings('empty_courses_in_category', 'No courses in this category at the moment.') }}</p>
        @else
            <ul class="space-y-6">
                @foreach($courses as $course)
                    <li class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow transition">
                        <a href="{{ route('academic.course.show', $course) }}" class="block group">
                            <h2 class="text-lg font-semibold text-gray-900 group-hover:text-amber-600 transition">{{ $course->name }}</h2>
                            @if($course->duration || $course->intake)
                                <p class="text-sm text-gray-500 mt-1">
                                    @if($course->duration) {{ $course->duration }} @endif
                                    @if($course->duration && $course->intake) &middot; @endif
                                    @if($course->intake) {{ __('public.intake') }}: {{ $course->intake }} @endif
                                </p>
                            @endif
                            @if($course->description)
                                <p class="text-gray-600 mt-2">{{ Str::limit(strip_tags($course->description), 120) }}</p>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
