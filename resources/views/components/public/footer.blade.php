@php
    $footerAddress = settings('footer_address');
    $footerPhone = settings('footer_phone', settings('topbar_phone'));
    $footerEmails = settings_array('footer_emails');
    $hasContact = $footerAddress || $footerPhone || count($footerEmails) > 0;
    $socialUrls = [
        'facebook' => settings('facebook_url'),
        'twitter' => settings('twitter_url'),
        'google_plus' => settings('google_plus_url'),
        'linkedin' => settings('linkedin_url'),
        'pinterest' => settings('pinterest_url'),
        'vimeo' => settings('vimeo_url'),
    ];
    $hasSocial = collect($socialUrls)->filter()->isNotEmpty();
@endphp
<footer class="bg-gray-800 text-gray-200 mt-auto" aria-label="Site footer">
    @if($hasContact)
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-6">
                <div class="flex flex-col items-center md:items-start text-center md:text-left">
                    <div class="flex items-center gap-2 text-red-500 mb-2" aria-hidden="true">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-semibold text-white">Address</span>
                    </div>
                    @if($footerAddress)
                        <p class="text-sm text-gray-300 whitespace-pre-line">{{ $footerAddress }}</p>
                    @else
                        <p class="text-sm text-gray-500">—</p>
                    @endif
                </div>
                <div class="flex flex-col items-center md:items-start text-center md:text-left md:border-l md:border-gray-600 md:pl-6">
                    <div class="flex items-center gap-2 text-red-500 mb-2" aria-hidden="true">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>
                        <span class="font-semibold text-white">Phone Number</span>
                    </div>
                    @if($footerPhone)
                        <a href="tel:{{ preg_replace('/\s+/', '', $footerPhone) }}" class="text-sm text-gray-300 hover:text-red-400 transition-colors">{{ $footerPhone }}</a>
                    @else
                        <p class="text-sm text-gray-500">—</p>
                    @endif
                </div>
                <div class="flex flex-col items-center md:items-start text-center md:text-left md:border-l md:border-gray-600 md:pl-6">
                    <div class="flex items-center gap-2 text-red-500 mb-2" aria-hidden="true">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        <span class="font-semibold text-white">Email Address</span>
                    </div>
                    @if(count($footerEmails) > 0)
                        <ul class="text-sm space-y-1">
                            @foreach($footerEmails as $email)
                                @if(is_string($email))
                                    <li><a href="mailto:{{ $email }}" class="text-red-400 hover:text-red-300 transition-colors">{{ $email }}</a></li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">—</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
    @if($hasSocial)
        <div class="border-t border-gray-700">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-center gap-4">
                    @if($socialUrls['facebook'])
                        <a href="{{ $socialUrls['facebook'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif
                    @if($socialUrls['twitter'])
                        <a href="{{ $socialUrls['twitter'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors" aria-label="Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    @endif
                    @if($socialUrls['google_plus'])
                        <a href="{{ $socialUrls['google_plus'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors" aria-label="Google Plus">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 0C5.385 0 0 5.385 0 12s5.385 12 12 12 12-5.385 12-12S18.615 0 12 0zm-1 17v-4H7v-2h4V7h2v4h4v2h-4v4h-2z"/></svg>
                        </a>
                    @endif
                    @if($socialUrls['linkedin'])
                        <a href="{{ $socialUrls['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors" aria-label="LinkedIn">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    @endif
                    @if($socialUrls['pinterest'])
                        <a href="{{ $socialUrls['pinterest'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors" aria-label="Pinterest">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 21c-4.963 0-9-4.037-9-9s4.037-9 9-9 9 4.037 9 9-4.037 9-9 9zm-1-14.5c-2.206 0-4 1.794-4 4 0 1.575.92 2.987 2.354 3.643-.033-.313-.068-.83.014-1.184.075-.324.49-2.06.49-2.06s-.125-.25-.125-.618c0-.578.335-1.01.752-1.01.355 0 .526.266.526.584 0 .357-.227 1.893-.344 2.942-.104.438.22.712.65.712.78 0 1.38-.822 1.38-2.006 0-1.048-.752-1.78-1.638-1.78-1.116 0-1.77 1.037-1.77 2.107 0 .357.137.74.308.953l.118.14c.012.017.026.04.026.098 0 .083-.1.33-.1.33s-.065.265-.265.265H9.5c-.4 0-.532-.532-.4-.846.19-.398.652-1.327.652-2.14 0-1.74-1.268-2.94-3.402-2.94-2.315 0-3.848 1.7-3.848 3.548 0 1.847.815 3.098 1.9 3.098.186 0 .37-.074.505-.206.055-.066.102-.19.102-.368 0-.178-.096-.342-.26-.342h-.418zm1.1 5.5c-.55 0-1.02-.45-1.02-1s.47-1 1.02-1 1.02.45 1.02 1-.47 1-1.02 1z"/></svg>
                        </a>
                    @endif
                    @if($socialUrls['vimeo'])
                        <a href="{{ $socialUrls['vimeo'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white transition-colors" aria-label="Vimeo">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.977 6.416c-.105 2.338-1.739 5.543-4.894 9.609-3.268 4.247-6.026 6.37-8.29 6.37-1.409 0-2.578-1.294-3.553-3.881L5.322 11.4C4.603 8.816 3.834 7.522 3.01 7.522c-.179 0-.806.378-1.881 1.132L0 7.197a315.065 315.065 0 003.501-3.128C5.08 2.701 6.266 1.984 7.055 1.91c1.867-.18 3.016 1.11 3.447 3.869.465 2.953.789 4.789.971 5.507.539 2.45 1.131 3.674 1.776 3.674.502 0 1.256-.796 2.265-2.385 1.004-1.589 1.54-2.797 1.612-3.628.144-1.371-.395-2.061-1.614-2.061-.574 0-1.167.121-1.777.391 1.186-3.868 3.434-5.757 6.762-5.637 2.473.06 3.628 1.664 3.493 4.797z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <div class="border-t border-gray-700">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-400">{{ settings('footer_text', '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.') }}</p>
        </div>
    </div>
</footer>
<button type="button"
    x-data="{ show: false }"
    x-init="window.addEventListener('scroll', () => { show = window.scrollY > 300 })"
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
    class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex items-center justify-center w-12 h-12 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900 touch-manipulation"
    aria-label="Scroll to top"
>
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>
