<x-filament-panels::page>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}

        @if($summary)
            <div class="rounded-md border border-gray-200 bg-white p-4 space-y-2">
                <h2 class="font-semibold text-gray-900">Import summary</h2>
                <p class="text-sm text-gray-700">
                    Imported: {{ $summary['imported'] ?? 0 }},
                    Skipped: {{ $summary['skipped'] ?? 0 }}
                </p>
                @if(!empty($summary['errors']))
                    <ul class="mt-2 list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach($summary['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-3">
            <x-filament::button tag="a" href="{{ route('admin.import-subject-marks.sample') }}" color="secondary" type="button">
                Download sample CSV
            </x-filament::button>

            <x-filament::button type="submit">
                {{ $dry_run ? 'Validate only (dry run)' : 'Import marks' }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

