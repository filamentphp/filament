@props([
    'actions' => [],
    'color' => 'gray',
    'icon' => null,
])

<div
    {{
        $attributes
            ->class([
                'fi-in-entry-wrp-hint flex items-center gap-x-3 text-sm',
                match ($color) {
                    'gray' => 'text-gray-500',
                    default => 'text-custom-600 dark:text-custom-400',
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables($color, shades: [400, 500, 600]),
            ])
    }}
>
    @if (! \Filament\Support\is_slot_empty($slot))
        {{ $slot }}
    @endif

    @if ($icon)
        <x-filament::icon
            :icon="$icon"
            @class([
                'fi-in-entry-wrp-hint-icon h-5 w-5',
                match ($color) {
                    'gray' => 'text-gray-400 dark:text-gray-500',
                    default => 'text-custom-500 dark:text-custom-400',
                },
            ])
        />
    @endif

    @if (count($actions))
        <div class="fi-in-entry-wrp-hint-action -m-1.5 flex gap-3 items-center">
            @foreach ($actions as $action)
                @php
                    $labeledFromBreakpoint = $action->getLabeledFromBreakpoint();
                @endphp
                <span
                    @class([
                        'inline-flex',
                        '-mx-2' => $action->isIconButton() || $labeledFromBreakpoint,
                        match ($labeledFromBreakpoint) {
                            'sm' => 'sm:mx-0',
                            'md' => 'md:mx-0',
                            'lg' => 'lg:mx-0',
                            'xl' => 'xl:mx-0',
                            '2xl' => '2xl:mx-0',
                            default => null,
                        },
                    ])
                >
                    {{ $action }}
                </span>
            @endforeach
        </div>
    @endif
</div>
