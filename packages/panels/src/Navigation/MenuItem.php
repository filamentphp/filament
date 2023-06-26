<?php

namespace Filament\Navigation;

use Closure;
use Laravel\SerializableClosure\Serializers\Native;

class MenuItem
{
    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    protected string | array | null $color = null;

    protected ?string $icon = null;

    protected ?string $label = null;

    protected ?int $sort = null;

    protected string | Closure | Native | null $url = null;

    final public function __construct()
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null  $color
     */
    public function color(string | array | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function sort(?int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function url(string | Closure | null $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getColor(): string | array | null
    {
        return $this->color;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getSort(): int
    {
        return $this->sort ?? -1;
    }

    public function getUrl(): ?string
    {
        return value($this->url);
    }
}
