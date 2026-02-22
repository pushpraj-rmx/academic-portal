@extends('layouts.public')
@section('title', 'Our Recruiters - ' . config('app.name'))
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ settings('section_recruiters', 'Our Recruiters') }}</h1>
    <p class="text-gray-600 mb-6"><a href="{{ route('placements.statistics') }}" class="text-amber-600 hover:text-amber-700 font-medium">{{ settings('placement_statistics_link', 'View placement statistics') }}</a></p>
    @if($recruiters->isEmpty())
        <p class="text-gray-600">{{ settings('empty_recruiters', 'No recruiters to display.') }}</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recruiters as $recruiter)
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm flex flex-col items-center text-center">
                    @if($recruiter->logo_path)
                        <img src="{{ Storage::disk('public')->url($recruiter->logo_path) }}" alt="{{ $recruiter->name }}" class="h-16 w-auto object-contain mb-4">
                    @else
                        <div class="h-16 w-16 rounded bg-gray-100 flex items-center justify-center mb-4 text-gray-400 font-semibold text-lg">{{ Str::substr($recruiter->name, 0, 2) }}</div>
                    @endif
                    <h2 class="text-lg font-semibold text-gray-900">{{ $recruiter->name }}</h2>
                    @if($recruiter->website)
                        <a href="{{ $recruiter->website }}" target="_blank" rel="noopener noreferrer" class="mt-2 text-amber-600 hover:text-amber-700 text-sm font-medium">{{ __('public.visit_website') }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
