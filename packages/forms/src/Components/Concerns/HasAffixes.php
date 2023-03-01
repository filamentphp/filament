<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Arr;

trait HasAffixes
{
    /**
     * @var array<Action> | null
     */
    protected ?array $cachedSuffixActions = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $suffixActions = [];

    protected string | Closure | null $suffixLabel = null;

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedPrefixActions = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $prefixActions = [];

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

    public function prefixAction(Action | Closure $action): static
    {
        $this->prefixActions([$action]);

        return $this;
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function prefixActions(array $actions): static
    {
        $this->prefixActions = array_merge($this->prefixActions, $actions);

        return $this;
    }

    public function suffixAction(Action | Closure $action): static
    {
        $this->suffixActions([$action]);

        return $this;
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function suffixActions(array $actions): static
    {
        $this->suffixActions = array_merge($this->suffixActions, $actions);

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

    /**
     * @return array<Action>
     */
    public function getPrefixActions(): array
    {
        return $this->cachedPrefixActions ?? $this->cachePrefixActions();
    }

    /**
     * @return array<Action>
     */
    public function cachePrefixActions(): array
    {
        $this->cachedPrefixActions = [];

        foreach ($this->prefixActions as $prefixAction) {
            foreach (Arr::wrap($this->evaluate($prefixAction)) as $action) {
                $this->cachedPrefixActions[$action->getName()] = $this->prepareAction($action);
            }
        }

        return $this->cachedPrefixActions;
    }

    /**
     * @return array<Action>
     */
    public function getSuffixActions(): array
    {
        return $this->cachedSuffixActions ?? $this->cacheSuffixActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheSuffixActions(): array
    {
        $this->cachedSuffixActions = [];

        foreach ($this->suffixActions as $suffixAction) {
            foreach (Arr::wrap($this->evaluate($suffixAction)) as $action) {
                $this->cachedSuffixActions[$action->getName()] = $this->prepareAction($action);
            }
        }

        return $this->cachedSuffixActions;
    }

    public function getPrefixLabel(): ?string
    {
        return $this->evaluate($this->prefixLabel);
    }

    public function getPostfixLabel(): ?string
    {
        return $this->getSuffixLabel();
    }

    public function getSuffixLabel(): ?string
    {
        return $this->evaluate($this->suffixLabel);
    }

    public function getPrefixIcon(): ?string
    {
        return $this->evaluate($this->prefixIcon);
    }

    public function getSuffixIcon(): ?string
    {
        return $this->evaluate($this->suffixIcon);
    }
}
