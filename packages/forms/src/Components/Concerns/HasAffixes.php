<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasAffixes
{
    protected string | Closure | null $postfixLabel = null;

    protected string | Closure | null $prefixLabel = null;

    public function prefix(string | Closure | null $label): static
    {
        $this->prefixLabel = $label;

        return $this;
    }

    public function postfix(string | Closure | null $label): static
    {
        $this->postfixLabel = $label;

        return $this;
    }

    public function suffix(string | Closure | null $label): static
    {
        return $this->postfix($label);
    }

    public function getPrefixLabel()
    {
        return $this->evaluate($this->prefixLabel);
    }

    public function getPostfixLabel()
    {
        return $this->evaluate($this->postfixLabel);
    }
}
