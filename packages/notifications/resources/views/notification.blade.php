@php
    $color = $getColor() ?? 'gray';
    $isInline = $isInline();
@endphp

<x-filament-notifications::notification
    :notification="$notification"
    :x-transition:enter-start="
        \Illuminate\Support\Arr::toCssClasses([
            'opacity-0',
            ($this instanceof \Filament\Notifications\Http\Livewire\Notifications)
            ? match (static::$horizontalAlignment) {
                'left' => '-translate-x-12',
                'right' => 'translate-x-12',
                'center' => match (static::$verticalAlignment) {
                    'top' => '-translate-y-12',
                    'bottom' => 'translate-y-12',
                    'center' => null,
                },
            }
            : null,
        ])
    "
    x-transition:leave-end="scale-95 opacity-0"
    @class([
        'w-full transition duration-300',
        ...match ($isInline) {
            true => [],
            false => [
                'max-w-sm rounded-xl bg-white shadow-lg ring-1 dark:bg-gray-800',
                match ($color) {
                    'gray' => 'ring-gray-950/5 dark:ring-white/20',
                    default => 'ring-custom-500/50',
                },
            ],
        },
    ])
    @style([
        \Filament\Support\get_color_css_variables($color, shades: [500]) => ! ($isInline || $color === 'gray'),
    ])
>
    <div
        @class([
            'flex w-full gap-3',
            ...match ($isInline) {
                true => ['py-2 pe-2 ps-6'],
                false => [
                    'rounded-xl p-4',
                    match ($color) {
                        'gray' => null,
                        default => 'bg-custom-500/10 dark:bg-custom-500/20',
                    },
                ],
            },
        ])
        @style([
            \Filament\Support\get_color_css_variables($color, shades: [500]) => ! ($isInline || $color === 'gray'),
        ])
    >
        @if ($icon = $getIcon())
            <x-filament-notifications::icon
                :name="$icon"
                :color="$getIconColor()"
                :size="$getIconSize()"
            />
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
    </div>
</x-filament-notifications::notification>
