<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;

trait HasIcons
{
    /**
     * @var array<string | Htmlable | null> | Arrayable | Closure
     */
    protected array | Arrayable | Closure $icons = [];

    /**
     * @param  array<string | Htmlable | null> | Arrayable | Closure  $icons
     */
    public function icons(array | Arrayable | Closure $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function getIcon(mixed $value): string | Htmlable | null
    {
        return $this->getIcons()[$value] ?? null;
    }

    /**
     * @return array<string | Htmlable | null>
     */
    public function getIcons(): array
    {
        $icons = $this->evaluate($this->icons);

        if ($icons instanceof Arrayable) {
            $icons = $icons->toArray();
        }

        return $icons;
    }
}
