<?php

namespace Filament\Forms\Components\RichEditor;

class TextColor
{
    public function __construct(
        protected string $label,
        protected string $color,
        protected ?string $darkColor = null,
    ) {}

    public static function make(string $label, string $color, ?string $darkColor = null): static
    {
        return app(static::class, [
            'label' => $label,
            'color' => $color,
            'darkColor' => $darkColor,
        ]);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDarkColor(): string
    {
        return $this->darkColor ?? $this->getColor();
    }
}
