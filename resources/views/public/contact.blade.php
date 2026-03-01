@extends('layouts.public')

@section('title', settings('contact_page_title', 'Contact') . ' - ' . config('app.name'))

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ settings('contact_page_title', 'Contact') }}</h1>
        <p class="text-gray-600 mb-8">{{ settings('contact_page_subtitle', 'Get in touch with us for any query.') }}</p>

        @if(session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('contact.store') }}" method="post" class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" maxlength="50"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required maxlength="255"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                @error('subject')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea id="message" name="message" rows="6" required maxlength="2000"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-amber-500 focus:ring-amber-500">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded-md hover:bg-amber-700">
                Send Message
            </button>
        </form>
    </div>
@endsection
