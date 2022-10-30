<x-filament-notifications::notification
    :notification="$notification"
    @class([
        'flex gap-3 w-full transition duration-300',
        'max-w-sm rounded-xl bg-white p-4 shadow-lg ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10' => ! $isInline(),
    ])
    :x-transition:enter-start="\Illuminate\Support\Arr::toCssClasses([
        'opacity-0',
        match (static::$horizontalAlignment) {
            'left' => '-translate-x-12',
            'right' => 'translate-x-12',
            'center' => match (static::$verticalAlignment) {
                'top' => '-translate-y-12',
                'bottom' => 'translate-y-12',
            },
        },
    ])"
    x-transition:leave-end="scale-95 opacity-0"
>
    @if ($icon = $getIcon())
        <x-filament-notifications::icon :icon="$icon" :color="$getIconColor()" />
    @endif

    <div class="grid flex-1">
        @if ($title = $getTitle())
            <x-filament-notifications::title>
                {{ str($title)->markdown()->sanitizeHtml()->toHtmlString() }}
            </x-filament-notifications::title>
        @endif

        @if ($date = $getDate())
            <x-filament-notifications::date>
                {{ $date }}
            </x-filament-notifications::date>
        @endif

        @if ($body = $getBody())
            <x-filament-notifications::body>
                {{ str($body)->markdown()->sanitizeHtml()->toHtmlString() }}
            </x-filament-notifications::body>
        @endif

        @if ($actions = $getActions())
            <x-filament-notifications::actions :actions="$actions" />
        @endif
    </div>

    <x-filament-notifications::close-button />
</x-filament-notifications::notification>
