@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'icon' => null,
    'shouldOpenUrlInNewTab' => false,
    'url' => null,
])

@php
    $tag = $url ? 'a' : 'button';
@endphp

<li
    @class([
        'fi-topbar-item overflow-hidden',
        'fi-topbar-item-active' => $active,
    ])
>
    <{{ $tag }}
        @if ($url)
            href="{{ $url }}"

            @if ($shouldOpenUrlInNewTab)
                target="_blank"
            @else
                wire:navigate
            @endif
        @else
            type="button"
        @endif
        @class([
            'flex items-center justify-center gap-x-2 rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-950/5 focus:bg-gray-950/5 dark:hover:bg-white/5 dark:focus:bg-white/5',
            'text-gray-700 dark:text-gray-300' => ! $active,
            'bg-gray-950/5 text-primary-600 dark:bg-white/5 dark:text-primary-400' => $active,
        ])
    >
        @if ($icon || $activeIcon)
            <x-filament::icon
                :name="($active && $activeIcon) ? $activeIcon : $icon"
                @class([
                    'fi-topbar-item-icon h-5 w-5',
                    'text-gray-400 dark:text-gray-500' => ! $active,
                    'text-primary-500' => $active,
                ])
            />
        @endif

        <span>
            {{ $slot }}
        </span>

        @if (filled($badge))
            <x-filament::badge :color="$badgeColor" size="sm">
                {{ $badge }}
            </x-filament::badge>
        @endif
    </{{ $tag }}>
</li>
