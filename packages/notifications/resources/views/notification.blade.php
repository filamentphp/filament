<div
    @class([
        'pointer-events-auto mb-4 flex w-full gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-lg transition duration-300',
        'dark:border-gray-700 dark:bg-gray-800' => config('notifications.dark_mode'),
    ])
    x-data="notificationComponent({ notification: {{ Js::from($toLivewire()) }} })"
    x-show="show"
    x-transition:enter-start="translate-x-12 opacity-0"
    x-transition:leave-end="scale-95 opacity-0"
    x-bind:class="{ 'absolute': !show }"
    id="notification-{{ $getId() }}"
    wire:key="notification-{{ $getId() }}"
>
    @if ($getIcon())
        <x-dynamic-component
            :component="$getIcon()"
            :class="Arr::toCssClasses([
                'h-6 w-6',
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
        <h4
            @class([
                'h-6 flex items-center font-medium text-sm text-gray-900',
                'dark:text-gray-200' => config('notifications.dark_mode'),
            ])
        >
            {!! \Illuminate\Support\Str::of($getTitle())->inlineMarkdown()->sanitizeHtml() !!}
        </h4>

        @if ($getDescription())
            <p
                @class([
                    'mt-1 text-sm text-gray-500',
                    'dark:text-gray-400' => config('notifications.dark_mode'),
                ])
            >
                {!! \Illuminate\Support\Str::of($getDescription())->inlineMarkdown()->sanitizeHtml() !!}
            </p>
        @endif

        @if ($getActions())
            <div class="flex gap-3 mt-4">
                @foreach($getActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        @endunless
    </div>

    <x-heroicon-s-x
        class="h-6 w-5 text-gray-400 transition-all"
        x-on:click="close"
    />
</div>
