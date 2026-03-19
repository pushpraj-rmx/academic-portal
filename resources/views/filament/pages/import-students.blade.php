<x-filament-panels::page>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}

        @if($summary)
            <div class="rounded-md border border-gray-200 bg-white p-4 space-y-2">
                <h2 class="font-semibold text-gray-900">Validation summary</h2>
                <p class="text-sm text-gray-700">
                    Valid rows: {{ $summary['valid'] ?? 0 }},
                    Invalid rows: {{ $summary['invalid'] ?? 0 }}
                </p>
            </div>
        @endif

        @if($previewRows)
            <div class="rounded-md border border-gray-200 bg-white p-4 space-y-4">
                <h2 class="font-semibold text-gray-900">Preview</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">Name</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">Email</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">Course</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">Roll</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">DOB</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-700">Errors</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($previewRows as $i => $row)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900">{{ $row['name'] ?? '' }}</td>
                                    <td class="px-3 py-2 text-gray-900">{{ $row['email'] ?? '' }}</td>
                                    <td class="px-3 py-2 text-gray-900">
                                        @php $cid = $row['course_id'] ?? null; @endphp
                                        {{ $cid ? \App\Models\Course::find($cid)?->name : '' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-900">{{ $row['roll_number'] ?? '' }}</td>
                                    <td class="px-3 py-2 text-gray-900">{{ $row['date_of_birth'] ?? '' }}</td>
                                    <td class="px-3 py-2 text-sm text-red-600">
                                        @php $errs = $previewErrors[$i] ?? []; @endphp
                                        @if($errs)
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach($errs as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-green-600">OK</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <x-filament::button tag="a" href="{{ route('admin.import-students.sample') }}" color="secondary" type="button">
                Download sample CSV
            </x-filament::button>

            <x-filament::button type="submit">
                Validate file
            </x-filament::button>

            @if($summary && ($summary['valid'] ?? 0) > 0)
                <x-filament::button type="button" color="success" wire:click="confirmImport">
                    Import {{ $summary['valid'] ?? 0 }} valid rows
                </x-filament::button>
            @endif
        </div>
    </form>
</x-filament-panels::page>

