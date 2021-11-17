@props([
    'footer' => null,
    'header' => null,
])

<div {{ $attributes->class(['p-2 space-y-2 bg-white shadow rounded-xl']) }}>
    @if ($header)
        <div class="px-4 py-2">
            {{ $header }}
        </div>
    @endif

    @if ($header && $slot->isNotEmpty())
        <x-filament::hr />
    @endif

    <div class="space-y-2">
        @if ($slot->isNotEmpty())
            <div class="px-4 py-2 space-y-4">
                {{ $slot }}
            </div>
        @endif
    </div>

    @if ($footer && $slot->isNotEmpty())
        <x-filament::hr />
    @endif

    @if ($footer)
        <div class="px-4 py-2">
            {{ $footer }}
        </div>
    @endif
</div>
