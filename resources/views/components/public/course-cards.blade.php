@php
    $courseCategories = \App\Models\CourseCategory::query()
        ->active()
        ->withCount('courses')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->limit(6)
        ->get();
@endphp
@if($courseCategories->isNotEmpty())
    <section class="bg-white py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ settings('section_courses', 'Courses') }}</h2>
                <a href="{{ route('academic.index') }}" class="text-amber-600 hover:text-amber-700 font-medium">View all</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courseCategories as $category)
                    <a href="{{ route('academic.category.show', $category) }}" class="block rounded-lg border border-gray-200 bg-gray-50 p-6 hover:border-amber-300 hover:shadow-sm transition">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ Str::limit($category->description, 90) }}</p>
                        <p class="mt-3 text-sm text-gray-500">{{ $category->courses_count }} {{ Str::plural('course', $category->courses_count) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
