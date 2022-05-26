<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;

trait HasAffixes
{
    protected Action | Closure | null $suffixAction = null;

    protected string | Closure | null $suffixLabel = null;

    protected Action | Closure | null $prefixAction = null;

    protected string | Closure | null $prefixLabel = null;

    public function prefix(string | Closure | null $label): static
    {
        $this->prefixLabel = $label;

        return $this;
    }

    public function postfix(string | Closure | null $label): static
    {
        return $this->suffix($label);
    }

    public function prefixAction(Action | Closure | null $action): static
    {
        $this->prefixAction = $action;

        return $this;
    }

    public function suffixAction(Action | Closure | null $action): static
    {
        $this->suffixAction = $action;

        return $this;
    }

    public function suffix(string | Closure | null $label): static
    {
        $this->suffixLabel = $label;

        return $this;
    }

    public function getPrefixAction(): ?Action
    {
        return $this->evaluate($this->prefixAction);
    }

    public function getSuffixAction(): ?Action
    {
        return $this->evaluate($this->suffixAction);
    }

    public function getPrefixLabel()
    {
        return $this->evaluate($this->prefixLabel);
    }

    public function getPostfixLabel()
    {
        return $this->getSuffixLabel();
    }

    public function getSuffixLabel()
    {
        return $this->evaluate($this->suffixLabel);
    }
}
