<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\HtmlString;

trait HasHint
{
    protected string | HtmlString | Action | Closure | null $hint = null;

    protected string | Closure | null $hintIcon = null;

    public function hint(string | HtmlString | Closure | Action | null $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function hintIcon(string | Closure | null $hintIcon): static
    {
        $this->hintIcon = $hintIcon;

        return $this;
    }

    public function getHint(): string | HtmlString | Action | null
    {
        return $this->hint instanceof Action ? $this->evaluate($this->hint)?->component($this) : $this->evaluate($this->hint);
    }

    public function getHintIcon(): ?string
    {
        return $this->evaluate($this->hintIcon);
    }

    public function getActions(): array
    {
        $hintAction = $this->getHint();

        return array_merge(
            parent::getActions(),
            $hintAction instanceof Action ? [$hintAction->getName() => $hintAction->component($this)] : [],
        );
    }
}
