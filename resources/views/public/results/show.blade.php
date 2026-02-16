@extends('layouts.public')
@section('title', ($student ? 'Results - ' . $student->user->name : 'Results') . ' - ' . config('app.name'))
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Exam Results</h1>

    @if(! $student)
        <p class="text-gray-600">No student found with that roll number or enrollment ID.</p>
        <a href="{{ route('results.index') }}" class="inline-block mt-4 text-amber-600 hover:text-amber-700 font-medium">Search again</a>
    @else
    <div class="mb-8">
        <p class="text-gray-700"><strong>Name:</strong> {{ $student->user->name }}</p>
        <p class="text-gray-700"><strong>Course:</strong> {{ $student->course->name }}</p>
        <p class="text-gray-700"><strong>Roll No:</strong> {{ $student->roll_number }}</p>
    </div>

    @if(empty($resultsBySession))
        <p class="text-gray-600">No published results found for this student.</p>
    @else
        <div class="space-y-8">
            @foreach($resultsBySession as $item)
                @php
                    $session = $item['session'];
                    $result = $item['result'];
                @endphp
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $session->name }}</h2>
                    <p class="text-sm text-gray-500 mb-4">{{ $session->academic_year }} · {{ $session->session_type }}</p>

                    <table class="min-w-full divide-y divide-gray-200 mb-4">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th scope="col" class="py-2 px-3 text-right text-xs font-medium text-gray-500 uppercase">Marks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($result['subject_marks_detail'] ?? [] as $sm)
                                <tr>
                                    <td class="py-2 px-3 text-sm text-gray-900">{{ $sm->subject->name ?? $sm->subject_id }}</td>
                                    <td class="py-2 px-3 text-sm text-right">{{ $sm->is_absent ? 'Absent' : ($sm->marks_obtained ?? '—') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="flex flex-wrap gap-4 text-sm">
                        <span><strong>Total:</strong> {{ number_format($result['marks_obtained'], 2) }} / {{ number_format($result['total_marks'], 2) }}</span>
                        <span><strong>Percentage:</strong> {{ number_format($result['percentage'], 2) }}%</span>
                        <span><strong>Grade:</strong> {{ $result['grade'] }}</span>
                        <span>
                            <strong>Status:</strong>
                            @if($result['is_passed'])
                                <span class="text-green-600 font-medium">Passed</span>
                            @else
                                <span class="text-red-600 font-medium">Failed</span>
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('results.index') }}" class="inline-block mt-8 text-amber-600 hover:text-amber-700 font-medium">Search again</a>
    @endif
</div>
@endsection
