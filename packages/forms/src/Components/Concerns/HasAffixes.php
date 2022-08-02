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

    protected string | Closure | null $prefixIcon = null;

    protected string | Closure | null $suffixIcon = null;

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

    public function prefixIcon(string | Closure | null $iconName): static
    {
        $this->prefixIcon = $iconName;

        return $this;
    }

    public function suffixIcon(string | Closure | null $iconName): static
    {
        $this->suffixIcon = $iconName;

        return $this;
    }

    public function getPrefixAction(): ?Action
    {
        return $this->evaluate($this->prefixAction)?->component($this);
    }

    public function getSuffixAction(): ?Action
    {
        return $this->evaluate($this->suffixAction)?->component($this);
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

    public function getPrefixIcon()
    {
        return $this->evaluate($this->prefixIcon);
    }

    public function getSuffixIcon()
    {
        return $this->evaluate($this->suffixIcon);
    }

    public function getActions(): array
    {
        $prefixAction = $this->getPrefixAction();
        $suffixAction = $this->getSuffixAction();

        return array_merge(
            parent::getActions(),
            $prefixAction ? [$prefixAction->getName() => $prefixAction->component($this)] : [],
            $suffixAction ? [$suffixAction->getName() => $suffixAction->component($this)] : [],
        );
    }
}
