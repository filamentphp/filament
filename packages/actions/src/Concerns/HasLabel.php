<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasLabel
{
    protected string | Htmlable | Closure | null $label = null;

    protected bool | Closure $isLabelHidden = false;

    protected bool $shouldTranslateLabel = false;

    /**
     * @deprecated Use `hiddenLabel()` instead.
     */
    public function disableLabel(bool | Closure $condition = true): static
    {
        $this->hiddenLabel($condition);

        return $this;
    }

    public function hiddenLabel(bool | Closure $condition = true): static
    {
        $this->isLabelHidden = $condition;

        return $this;
    }

    public function label(string | Htmlable | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function translateLabel(bool $shouldTranslateLabel = true): static
    {
        $this->shouldTranslateLabel = $shouldTranslateLabel;

        return $this;
    }

    public function getLabel(): string | Htmlable | null
    {
        $label = $this->evaluate($this->label) ?? (string) str($this->getName())
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return is_string($label) && $this->shouldTranslateLabel
            ? __($label)
            : $label;
    }

    public function isLabelHidden(): bool
    {
        return (bool) $this->evaluate($this->isLabelHidden);
    }
}
