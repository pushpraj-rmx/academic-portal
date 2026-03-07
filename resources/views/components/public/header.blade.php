@php
    $headerNavItems = \App\Models\NavItem::forHeader()->get()->filter(fn ($item) => $item->shouldShow());
    $headerCourseCategories = \App\Models\CourseCategory::query()
        ->active()
        ->orderBy('sort_order')
        ->orderBy('name')
        ->limit(8)
        ->get();
    $topbarWelcome = settings('topbar_welcome') ?: 'Welcome to ' . config('app.name');
    $callUs = settings('topbar_phone', settings('footer_phone'));
    $mailUs = settings('topbar_email');
    $location = settings('topbar_location', settings('footer_address'));
    $socialLinks = [
        'facebook' => settings('facebook_url'),
        'twitter' => settings('twitter_url'),
        'google_plus' => settings('google_plus_url'),
        'linkedin' => settings('linkedin_url'),
    ];
    $payuUrl = settings('payu_url');
@endphp

<div x-data="{ mobileOpen: false }">
<header class="bg-white">
    {{-- Tier 1: Black top bar – welcome left, social + PayU right --}}
    <div class="bg-black text-white text-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                </svg>
                <span>{{ $topbarWelcome }}</span>
            </div>
            <div class="flex items-center gap-4">
                @if($socialLinks['facebook'])
                    <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                @endif
                @if($socialLinks['twitter'])
                    <a href="{{ $socialLinks['twitter'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="Twitter">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                @endif
                @if($socialLinks['google_plus'])
                    <a href="{{ $socialLinks['google_plus'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="Google+">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                    </a>
                @endif
                @if($socialLinks['linkedin'])
                    <a href="{{ $socialLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="LinkedIn">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                @endif
                @if($payuUrl)
                    <a href="{{ $payuUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 font-medium text-red-500 hover:text-red-400">
                        <span class="font-bold">PayU</span><span class="text-white">now</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Tier 2: White middle – logo + name left, Call Us / Mail Us / Location right --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-0 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                @if(settings('logo_path'))
                    <img src="{{ Storage::disk('public')->url(settings('logo_path')) }}" alt="{{ config('app.name') }} logo" class="
                    logo w-auto object-contain">
                @endif
                <!-- <span class="text-lg font-bold text-gray-900 leading-tight">{{ config('app.name') }}</span> -->
            </a>
            <div class="flex flex-wrap items-start gap-6 lg:gap-8">
                @if($callUs)
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <p class="font-bold text-gray-900">Call Us</p>
                            <p class="text-sm text-gray-600">{{ $callUs }}</p>
                        </div>
                    </div>
                @endif
                @if($mailUs)
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="font-bold text-gray-900">Mail Us</p>
                            <p class="text-sm text-gray-600">{{ $mailUs }}</p>
                        </div>
                    </div>
                @endif
                @if($location)
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p class="font-bold text-gray-900">Location</p>
                            <p class="text-sm text-gray-600">{{ $location }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>

{{-- Nav bar – sticky on scroll (sibling of header so sticky works) --}}
<div class="sticky top-0 z-50 bg-gray-800 shadow-md">
    <nav class="bg-gray-800" aria-label="Main navigation">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-12">
                <div class="hidden md:flex items-center gap-1 flex-wrap">
                    @foreach($headerNavItems as $item)
                        @if($item->isDropdown())
                            <div class="relative group">
                                <button type="button" class="px-3 py-2 text-sm font-medium uppercase text-white hover:text-red-400">
                                    {{ $item->label }}
                                </button>
                                <div class="absolute left-0 top-full z-20 hidden group-hover:block bg-white border border-gray-200 rounded-md shadow-lg min-w-56 py-2">
                                    @if($item->dynamic_source === 'course_categories')
                                        @foreach($headerCourseCategories as $category)
                                            <a href="{{ route('academic.category.show', $category) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ $category->name }}</a>
                                        @endforeach
                                        <a href="{{ route('academic.index') }}" class="block px-4 py-2 text-sm text-amber-700 hover:bg-gray-50">View all courses</a>
                                    @else
                                        @foreach($item->children as $child)
                                            <a href="{{ $child->href }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ $child->label }}</a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @else
                            @php
                                $isAuthLink = $item->route_name === 'login' || $item->url === '/admin';
                            @endphp
                            <a href="{{ $item->href ?? '#' }}" class="px-3 py-2 text-sm font-medium uppercase {{ $item->isActive() ? 'text-red-500' : ($isAuthLink ? 'text-amber-400 hover:text-amber-300' : 'text-white hover:text-red-400') }}">
                                {{ $item->label }}
                            </a>
                        @endif
                    @endforeach
                </div>
                <button type="button" class="md:hidden p-2 text-white" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Mobile menu --}}
    <div class="md:hidden border-t border-gray-700" x-show="mobileOpen" x-transition>
        <div class="px-4 py-3 space-y-1">
            @foreach($headerNavItems as $item)
                @if($item->isDropdown())
                    <span class="block py-2 font-medium text-gray-300 uppercase text-sm">{{ $item->label }}</span>
                    @if($item->dynamic_source === 'course_categories')
                        @foreach($headerCourseCategories as $category)
                            <a href="{{ route('academic.category.show', $category) }}" class="block py-1.5 pl-4 text-sm text-gray-300 hover:text-white">{{ $category->name }}</a>
                        @endforeach
                        <a href="{{ route('academic.index') }}" class="block py-1.5 pl-4 text-sm text-amber-400">View all courses</a>
                    @else
                        @foreach($item->children as $child)
                            <a href="{{ $child->href }}" class="block py-1.5 pl-4 text-sm text-gray-300 hover:text-white">{{ $child->label }}</a>
                        @endforeach
                    @endif
                @else
                    <a href="{{ $item->href ?? '#' }}" class="block py-2 text-sm {{ $item->isActive() ? 'text-red-500 font-medium' : 'text-gray-300 hover:text-white' }}">{{ $item->label }}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
</div>
