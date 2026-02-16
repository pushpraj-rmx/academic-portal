@extends('layouts.public')
@section('title', 'Placement Statistics - ' . config('app.name'))
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Placement Statistics</h1>

    @if($highestPackage !== null)
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Highest Package (CTC / Stipend)</h2>
            <p class="text-2xl font-bold text-amber-600">₹ {{ number_format($highestPackage, 2) }} LPA</p>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Placements by Academic Year</h2>
        @if($byYear->isEmpty())
            <p class="text-gray-600">No placement data by year.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Academic Year</th>
                            <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">Total Placements</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($byYear as $row)
                            <tr>
                                <td class="px-4 py-2 text-gray-900">{{ $row->academic_year }}</td>
                                <td class="px-4 py-2 text-right text-gray-900">{{ $row->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Placements by Course</h2>
        @if($byCourse->isEmpty())
            <p class="text-gray-600">No placement data by course.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Course</th>
                            <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">Total Placements</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($byCourse as $row)
                            <tr>
                                <td class="px-4 py-2 text-gray-900">{{ $row->course_name }}</td>
                                <td class="px-4 py-2 text-right text-gray-900">{{ $row->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
