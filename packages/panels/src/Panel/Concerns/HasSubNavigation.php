<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Pages\Enums\SubNavigationPosition;

trait HasSubNavigation
{
    protected SubNavigationPosition | Closure | null $subNavigationPosition = null;

    protected string | Closure $subNavigationWidth = '18rem';

    public function subNavigationPosition(SubNavigationPosition | Closure | null $position): static
    {
        $this->subNavigationPosition = $position;

        return $this;
    }

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return $this->evaluate($this->subNavigationPosition) ?? SubNavigationPosition::Start;
    }

    public function subNavigationWidth(string | Closure $width): static
    {
        $this->subNavigationWidth = $width;

        return $this;
    }

    public function getSubNavigationWidth(): string
    {
        return $this->evaluate($this->subNavigationWidth);
    }
}
