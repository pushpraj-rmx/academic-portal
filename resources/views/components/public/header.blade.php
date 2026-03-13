@php
    $headerNavItems = \App\Models\NavItem::forHeader()->get()
        ->filter(fn ($item) => $item->shouldShow())
        ->filter(fn ($item) => $item->url !== '/admin' && ! str_starts_with($item->route_name ?? '', 'filament.'));
    $headerCourseCategories = \App\Models\CourseCategory::query()
        ->active()
        ->orderBy('sort_order')
        ->orderBy('name')
        ->limit(8)
        ->get();
    $topbarWelcome = settings('topbar_welcome') ?: 'Welcome to ' . config('app.name');
    $callUs = settings('footer_phone', settings('topbar_phone'));
    $mailUs = settings('topbar_email');
    $location = settings('topbar_location', settings('footer_address'));
    $socialLinks = [
        'facebook' => settings('facebook_url'),
        'twitter' => settings('twitter_url'),
        'google_plus' => settings('google_plus_url'),
        'linkedin' => settings('linkedin_url'),
        'pinterest' => settings('pinterest_url'),
        'vimeo' => settings('vimeo_url'),
    ];
    $payuUrl = settings('payu_url');
@endphp

<div
    x-data="{ mobileOpen: false, openSection: null }"
    x-init="$watch('mobileOpen', v => { document.body.style.overflow = v ? 'hidden' : '' })"
    @keydown.escape.window="mobileOpen = false"
    class="w-full overflow-x-hidden lg:overflow-visible"
>
<header class="bg-white w-full">
    {{-- Tier 1: Black top bar – welcome left, social + PayU right --}}
    <div class="bg-black text-white text-sm min-h-[3rem] flex items-center">
        <div class="max-w-6xl mx-auto w-full px-3 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center justify-center sm:justify-between gap-x-4 gap-y-2">
            <div class="flex items-start justify-center sm:justify-start gap-2 min-w-0 w-full sm:w-auto">
                <svg class="w-4 h-4 shrink-0 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                </svg>
                <span class="line-clamp-2 text-xs sm:text-sm leading-snug text-center sm:text-left">{{ $topbarWelcome }}</span>
            </div>
            <div class="flex items-center gap-3 sm:gap-4 shrink-0">
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
                @if($socialLinks['pinterest'])
                    <a href="{{ $socialLinks['pinterest'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="Pinterest">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 21c-4.963 0-9-4.037-9-9s4.037-9 9-9 9 4.037 9 9-4.037 9-9 9z"/></svg>
                    </a>
                @endif
                @if($socialLinks['vimeo'])
                    <a href="{{ $socialLinks['vimeo'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white" aria-label="Vimeo">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.977 6.416c-.105 2.338-1.739 5.543-4.894 9.609-3.268 4.247-6.026 6.37-8.29 6.37-1.409 0-2.578-1.294-3.553-3.881L5.322 11.4C4.603 8.816 3.834 7.522 3.01 7.522c-.179 0-.806.378-1.881 1.132L0 7.197a315.065 315.065 0 003.501-3.128C5.08 2.701 6.266 1.984 7.055 1.91c1.867-.18 3.016 1.11 3.447 3.869.465 2.953.789 4.789.971 5.507.539 2.45 1.131 3.674 1.776 3.674.502 0 1.256-.796 2.265-2.385 1.004-1.589 1.54-2.797 1.612-3.628.144-1.371-.395-2.061-1.614-2.061-.574 0-1.167.121-1.777.391 1.186-3.868 3.434-5.757 6.762-5.637 2.473.06 3.628 1.664 3.493 4.797z"/></svg>
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
        <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center sm:items-center sm:justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 min-w-0">
                @if(settings('logo_path'))
                    <img src="{{ Storage::disk('public')->url(settings('logo_path')) }}" alt="{{ config('app.name') }} logo" class="
                    logo w-auto object-contain">
                @endif
                <!-- <span class="text-lg font-bold text-gray-900 leading-tight">{{ config('app.name') }}</span> -->
            </a>
            <div class="hidden sm:flex flex-wrap items-start gap-6 lg:gap-8">
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
<div class="sticky top-0 z-50 bg-gray-800 shadow-md overflow-hidden lg:overflow-visible">
    <nav class="bg-gray-800" aria-label="Main navigation">
        <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-12">
                <div class="hidden lg:flex items-center gap-1 flex-wrap">
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
                {{-- Mobile only: hamburger / close (X) – hidden on lg to avoid affecting desktop nav --}}
                <div class="lg:hidden shrink-0">
                    <button
                        type="button"
                        class="relative min-h-[44px] min-w-[44px] p-3 -m-1 flex items-center justify-center text-white touch-manipulation rounded"
                        @click="mobileOpen = !mobileOpen"
                        :aria-label="mobileOpen ? 'Close menu' : 'Open menu'"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="w-6 h-6 absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</div>

