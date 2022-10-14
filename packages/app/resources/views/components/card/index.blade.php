@props([
    'actions' => null,
    'footer' => null,
    'header' => null,
    'heading' => null,
])

<div {{ $attributes->class([
    'p-2 space-y-2 bg-white rounded-xl shadow',
    'dark:border-gray-600 dark:bg-gray-800' => config('filament.dark_mode'),
]) }}>
    @if ($actions || $header || $heading)
        <div class="px-4 py-2">
            @if ($header)
                {{ $header }}
            @elseif ($actions || $heading)
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <x-filament::card.heading>
                        {{ $heading }}
                    </x-filament::card.heading>

                    @if ($actions)
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            {{ $actions }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if (($actions || $header || $heading) && $slot->isNotEmpty())
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
