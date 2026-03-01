@extends('layouts.public')

@section('title', 'Students Verification - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Students Verification</h1>

        <form action="{{ route('students.verification.search') }}" method="get" class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
            <label for="query" class="block text-sm font-medium text-gray-700">Enrollment ID or Roll Number</label>
            <div class="mt-2 flex flex-col sm:flex-row gap-3">
                <input id="query" name="query" type="text" value="{{ old('query', $query) }}" required maxlength="255"
                    class="w-full rounded-md border-gray-300 focus:border-amber-500 focus:ring-amber-500"
                    placeholder="e.g. ENR-2026-001">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded-md hover:bg-amber-700">
                    Verify
                </button>
            </div>
            @error('query')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </form>

        @if($query !== null)
            <div class="mt-6 bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                @if($student)
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Student Found</h2>
                    <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-gray-500">Name</dt><dd class="text-gray-900 font-medium">{{ $student->user?->name }}</dd></div>
                        <div><dt class="text-gray-500">Enrollment ID</dt><dd class="text-gray-900 font-medium">{{ $student->enrollment_id }}</dd></div>
                        <div><dt class="text-gray-500">Roll Number</dt><dd class="text-gray-900 font-medium">{{ $student->roll_number }}</dd></div>
                        <div><dt class="text-gray-500">Course</dt><dd class="text-gray-900 font-medium">{{ $student->course?->name }}</dd></div>
                        <div><dt class="text-gray-500">Verification Status</dt><dd class="text-gray-900 font-medium capitalize">{{ $student->verification_status }}</dd></div>
                    </dl>
                @else
                    <p class="text-gray-700">{{ settings('empty_student_not_found', 'No student found with that roll number or enrollment ID.') }}</p>
                @endif
            </div>
        @endif
    </div>
@endsection
