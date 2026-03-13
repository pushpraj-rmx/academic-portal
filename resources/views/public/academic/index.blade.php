@extends('layouts.public')
@section('title', 'Courses - ' . config('app.name'))
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">{{ settings('section_courses', 'Courses') }}</h1>
    @if($categories->isEmpty())
        <p class="text-gray-600">{{ settings('empty_course_categories', 'No course categories available.') }}</p>
    @else
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @foreach($categories as $category)
                <li class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                    <a href="{{ route('academic.category.show', $category) }}" class="block group">
                        @if($category->image_path)
                            <img
                                src="{{ asset('storage/' . ltrim($category->image_path, '/')) }}"
                                alt="{{ $category->image_alt ?? $category->name }}"
                                class="w-full h-44 object-cover rounded-md mb-4"
                                loading="lazy"
                            />
                        @endif
                        <h2 class="text-xl font-semibold text-gray-900 group-hover:text-amber-600">{{ $category->name }}</h2>
                        @if($category->description)
                            <p class="text-gray-600 mt-2">{{ Str::limit(strip_tags($category->description), 160) }}</p>
                        @endif
                        <p class="text-sm text-gray-500 mt-2">{{ $category->courses_count }} {{ Str::plural('course', $category->courses_count) }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