{{-- Mobile drawer: backdrop + slide-in panel (same Alpine scope as root) --}}
{{-- Backdrop: semi-transparent, closes on click --}}
<div
    class="fixed inset-0 z-40 bg-black/50 lg:hidden"
    x-show="mobileOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="mobileOpen = false"
    aria-hidden="true"
></div>
{{-- Drawer panel: slides in from left --}}
<aside
    class="fixed left-0 top-0 bottom-0 z-50 w-72 max-w-[85vw] bg-gray-800 shadow-xl lg:hidden flex flex-col"
    x-show="mobileOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    role="dialog"
    aria-modal="true"
    aria-label="Main menu"
>
            <div class="flex-1 overflow-y-auto py-4">
                <div class="space-y-0">
                    @foreach($headerNavItems as $item)
                        @if($item->isDropdown())
                            <div class="border-b border-gray-700">
                                <button
                                    type="button"
                                    class="w-full min-h-[44px] flex items-center justify-between px-4 py-3 text-left text-sm font-medium uppercase text-gray-200 hover:bg-gray-700 hover:text-white transition-colors"
                                    @click="openSection = openSection === {{ $loop->index }} ? null : {{ $loop->index }}"
                                    :aria-expanded="openSection === {{ $loop->index }}"
                                >
                                    <span>{{ $item->label }}</span>
                                    <svg class="w-5 h-5 shrink-0 transition-transform duration-200" :class="openSection === {{ $loop->index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div
                                    x-show="openSection === {{ $loop->index }}"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="bg-gray-900/50"
                                >
                                    <div class="pb-2">
                                        @if($item->dynamic_source === 'course_categories')
                                            @foreach($headerCourseCategories as $category)
                                                <a href="{{ route('academic.category.show', $category) }}" class="block min-h-[44px] px-4 py-3 pl-6 text-sm text-gray-300 hover:bg-gray-700 hover:text-white flex items-center" @click="mobileOpen = false">{{ $category->name }}</a>
                                            @endforeach
                                            <a href="{{ route('academic.index') }}" class="block min-h-[44px] px-4 py-3 pl-6 text-sm text-amber-400 hover:bg-gray-700 hover:text-amber-300 flex items-center" @click="mobileOpen = false">View all courses</a>
                                        @else
                                            @foreach($item->children as $child)
                                                <a href="{{ $child->href }}" class="block min-h-[44px] px-4 py-3 pl-6 text-sm text-gray-300 hover:bg-gray-700 hover:text-white flex items-center" @click="mobileOpen = false">{{ $child->label }}</a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                $isAuthLink = $item->route_name === 'login' || $item->url === '/admin';
                            @endphp
                            <a href="{{ $item->href ?? '#' }}" class="block min-h-[44px] px-4 py-3 text-sm font-medium uppercase border-b border-gray-700 {{ $item->isActive() ? 'text-red-500 bg-gray-900/30' : ($isAuthLink ? 'text-amber-400 hover:bg-gray-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white') }} flex items-center transition-colors" @click="mobileOpen = false">{{ $item->label }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        </aside>
</div>
