<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Support\Contracts\HasIcon as IconInterface;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\Column;

trait HasIcon
{
    protected string | bool | Closure | null $icon = null;

    protected IconPosition | string | Closure | null $iconPosition = null;

    public function icon(string | bool | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $icons
     */
    public function icons(array | Closure $icons): static
    {
        $this->icon(function (Column $column, $state) use ($icons) {
            $icons = $column->evaluate($icons);

            $icon = null;

            foreach ($icons as $conditionalIcon => $condition) {
                if (is_numeric($conditionalIcon)) {
                    $icon = $condition;
                } elseif ($condition instanceof Closure && $column->evaluate($condition)) {
                    $icon = $conditionalIcon;
                } elseif ($condition === $state) {
                    $icon = $conditionalIcon;
                }
            }

            return $icon;
        });

        return $this;
    }

    public function iconPosition(IconPosition | string | Closure | null $iconPosition): static
    {
        $this->iconPosition = $iconPosition;

        return $this;
    }

    public function getIcon(mixed $state): ?string
    {
        $icon = $this->evaluate($this->icon, [
            'state' => $state,
        ]);

        if ($icon === false) {
            return null;
        }

        if (filled($icon)) {
            return $icon;
        }

        if (! $state instanceof IconInterface) {
            return null;
        }

        return $state->getIcon();
    }

    public function getIconPosition(): IconPosition | string
    {
        return $this->evaluate($this->iconPosition) ?? IconPosition::Before;
    }
}
