<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasIndicator
{
    protected string | Closure | null $indicator = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $indicatorColor = null;

    public function indicator(string | Closure | null $indicator): static
    {
        $this->indicator = $indicator;

        return $this;
    }

    /**
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
     */
    public function indicatorColor(string | array | Closure | null $color): static
    {
        $this->indicatorColor = $color;

        return $this;
    }

    public function getIndicator(): ?string
    {
        return $this->evaluate($this->indicator);
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getIndicatorColor(): string | array | null
    {
        return $this->evaluate($this->indicatorColor);
    }
}
