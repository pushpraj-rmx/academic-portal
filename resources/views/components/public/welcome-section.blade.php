<section class="bg-white py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
            {{ settings('welcome_section_title', $page->title ?? settings('section_welcome', 'Welcome')) }}
        </h2>
        <div class="mt-4 prose prose-lg max-w-none text-gray-700">
            @if(settings('welcome_section_body'))
                <p>{{ settings('welcome_section_body') }}</p>
            @elseif(isset($page))
                {!! $page->body !!}
            @endif
        </div>
    </div>
</section>
