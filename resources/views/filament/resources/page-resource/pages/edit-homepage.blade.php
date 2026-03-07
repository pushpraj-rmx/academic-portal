<x-filament-panels::page>
    <x-filament::section class="mb-6">
        <x-slot name="description">
            Everything you edit here is shown on the public homepage. Section titles and welcome text appear in the same order as on the site.
        </x-slot>
    </x-filament::section>

    <form wire:submit="save">
        {{ $this->form }}
        <div class="mt-6">
            <x-filament::button type="submit">
                Save
            </x-filament::button>
        </div>
    </form>

    <x-filament::section class="mt-8">
        <x-slot name="heading">
            Other content on the homepage
        </x-slot>
        <x-slot name="description">
            These blocks appear on the public homepage but are managed in their own sections below.
        </x-slot>
        <div class="flex flex-wrap gap-2">
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\HeroSlideResource::getUrl('index') }}" color="gray" size="sm" outlined>
                Hero slides
            </x-filament::button>
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\AnnouncementResource::getUrl('index') }}" color="gray" size="sm" outlined>
                Announcements
            </x-filament::button>
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\StatCounterResource::getUrl('index') }}" color="gray" size="sm" outlined>
                Stat counters
            </x-filament::button>
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\CourseCategoryResource::getUrl('index') }}" color="gray" size="sm" outlined>
                Course categories
            </x-filament::button>
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\CertificationResource::getUrl('index') }}" color="gray" size="sm" outlined>
                Certifications
            </x-filament::button>
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\TestimonialResource::getUrl('index') }}" color="gray" size="sm" outlined>
                Testimonials
            </x-filament::button>
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\RecruiterResource::getUrl('index') }}" color="gray" size="sm" outlined>
                Recruiters
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-panels::page>
