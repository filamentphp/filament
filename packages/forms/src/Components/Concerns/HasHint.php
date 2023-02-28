<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

trait HasHint
{
    protected string | HtmlString | Closure | null $hint = null;

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedHintActions = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $hintActions = [];

    protected string | Closure | null $hintColor = null;

    protected string | Closure | null $hintIcon = null;

    public function hint(string | HtmlString | Closure | null $hint): static
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

    public function hintAction(Action | Closure $action): static
    {
        $this->hintActions([$action]);

        return $this;
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function hintActions(array $actions): static
    {
        $this->hintActions = array_merge($this->hintActions, $actions);

        return $this;
    }

    public function getHint(): string | HtmlString | null
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

    /**
     * @return array<Action>
     */
    public function getHintActions(): array
    {
        return $this->cachedHintActions ?? $this->cacheHintActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheHintActions(): array
    {
        $this->cachedHintActions = [];

        foreach ($this->hintActions as $hintAction) {
            foreach (Arr::wrap($this->evaluate($hintAction)) as $action) {
                $this->cachedHintActions[$action->getName()] = $this->prepareAction($action);
            }
        }

        return $this->cachedHintActions;
    }
}
