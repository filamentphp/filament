{{-- @deprecated Use a schema to render widgets. --}}

@props([
    'columns' => [
        'lg' => 2,
    ],
    'data' => [],
    'widgets' => [],
])

@php
    if (is_array($columns)) {
        $columns['lg'] ??= ($columns ? (is_array($columns) ? null : $columns) : 2);
    }
@endphp

<div {{ $attributes->grid($columns)->class(['fi-wi']) }}>
    @php
        $normalizeWidgetClass = function (string | Filament\Widgets\WidgetConfiguration $widget): string {
            if ($widget instanceof \Filament\Widgets\WidgetConfiguration) {
                return $widget->widget;
            }

            return $widget;
        };
    @endphp

    @foreach ($widgets as $widgetKey => $widget)
        @php
            $widgetClass = $normalizeWidgetClass($widget);
        @endphp

        @livewire(
            $widgetClass,
            [...(($widget instanceof \Filament\Widgets\WidgetConfiguration) ? [...$widget->widget::getDefaultProperties(), ...$widget->getProperties()] : $widget::getDefaultProperties()), ...$data],
            key("{$widgetClass}-{$widgetKey}"),
        )
    @endforeach
</div>
