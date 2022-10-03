<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\HtmlString;

trait HasHint
{
    protected string | HtmlString | Closure | null $hint = null;

    protected string | Closure | null $hintIcon = null;

    protected Action | Closure | null $hintAction = null;

    public function hint(string | HtmlString | Closure | null $hint): static
    {
        $this->hint = $hint;

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

    public function getHint(): string | HtmlString | null
    {
        return $this->evaluate($this->hint);
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
