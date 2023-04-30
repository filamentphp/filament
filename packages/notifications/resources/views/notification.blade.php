@php
    use Filament\Notifications\Http\Livewire\Notifications;

    $color = $getColor();
    $iconColor = $getIconColor();
    $isInline = $isInline();
@endphp
<x-filament-notifications::notification
    :notification="$notification"
    @class([
        'w-full transition duration-300',
        'max-w-sm rounded-xl bg-white shadow-lg ring-1 ring-gray-900/10 dark:ring-gray-50/10' => ! $isInline,
        'dark:bg-gray-800' => ! $color && ! $isInline,
        'dark:bg-gray-700' => $color,
    ])
    :x-transition:enter-start="\Illuminate\Support\Arr::toCssClasses([
        'opacity-0',
        ($this instanceof Notifications) ? match (static::$horizontalAlignment) {
            'left' => '-translate-x-12',
            'right' => 'translate-x-12',
            'center' => match (static::$verticalAlignment) {
                'top' => '-translate-y-12',
                'bottom' => 'translate-y-12',
                'center' => null,
            },
        } : null,
    ])"
    x-transition:leave-end="scale-95 opacity-0"
>
    <div
        @class([
            'flex gap-3 w-full',
            'py-2 pl-6 pr-2' => $isInline,
            'p-4 rounded-xl border' => ! $isInline,
            match ($color) {
                'primary' => 'border-primary-500/50 bg-primary-500/10 dark:bg-primary-500/20',
                'secondary' => 'border-secondary-500/40 bg-secondary-500/10 dark:bg-secondary-500/20',
                'danger' => 'border-danger-500/40 bg-danger-500/10 dark:bg-danger-500/20',
                'success' => 'border-success-500/40 bg-success-500/10 dark:bg-success-500/20',
                'warning' => 'border-warning-500/40 bg-warning-500/10 dark:bg-warning-500/20',
                null => 'border-transparent',
                default => $color,
            },
        ])
    >
        @if ($icon = $getIcon())
            <x-filament-notifications::icon :icon="$icon" :color="$iconColor" />
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
