@props([
    'columns' => [
        'lg' => 2,
    ],
    'data' => [],
    'widgets' => [],
])

<x-filament::grid
    :default="$columns['default'] ?? 1"
    :sm="$columns['sm'] ?? null"
    :md="$columns['md'] ?? null"
    :lg="$columns['lg'] ?? ($columns ? (is_array($columns) ? null : $columns) : 2)"
    :xl="$columns['xl'] ?? null"
    :two-xl="$columns['2xl'] ?? null"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->class('fi-wi gap-6')"
>
    @php
        $normalizeWidgetClass = function (string | Filament\Widgets\WidgetConfiguration $widget): string {
            if ($widget instanceof \Filament\Widgets\WidgetConfiguration) {
                return $widget->widget;
            }

            return $widget;
        };
    @endphp

    @foreach ($widgets as $key => $widget)
        @livewire(
            $normalizeWidgetClass($widget),
            [...(($widget instanceof \Filament\Widgets\WidgetConfiguration) ? $widget->properties : $widget::getDefaultProperties()), ...$data],
            key($key),
        )
    @endforeach
</x-filament::grid>
