<div
    {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['filament-tables-icon-count-summary space-y-1 px-4 py-3 text-sm']) }}
>
    @if (filled($label = $getLabel()))
        <p class="text-gray-500 dark:text-gray-400">{{ $label }}:</p>
    @endif

    @foreach ($getState() as $color => $icons)
        @foreach ($icons as $icon => $count)
            @if ($icon)
                <div class="flex items-center space-x-1">
                    <span>
                        {{ $count }}
                    </span>

                    <span class="text-gray-500 dark:text-gray-400">
                        &times;
                    </span>

                    <x-filament::icon
                        :name="$icon"
                        alias="tables::columns.summaries.icon-count"
                        color="text-custom-500"
                        size="h-4 w-4"
                        :style="\Filament\Support\get_color_css_variables(json_decode($color) ?? 'gray', shades: [500])"
                    />
                </div>
            @endif
        @endforeach
    @endforeach
</div>
