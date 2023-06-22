<x-notifications::notification
    :notification="$notification"
    :class="
        \Illuminate\Support\Arr::toCssClasses([
            'flex gap-3 w-full transition duration-300',
            'shadow-lg max-w-sm bg-white rounded-xl p-4 border border-gray-200' => ! $isInline(),
            'dark:border-gray-700 dark:bg-gray-800' => (! $isInline()) && config('notifications.dark_mode'),
        ])
    "
    :x-transition:enter-start="
        \Illuminate\Support\Arr::toCssClasses([
            'opacity-0',
            match (config('notifications.layout.alignment.horizontal')) {
                'left' => '-translate-x-12',
                'right' => 'translate-x-12',
                'center' => match (config('notifications.layout.alignment.vertical')) {
                    'top' => '-translate-y-12',
                    'bottom' => 'translate-y-12',
                    'center' => null,
                },
            },
        ])
    "
    x-transition:leave-end="scale-95 opacity-0"
>
    @if ($icon = $getIcon())
        <x-notifications::icon :icon="$icon" :color="$getIconColor()" />
    @endif

    <div class="grid flex-1">
        @if ($title = $getTitle())
            <x-notifications::title>
                {{ \Illuminate\Support\Str::of($title)->markdown()->sanitizeHtml()->toHtmlString() }}
            </x-notifications::title>
        @endif

        @if ($date = $getDate())
            <x-notifications::date>
                {{ $date }}
            </x-notifications::date>
        @endif

        @if ($body = $getBody())
            <x-notifications::body>
                {{ \Illuminate\Support\Str::of($body)->markdown()->sanitizeHtml()->toHtmlString() }}
            </x-notifications::body>
        @endif

        @if ($actions = $getActions())
            <x-notifications::actions :actions="$actions" />
        @endif
    </div>

    <x-notifications::close-button />
</x-notifications::notification>
