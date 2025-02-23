<?php

namespace Filament\Support\Concerns;

use BackedEnum;
use Closure;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\HtmlString;

trait HasIcon
{
    protected string | BackedEnum | Htmlable | Closure | false | null $icon = null;

    protected IconPosition | string | Closure | null $iconPosition = null;

    protected IconSize | string | Closure | null $iconSize = null;

    public function icon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->icon = filled($icon) ? $icon : false;

        return $this;
    }

    public function iconPosition(IconPosition | string | Closure | null $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function iconSize(IconSize | string | Closure | null $size): static
    {
        $this->iconSize = $size;

        return $this;
    }

    public function getIcon(string | BackedEnum | null $default = null): string | BackedEnum | Htmlable | null
    {
        $icon = $this->evaluate($this->icon);

        // https://github.com/filamentphp/filament/pull/13512
        if ($icon instanceof Renderable) {
            return new HtmlString($icon->render());
        }

        if ($icon === false) {
            return null;
        }

        return $icon ?? $default;
    }

    public function getIconPosition(): IconPosition
    {
        $position = $this->evaluate($this->iconPosition);

        if ($position instanceof IconPosition) {
            return $position;
        }

        return IconPosition::tryFrom($position) ?? IconPosition::Before;
    }

    public function getIconSize(): IconSize | string | null
    {
        return $this->evaluate($this->iconSize);
    }
}
