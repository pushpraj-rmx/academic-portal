@extends('layouts.public')

@section('title', 'Grading System - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8">Grading System</h1>

        @if(settings('grading_system_intro'))
            <div class="prose prose-sm sm:prose max-w-none text-gray-700 mb-8">
                {!! settings('grading_system_intro') !!}
            </div>
        @endif

        @if($gradingRules->isEmpty())
            @if(settings('grading_system_empty_content'))
                <div class="prose prose-sm sm:prose max-w-none text-gray-700">
                    {!! settings('grading_system_empty_content') !!}
                </div>
            @else
                <p class="text-gray-600">{{ settings('empty_grading_rules', 'No grading rules available right now.') }}</p>
            @endif
        @else
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium text-gray-700">Grade</th>
                            <th class="px-2 sm:px-4 py-2 text-right text-xs sm:text-sm font-medium text-gray-700">Min %</th>
                            <th class="px-2 sm:px-4 py-2 text-right text-xs sm:text-sm font-medium text-gray-700">Max %</th>
                            <th class="px-2 sm:px-4 py-2 text-right text-xs sm:text-sm font-medium text-gray-700">GPA</th>
                            <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium text-gray-700">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($gradingRules as $rule)
                            <tr>
                                <td class="px-2 sm:px-4 py-2 text-sm text-gray-900 font-semibold">{{ $rule->grade }}</td>
                                <td class="px-2 sm:px-4 py-2 text-right text-sm text-gray-900">{{ number_format((float) $rule->min_percentage, 2) }}</td>
                                <td class="px-2 sm:px-4 py-2 text-right text-sm text-gray-900">{{ number_format((float) $rule->max_percentage, 2) }}</td>
                                <td class="px-2 sm:px-4 py-2 text-right text-sm text-gray-900">{{ $rule->gpa !== null ? number_format((float) $rule->gpa, 2) : '—' }}</td>
                                <td class="px-2 sm:px-4 py-2 text-sm text-gray-700 prose prose-sm max-w-none">{!! $rule->description ?? '—' !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
