@props([
    'actions' => [],
    'color' => 'gray',
    'icon' => null,
])

<div
    {{
        $attributes
            ->class([
                'filament-infolists-entry-wrapper-hint flex items-center space-x-2 text-custom-500 rtl:space-x-reverse dark:text-custom-300',
            ])
            ->styles([
                \Filament\Support\get_color_css_variables($color, shades: [300, 500]),
            ])
    }}
>
    @if ($slot->isNotEmpty())
        <span class="text-xs leading-tight">
            {{ $slot }}
        </span>
    @endif

    @if ($icon)
        <x-filament::icon
            :name="$icon"
            alias="infolists::entry-wrapper.hint"
            size="h-5 w-5"
        />
    @endif

    @if (count($actions))
        <div
            class="filament-infolists-entry-wrapper-hint-action flex items-center gap-1"
        >
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</div>
