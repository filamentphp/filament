<?php

namespace Filament\Widgets;

class WidgetConfiguration
{
    /**
     * @param  class-string<Widget>  $widget
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        public readonly string $widget,
        protected array $properties = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
