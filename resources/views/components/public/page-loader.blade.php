@php
    $logoUrl = settings('logo_path') ? Storage::disk('public')->url(settings('logo_path')) : null;
@endphp
<div
    x-data="pageLoader()"
    x-show="visible"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-gray-50"
>
    <div class="flex flex-col items-center gap-6">
        <div class="relative flex h-28 w-28 items-center justify-center">
            <div class="absolute inset-0 rounded-full border-2 border-gray-200 border-t-red-500 animate-spin" aria-hidden="true"></div>
            @if($logoUrl)
                <img
                    src="{{ $logoUrl }}"
                    alt="{{ config('app.name') }}"
                    class="relative z-10 h-20 w-auto max-w-[6rem] object-contain animate-pulse"
                />
            @else
                <span class="relative z-10 text-xl font-bold text-gray-800">{{ config('app.name') }}</span>
            @endif
        </div>
        <div class="flex items-center gap-1.5" aria-hidden="true">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-bounce" style="animation-delay: 0ms;"></span>
            <span class="w-2 h-2 rounded-full bg-red-500 animate-bounce" style="animation-delay: 150ms;"></span>
            <span class="w-2 h-2 rounded-full bg-red-500 animate-bounce" style="animation-delay: 300ms;"></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pageLoader', () => ({
        visible: true,
        init() {
            const hide = () => this.hide();
            if (document.readyState === 'complete') {
                hide();
                return;
            }
            window.addEventListener('load', hide);
            setTimeout(hide, 4000);
        },
        hide() {
            this.visible = false;
        },
    }));
});
</script>
