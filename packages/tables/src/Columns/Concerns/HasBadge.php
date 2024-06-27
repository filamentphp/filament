<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Columns\Column;

trait HasBadge
{
    protected bool | Closure $isBadge = false;

    protected string | bool | Closure | null $badgeTooltip = null;

    public function badge(bool | Closure $condition = true): static
    {
        $this->isBadge = $condition;

        return $this;
    }

    public function isBadge(): bool
    {
        return (bool) $this->evaluate($this->isBadge);
    }

    public function badgeTooltip(string | bool | Closure | null $tooltip): static
    {
        $this->badgeTooltip = $tooltip;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $tooltips
     */
    public function badgeTooltips(array | Closure $tooltips): static
    {
        $this->badgeTooltip(function (Column $column, $state) use ($tooltips) {
            $tooltips = $column->evaluate($tooltips);

            $tooltip = null;

            foreach ($tooltips as $conditionalTooltip => $condition) {
                if (is_numeric($conditionalTooltip)) {
                    $tooltip = $condition;
                } elseif ($condition instanceof Closure && $column->evaluate($condition)) {
                    $tooltip = $conditionalTooltip;
                } elseif ($condition === $state) {
                    $tooltip = $conditionalTooltip;
                }
            }

            return $tooltip;
        });

        return $this;
    }

    public function getBadgeTooltip(mixed $state): ?string
    {
        $tooltip = $this->evaluate($this->badgeTooltip, [
            'state' => $state,
        ]);

        if ($tooltip === false) {
            return null;
        }

        if (filled($tooltip)) {
            return $tooltip;
        }

        if (! $state instanceof \Filament\Support\Contracts\HasDescription) {
            return null;
        }

        return $state->getDescription();
    }
}
