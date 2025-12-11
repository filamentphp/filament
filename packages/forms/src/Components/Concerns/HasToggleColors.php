<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasToggleColors
{
    /**
     * @var string|array<string, string>|Closure|null
     */
    protected string | array | Closure | null $offColor = null;

    /**
     * @var string|array<string, string>|Closure|null
     */
    protected string | array | Closure | null $onColor = null;

    /**
     * @param  string|array<string, string>|Closure|null  $color
     * @return $this
     */
    public function offColor(string | array | Closure | null $color): static
    {
        $this->offColor = $color;

        return $this;
    }

    /**
     * @param  string|array<string, string>|Closure|null  $color
     * @return $this
     */
    public function onColor(string | array | Closure | null $color): static
    {
        $this->onColor = $color;

        return $this;
    }

    /**
     * @return array<string, string>|string|null
     */
    public function getOffColor(): array | string | null
    {
        return $this->evaluate($this->offColor);
    }

    /**
     * @return array<string, string>|string|null
     */
    public function getOnColor(): array | string | null
    {
        return $this->evaluate($this->onColor);
    }
}
