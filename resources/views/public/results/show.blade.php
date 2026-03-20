@extends('layouts.public')
@section('title', ($student ? 'Results - ' . $student->user->name : 'Results') . ' - ' . config('app.name'))
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">{{ settings('section_exam_results', 'Exam Results') }}</h1>

    @if(! $student)
        <p class="text-gray-600">{{ settings('empty_student_not_found', 'No student found with that roll number or enrollment ID.') }}</p>
        <a href="{{ route('results.index') }}" class="inline-block mt-4 text-amber-600 hover:text-amber-700 font-medium">{{ __('public.search_again') }}</a>
    @else
        @if(count($availableSessions ?? []) > 0)
            <form action="{{ route('results.search') }}" method="get" class="mb-6 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div class="w-full sm:w-2/3">
                        <label for="exam_session_id" class="block text-sm font-medium text-gray-700">
                            Exam session
                        </label>
                        <select name="exam_session_id" id="exam_session_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"
                        >
                            @foreach($availableSessions as $s)
                                <option value="{{ $s->id }}" @selected($selectedSessionId === $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full sm:w-auto">
                        <input type="hidden" name="query" value="{{ $query }}">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded-md hover:bg-amber-700">
                            View
                        </button>
                    </div>
                </div>
            </form>
        @endif

    @if(empty($resultsBySession))
        <p class="text-gray-600">
            @if(filled($selectedSessionId))
                No published results found for the selected exam session.
            @else
                {{ settings('empty_no_results', 'No published results found for this student.') }}
            @endif
        </p>
    @else
        <div class="space-y-8">
            @foreach($resultsBySession as $item)
                @php
                    $session = $item['session'];
                    $result = $item['result'];
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 pt-6 pb-2">
                        <div class="text-center">
                            <div class="text-3xl font-extrabold text-gray-900 leading-tight">{{ config('app.name') }}</div>
                            <div class="mt-2 text-base sm:text-lg font-bold text-gray-800">{{ $session->name }}</div>
                            <div class="text-sm text-gray-600 mt-1">{{ $session->academic_year }} · {{ $session->session_type }}</div>
                            <div class="text-sm text-gray-600 mt-1">Semester Mark Sheet</div>
                        </div>
                    </div>

                    <div class="p-6 pt-2 space-y-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse border border-gray-200">
                                <tbody>
                                    <tr>
                                        <td class="w-1/3 bg-gray-50 border border-gray-200 px-4 py-2 font-semibold text-gray-700">Student</td>
                                        <td class="border border-gray-200 px-4 py-2 text-gray-900">{{ $student->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-50 border border-gray-200 px-4 py-2 font-semibold text-gray-700">Course</td>
                                        <td class="border border-gray-200 px-4 py-2 text-gray-900">{{ $student->course->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-50 border border-gray-200 px-4 py-2 font-semibold text-gray-700">Roll No</td>
                                        <td class="border border-gray-200 px-4 py-2 text-gray-900">{{ $student->roll_number ?: '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-50 border border-gray-200 px-4 py-2 font-semibold text-gray-700">Father name</td>
                                        <td class="border border-gray-200 px-4 py-2 text-gray-900">{{ $student->father_name ?: '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-50 border border-gray-200 px-4 py-2 font-semibold text-gray-700">Mother name</td>
                                        <td class="border border-gray-200 px-4 py-2 text-gray-900">{{ $student->mother_name ?: '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse border border-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th scope="col" class="border border-gray-200 px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('public.subject') }}</th>
                                        <th scope="col" class="border border-gray-200 px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('public.marks') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['subject_marks_detail'] ?? [] as $sm)
                                        <tr class="bg-white hover:bg-gray-50">
                                            <td class="border border-gray-200 px-4 py-3 text-gray-900 font-medium">{{ $sm->subject->name ?? $sm->subject_id }}</td>
                                            <td class="border border-gray-200 px-4 py-3 text-right text-gray-900">
                                                {{ $sm->is_absent ? __('public.absent') : ($sm->marks_obtained ?? '—') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase">{{ __('public.total') }}</div>
                                <div class="mt-1 text-lg font-bold text-gray-900">
                                    {{ number_format($result['marks_obtained'], 2) }} / {{ number_format($result['total_marks'], 2) }}
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase">{{ __('public.percentage') }}</div>
                                <div class="mt-1 text-lg font-bold text-gray-900">{{ number_format($result['percentage'], 2) }}%</div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase">{{ __('public.status') }}</div>
                                <div class="mt-1 text-lg font-bold text-gray-900">
                                    @if($result['is_passed'])
                                        {{ __('public.passed') }}
                                    @else
                                        {{ __('public.failed') }}
                                    @endif
                                </div>
                                <div class="mt-2 text-sm font-semibold text-gray-700">
                                    Grade: {{ $result['grade'] }}
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs text-gray-600 leading-relaxed mb-0">
                                This is a system-generated result for reference only and should not be treated as the official marksheet.
                                If you find any discrepancy in these results, please contact {{ config('app.name') }} for verification.
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('results.index') }}" class="inline-block mt-8 text-amber-600 hover:text-amber-700 font-medium">{{ __('public.search_again') }}</a>
    @endif
</div>
@endsection
