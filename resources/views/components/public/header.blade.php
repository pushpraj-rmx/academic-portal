<header class="bg-white border-b border-gray-200 shadow-sm">
    <nav class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Main navigation">
        <div class="flex justify-between items-center h-16">
            <a href="{{ route('home') }}" class="text-xl font-semibold text-gray-800 hover:text-gray-600">
                {{ config('app.name') }}
            </a>
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 font-medium">Home</a>
                <a href="{{ route('pages.show', ['page' => 'about']) }}" class="text-gray-600 hover:text-gray-900 font-medium">About</a>
                <a href="{{ route('academic.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Courses</a>
                <a href="{{ route('notices.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Notices</a>
                <a href="{{ route('results.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Results</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Dashboard</a>
                    @if(auth()->user()->can('page.view'))
                        <a href="{{ url('/admin') }}" class="text-amber-600 hover:text-amber-700 font-medium">Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Login</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
