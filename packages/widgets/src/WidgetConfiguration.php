<?php

namespace Filament\Widgets;

class WidgetConfiguration
{
    /**
     * @param  class-string<Widget>  $widget
     * @param  array<string, mixed>  $props
     */
    public function __construct(
        readonly public string $widget,
        readonly public array $props = [],
    ) {
    }
}
