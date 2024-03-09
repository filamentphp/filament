<?php

namespace Filament\Widgets;

class WidgetConfiguration
{
    /**
     * @param  class-string<Widget>  $widget
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        readonly public string $widget,
        protected array $properties = [],
    ) {
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
