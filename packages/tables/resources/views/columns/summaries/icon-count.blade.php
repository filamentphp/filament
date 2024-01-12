<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class(['fi-ta-icon-count-summary grid gap-y-1.5 px-3 py-4'])
    }}
>
    @if (filled($label = $getLabel()))
        <p class="text-sm font-medium text-gray-950 dark:text-white">
            {{ $label }}
        </p>
    @endif

    @if ($state = $getState())
        <div class="grid gap-y-1.5">
            @foreach ($state as $color => $icons)
                @php
                    $color = json_decode($color);
                @endphp

                @foreach ($icons as $icon => $count)
                    @if (filled($icon))
                        <div class="flex items-center justify-end gap-x-1.5">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{ $count }}
                            </span>

                            <x-filament::icon
                                :icon="$icon"
                                @class([
                                    'fi-ta-icon-count-summary-icon h-6 w-6',
                                    match ($color) {
                                        'gray' => 'fi-color-gray text-gray-400 dark:text-gray-500',
                                        default => 'fi-color-custom text-custom-500 dark:text-custom-400',
                                    },
                                ])
                                @style([
                                    \Filament\Support\get_color_css_variables(
                                        $color,
                                        shades: [400, 500],
                                        alias: 'tables::columns.summaries.icon-count.icon',
                                    ) => $color !== 'gray',
                                ])
                            />
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    @endif
</div>
