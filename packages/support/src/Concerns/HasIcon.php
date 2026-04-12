<?php

namespace Filament\Support\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\HtmlString;

trait HasIcon
{
    protected string | BackedEnum | Htmlable | Closure | false | null $icon = null;

    public function icon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        // Security: Icon strings are escaped when rendered as URLs, but
        // invalid icon names from user input will cause rendering errors.
        // Validate against a known allowlist if user-controlled.

        $this->icon = filled($icon) ? $icon : false;

        return $this;
    }

    public function getIcon(string | BackedEnum | Htmlable | null $default = null): string | BackedEnum | Htmlable | null
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
}
