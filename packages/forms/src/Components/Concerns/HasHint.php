<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;

trait HasHint
{
    protected string | Htmlable | Closure | null $hint = null;

    protected Action | Closure | null $hintAction = null;

    protected string | Closure | null $hintColor = null;

    protected string | Closure | null $hintIcon = null;

    public function hint(string | Htmlable | Closure | null $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function hintColor(string | Closure | null $hintColor): static
    {
        $this->hintColor = $hintColor;

        return $this;
    }

    public function hintIcon(string | Closure | null $hintIcon): static
    {
        $this->hintIcon = $hintIcon;

        return $this;
    }

    public function hintAction(Action | Closure | null $action): static
    {
        $this->hintAction = $action;

        return $this;
    }

    public function getHint(): string | Htmlable | null
    {
        return $this->evaluate($this->hint);
    }

    public function getHintColor(): ?string
    {
        return $this->evaluate($this->hintColor);
    }

    public function getHintIcon(): ?string
    {
        return $this->evaluate($this->hintIcon);
    }

    public function getHintAction(): ?Action
    {
        return $this->evaluate($this->hintAction)?->component($this);
    }

    public function getActions(): array
    {
        $hintAction = $this->getHintAction();

        return array_merge(
            parent::getActions(),
            $hintAction ? [$hintAction->getName() => $hintAction->component($this)] : [],
        );
    }
}
