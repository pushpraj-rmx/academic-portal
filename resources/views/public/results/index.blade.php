@extends('layouts.public')
@section('title', 'Results - ' . config('app.name'))
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">{{ settings('section_check_results', 'Check Results') }}</h1>
    <p class="text-gray-600 mb-8">{{ settings('results_intro', 'Enter your roll number or enrollment ID to view your exam results.') }}</p>
    <form action="{{ route('results.search') }}" method="get" class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
        <div class="space-y-4">
            <div>
                <label for="query" class="block text-sm font-medium text-gray-700">{{ __('public.roll_number_or_enrollment_id') }}</label>
                <input type="text" name="query" id="query" value="{{ old('query') }}"
                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm py-2 px-3 focus:border-amber-500 focus:ring-amber-500 sm:text-sm"
                    required maxlength="255" placeholder="e.g. 2024CS001 or ENR-12345">
                @error('query')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('public.search') }}
            </button>
        </div>
    </form>
</div>
@endsection
