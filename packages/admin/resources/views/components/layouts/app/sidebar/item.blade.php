@props([
    'active' => false,
    'badge',
    'badgeColor',
    'icon',
    'shouldOpenUrlInNewTab',
    'url',
])

<li @class(['filament-sidebar-item', 'filament-sidebar-item-active' => $active])>
    <a
        href="{{ $url }}"
        {!! $shouldOpenUrlInNewTab ? 'target="_blank"' : '' !!}
        x-on:click="window.matchMedia(`(max-width: 1024px)`).matches && $store.sidebar.close()"
        @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
            x-data="{ tooltip: {} }"
            x-init="
                Alpine.effect(() => {
                    if (Alpine.store('sidebar').isOpen) {
                        tooltip = false
                    } else {
                        tooltip = {
                            content: {{ \Illuminate\Support\Js::from($slot->toHtml()) }},
                            theme: Alpine.store('theme') === 'light' ? 'dark' : 'light',
                            placement: document.dir === 'rtl' ? 'left' : 'right',
                        }
                    }
                })
            "
            x-tooltip.html="tooltip"
        @endif
        @class([
            'flex items-center justify-center gap-3 px-3 py-2 rounded-lg font-medium transition',
            'hover:bg-gray-500/5 focus:bg-gray-500/5' => ! $active,
            'dark:text-gray-300 dark:hover:bg-gray-700' => (! $active) && config('filament.dark_mode'),
            'bg-primary-500 text-white' => $active,
        ])
    >
        <x-dynamic-component :component="$icon" class="h-5 w-5 shrink-0" />

        <div class="flex flex-1"
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                x-data="{}"
                x-show="$store.sidebar.isOpen"
            @endif
        >
            <span>
                {{ $slot }}
            </span>

            @if (filled($badge))
                @php
                    $badgeColors = \Illuminate\Support\Arr::toCssClasses(
                        match ($badgeColor ?? null) {
                            'danger' => [
                                'text-danger-700 bg-danger-500/10' => ! $active,
                                'text-white bg-white/20' => $active,
                                'dark:text-danger-500' => (! $active) && config('tables.dark_mode'),
                            ],
                            'success' => [
                                'text-success-700 bg-success-500/10' => ! $active,
                                'text-white bg-white/20' => $active,
                                'dark:text-success-500' => (! $active) && config('tables.dark_mode'),
                            ],
                            'warning' => [
                                'text-warning-700 bg-warning-500/10' => ! $active,
                                'text-white bg-white/20' => $active,
                                'dark:text-warning-500' => (! $active) && config('filament.dark_mode'),
                            ],
                            default => [
                                'text-primary-700 bg-primary-500/20' => ! $active,
                                'text-white bg-white/20' => $active,
                                'dark:text-primary-500' => (! $active) && config('filament.dark_mode'),
                            ]
                        }
                    );
                @endphp
          
                <span @class([
                    'inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal',
                    $badgeColors => $badgeColors,
                ])> {{ $badge }} </span>
            @endif
        </div>
    </a>
</li>
