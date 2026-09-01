<?php

namespace Filament\Forms\Components\Concerns;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Illuminate\Contracts\Support\Htmlable;
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

    protected string | Htmlable | Closure | null $suffixLabel = null;

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedPrefixActions = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $prefixActions = [];

    protected string | Htmlable | Closure | null $prefixLabel = null;

    protected string | BackedEnum | Htmlable | Closure | null $prefixIcon = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $prefixIconColor = null;

    protected string | BackedEnum | Htmlable | Closure | null $suffixIcon = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $suffixIconColor = null;

    protected bool | Closure $isPrefixInline = false;

    protected bool | Closure $isSuffixInline = false;

    public function prefix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        $this->prefixLabel = $label;
        $this->inlinePrefix($isInline);

        return $this;
    }

    public function postfix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        return $this->suffix($label, $isInline);
    }

    public function prefixAction(Action | Closure $action, bool | Closure $isInline = false): static
    {
        $this->prefixActions([$action], $isInline);

        return $this;
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function prefixActions(array $actions, bool | Closure $isInline = false): static
    {
        $this->prefixActions = [
            ...$this->prefixActions,
            ...$actions,
        ];
        $this->inlinePrefix($isInline);

        return $this;
    }

    public function suffixAction(Action | Closure $action, bool | Closure $isInline = false): static
    {
        $this->suffixActions([$action], $isInline);

        return $this;
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function suffixActions(array $actions, bool | Closure $isInline = false): static
    {
        $this->suffixActions = [
            ...$this->suffixActions,
            ...$actions,
        ];
        $this->inlineSuffix($isInline);

        return $this;
    }

    public function suffix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        $this->suffixLabel = $label;
        $this->inlineSuffix($isInline);

        return $this;
    }

    public function inlinePrefix(bool | Closure $isInline = true): static
    {
        $this->isPrefixInline = $isInline;

        return $this;
    }

    public function inlineSuffix(bool | Closure $isInline = true): static
    {
        $this->isSuffixInline = $isInline;

        return $this;
    }

    public function prefixIcon(string | BackedEnum | Htmlable | Closure | null $icon, bool | Closure $isInline = false): static
    {
        $this->prefixIcon = $icon;
        $this->inlinePrefix($isInline);

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function prefixIconColor(string | array | Closure | null $color = null): static
    {
        $this->prefixIconColor = $color;

        return $this;
    }

    public function suffixIcon(string | BackedEnum | Htmlable | Closure | null $icon, bool | Closure $isInline = false): static
    {
        $this->suffixIcon = $icon;
        $this->inlineSuffix($isInline);

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function suffixIconColor(string | array | Closure | null $color = null): static
    {
        $this->suffixIconColor = $color;

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
                $this->cachedPrefixActions[$action->getName()] = $this->prepareAction(
                    $action
                        ->defaultSize(Size::Small)
                        ->defaultView(Action::ICON_BUTTON_VIEW),
                );
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
                $this->cachedSuffixActions[$action->getName()] = $this->prepareAction(
                    $action
                        ->defaultSize(Size::Small)
                        ->defaultView(Action::ICON_BUTTON_VIEW),
                );
            }
        }

        return $this->cachedSuffixActions;
    }

    public function getPrefixLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->prefixLabel);
    }

    public function getSuffixLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->suffixLabel);
    }

    public function getPrefixIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->prefixIcon);
    }

    public function getSuffixIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->suffixIcon);
    }

    /**
     * @return string | array<string> | null
     */
    public function getPrefixIconColor(): string | array | null
    {
        return $this->evaluate($this->prefixIconColor);
    }

    /**
     * @return string | array<string> | null
     */
    public function getSuffixIconColor(): string | array | null
    {
        return $this->evaluate($this->suffixIconColor);
    }

    public function isPrefixInline(): bool
    {
        return (bool) $this->evaluate($this->isPrefixInline);
    }

    public function isSuffixInline(): bool
    {
        return (bool) $this->evaluate($this->isSuffixInline);
    }
}
