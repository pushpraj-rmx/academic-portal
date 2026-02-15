@extends('layouts.public')
@section('title', 'Courses - ' . config('app.name'))
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Courses</h1>
    @if($categories->isEmpty())
        <p class="text-gray-600">No course categories available.</p>
    @else
        <ul class="space-y-8">
            @foreach($categories as $category)
                <li class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                    <a href="{{ route('academic.category.show', $category) }}" class="block group">
                        <h2 class="text-xl font-semibold text-gray-900 group-hover:text-amber-600">{{ $category->name }}</h2>
                        @if($category->description)
                            <p class="text-gray-600 mt-2">{{ Str::limit($category->description, 160) }}</p>
                        @endif
                        <p class="text-sm text-gray-500 mt-2">{{ $category->courses_count }} {{ Str::plural('course', $category->courses_count) }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
