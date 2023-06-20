@props([
    'active' => false,
    'activeIcon',
    'badge' => null,
    'badgeColor' => null,
    'icon',
    'iconColor' => null,
    'shouldOpenUrlInNewTab' => false,
    'url',
])

<li
    @class([
        'filament-sidebar-item overflow-hidden',
        'filament-sidebar-item-active' => $active,
    ])
>
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
            'flex items-center justify-center gap-3 rounded-lg px-3 py-2 font-medium transition',
            'hover:bg-gray-500/5 focus:bg-gray-500/5' => ! $active,
            'dark:text-gray-300 dark:hover:bg-gray-700' => (! $active) && config('filament.dark_mode'),
            'bg-primary-500 text-white' => $active,
        ])
    >
        <x-dynamic-component
            :component="($active && $activeIcon) ? $activeIcon : $icon"
            :class="
                \Illuminate\Support\Arr::toCssClasses([
                    'h-5 w-5 shrink-0',
                    'text-primary-500' => (! $active) && ($iconColor === 'primary'),
                    'text-danger-500' => (! $active) && ($iconColor === 'danger'),
                    'text-gray-500' => (! $active) && ($iconColor === 'secondary'),
                    'text-success-500' => (! $active) && ($iconColor === 'success'),
                    'text-warning-500' => (! $active) && ($iconColor === 'warning'),
                ])
            "
        />

        <div
            class="flex flex-1"
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                x-show="$store.sidebar.isOpen"
            @endif
        >
            <span>
                {{ $slot }}
            </span>
        </div>

        @if (filled($badge))
            <x-filament::layouts.app.sidebar.badge
                :badge="$badge"
                :badge-color="$badgeColor"
                :active="$active"
            />
        @endif
    </a>
</li>
