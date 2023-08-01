@php
    use Filament\Support\Enums\IconPosition;
@endphp

@props([
    'active' => false,
    'alpineActive' => null,
    'badge' => null,
    'icon' => null,
    'iconColor' => 'gray',
    'iconPosition' => IconPosition::Before,
    'tag' => 'button',
    'type' => 'button',
])

@php
    $hasAlpineActiveClasses = filled($alpineActive);

    $inactiveItemClasses = 'text-gray-500 hover:text-gray-700 focus:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 dark:focus:text-gray-200';

    $activeItemClasses = 'fi-tabs-item-active bg-gray-50 text-primary-600 dark:bg-white/5 dark:text-primary-400';

    $iconClasses = 'fi-tabs-item-icon h-5 w-5';

    $inactiveIconClasses = 'text-gray-400 dark:text-gray-500';

    $activeIconClasses = 'text-primary-600 dark:text-primary-400';
@endphp

<{{ $tag }}
    @if ($tag === 'button')
        type="{{ $type }}"
    @endif
    @if ($hasAlpineActiveClasses)
        x-bind:class="{
            @js($inactiveItemClasses): ! {{ $alpineActive }},
            @js($activeItemClasses): {{ $alpineActive }},
        }"
    @endif
    {{
        $attributes
            ->merge([
                'aria-selected' => $active,
                'role' => 'tab',
            ])
            ->class([
                'fi-tabs-item flex items-center gap-x-2 rounded-lg px-3 py-2 text-sm font-medium font-medium outline-none transition duration-75 hover:bg-gray-50 focus:bg-gray-50 dark:hover:bg-white/5 dark:focus:bg-white/5',
                $inactiveItemClasses => (! $hasAlpineActiveClasses) && (! $active),
                $activeItemClasses => (! $hasAlpineActiveClasses) && $active,
            ])
    }}
>
    @if ($icon && in_array($iconPosition, [IconPosition::Before, 'before']))
        <x-filament::icon
            :icon="$icon"
            :x-bind:class="$hasAlpineActiveClasses ? '{ ' . \Illuminate\Support\Js::from($inactiveIconClasses) . ': ! (' . $alpineActive . '), ' . \Illuminate\Support\Js::from($activeIconClasses) . ': ' . $alpineActive . ' }' : null"
            @class([
                $iconClasses,
                $inactiveIconClasses => (! $hasAlpineActiveClasses) && (! $active),
                $activeIconClasses => (! $hasAlpineActiveClasses) && $active,
            ])
        />
    @endif

    <span>
        {{ $slot }}
    </span>

    @if ($icon && in_array($iconPosition, [IconPosition::After, 'after']))
        <x-filament::icon
            :icon="$icon"
            :x-bind:class="$hasAlpineActiveClasses ? '{ ' . \Illuminate\Support\Js::from($inactiveIconClasses) . ': ! (' . $alpineActive . '), ' . \Illuminate\Support\Js::from($activeIconClasses) . ': ' . $alpineActive . ' }' : null"
            @class([
                $iconClasses,
                $inactiveIconClasses => (! $hasAlpineActiveClasses) && (! $active),
                $activeIconClasses => (! $hasAlpineActiveClasses) && $active,
            ])
        />
    @endif

    @if (filled($badge))
        <x-filament::badge size="sm">
            {{ $badge }}
        </x-filament::badge>
    @endif
</{{ $tag }}>
