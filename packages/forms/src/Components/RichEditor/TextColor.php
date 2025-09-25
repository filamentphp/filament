<?php

namespace Filament\Forms\Components\RichEditor;

use Filament\Support\Colors\Color;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TextColor
{
    public function __construct(
        protected ?string $label = null,
        protected ?string $color = null,
        protected ?string $darkColor = null,
    ) {}

    public static function make(?string $label = null, ?string $color = null, ?string $darkColor = null): static
    {
        return app(static::class, [
            'label' => $label,
            'color' => $color,
            'darkColor' => $darkColor,
        ]);
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getSafeLabelHtml(): string
    {
        return e($this->getLabel());
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getDarkColor(): ?string
    {
        return $this->darkColor ?? $this->getColor();
    }

    /**
     * @return array<string, TextColor>
     */
    public static function getDefaults(): array
    {
        return Arr::mapWithKeys(
            Color::all(),
            fn (array $color, string $name): array => [$name => TextColor::make(Str::ucwords($name), $color['600'], $color['400'] ?? null)],
        );
    }
}
