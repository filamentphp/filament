<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;

trait HasIcons
{
    protected string | Closure | null $icon = null;

    protected string | Closure | null $iconPosition = null;

    public function icon(string | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function icons(array | Closure $icons): static
    {
        $this->icon(function (Column $column, Model $record, $state) use ($icons) {
            $icons = $column->evaluate($icons);
            $stateIcon = null;

            foreach ($icons as $icon => $condition) {
                if (is_numeric($icon)) {
                    $stateIcon = $condition;
                } elseif ($condition instanceof Closure && $condition($state, $record)) {
                    $stateIcon = $icon;
                } elseif ($condition === $state) {
                    $stateIcon = $icon;
                }
            }

            return $stateIcon;
        });

        return $this;
    }

    public function iconPosition(string | Closure | null $iconPosition): static
    {
        $this->iconPosition = $iconPosition;

        return $this;
    }

    public function getStateIcon(): ?string
    {
        return $this->evaluate($this->icon, [
            'state' => $this->getState(),
        ]);
    }

    public function getIconPosition(): string
    {
        return $this->evaluate($this->iconPosition) ?? 'before';
    }
}
