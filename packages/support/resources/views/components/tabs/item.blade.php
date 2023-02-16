@props([
    'active' => false,
    'alpineActive' => null,
    'badge' => null,
    'icon' => null,
    'tag' => 'button',
    'type' => 'button',
])

<{{ $tag }}
    @if ($tag === 'button')
        type="{{ $type }}"
    @endif
    @if ($alpineActive)
        x-bind:class="{
            'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400': ! ({{ $alpineActive }}),
            'text-primary-600 shadow bg-white dark:text-white dark:bg-primary-600': {{ $alpineActive }},
        }"
    @endif
    {{ $attributes
        ->merge([
            'aria-selected' => $active,
            'role' => 'tab',
        ])
        ->class([
            'filament-tabs-item flex whitespace-nowrap items-center gap-3 h-8 px-5 font-medium rounded-md whitespace-nowrap outline-none focus:ring-2 focus:ring-primary-600 focus:ring-inset',
            'hover:text-gray-800 focus:text-primary-600 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-400' => (! $active) && (! $alpineActive),
            'text-primary-600 shadow bg-white dark:text-white dark:bg-primary-600' => $active,
        ]) }}
>
    @if ($icon)
        <x-filament::icon
            :name="$icon"
            alias="support::tabs.item"
            size="h-5 w-5"
        />
    @endif

    <span>
        {{ $slot }}
    </span>

    @if ($badge)
        <span
            @if ($alpineActive)
                x-bind:class="{
                    'bg-gray-200 dark:bg-gray-600': ! ({{ $alpineActive }}),
                    'bg-white text-primary-600 font-medium': {{ $alpineActive }},
                }"
            @endif
            @class([
                'inline-flex items-center justify-center min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal',
                'bg-gray-200 dark:bg-gray-600' => (! $active) && (! $alpineActive),
                'bg-white text-primary-600 font-medium' => $active,
            ])
        >
            {{ $badge }}
        </span>
    @endif
</{{ $tag }}>
