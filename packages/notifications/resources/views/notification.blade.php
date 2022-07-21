<x-notifications::notification
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-notifications-notification pointer-events-auto mb-4 flex w-full max-w-md gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-lg transition duration-300',
        'dark:border-gray-700 dark:bg-gray-800' => config('notifications.dark_mode'),
    ])"
    x-transition:enter-start="translate-x-12 opacity-0"
    x-transition:leave-end="scale-95 opacity-0"
>
    @if ($getIcon())
        <x-dynamic-component
            :component="$getIcon()"
            :class="\Illuminate\Support\Arr::toCssClasses([
                'filament-notifications-notification-icon h-6 w-6',
                match ($getIconColor()) {
                    'success' => 'text-success-400',
                    'warning' => 'text-warning-400',
                    'danger' => 'text-danger-400',
                    'primary' => 'text-primary-400',
                    'secondary' => 'text-gray-400',
                },
            ])"
        />
    @endif

    <div class="grid flex-1">
        <div
            @class([
                'filament-notifications-notification-title flex h-6 items-center text-sm font-medium text-gray-900',
                'dark:text-gray-200' => config('notifications.dark_mode'),
            ])
        >
            {!! \Illuminate\Support\Str::of($getTitle())->markdown()->sanitizeHtml() !!}
        </div>

        @if ($getBody())
            <div
                @class([
                    'filament-notifications-notification-body mt-1 text-sm text-gray-500',
                    'dark:text-gray-400' => config('notifications.dark_mode'),
                ])
            >
                {!! \Illuminate\Support\Str::of($getBody())->markdown()->sanitizeHtml() !!}
            </div>
        @endif

        @if ($getActions())
            <x-notifications::actions :actions="$getActions()" />
        @endif
    </div>

    <x-heroicon-s-x
        class="filament-notifications-notification-close-button h-6 w-5 cursor-pointer text-gray-400"
        x-on:click="close"
    />
</x-notifications::notification>
