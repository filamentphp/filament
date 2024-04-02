<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasNoResultState
{
    protected View | Htmlable | Closure | null $noResultState = null;

    protected string | Htmlable | Closure | null $noResultStateDescription = null;

    protected string | Htmlable | Closure | null $noResultStateHeading = null;

    protected string | Closure | null $noResultStateIcon = null;

    /**
     * @var array<Action | ActionGroup>
     */
    protected array $noResultStateActions = [];

    public function noResultStateDescription(string | Htmlable | Closure | null $description): static
    {
        $this->noResultStateDescription = $description;

        return $this;
    }

    public function noResultState(View | Htmlable | Closure | null $noResultState): static
    {
        $this->noResultState = $noResultState;

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function noResultStateActions(array | ActionGroup $actions, bool $shouldOverwriteExistingActions = false): static
    {
        $this->noResultStateActions = [];
        $this->pushNoResultStateActions($actions, $shouldOverwriteExistingActions);

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function pushNoResultStateActions(array | ActionGroup $actions, bool $shouldOverwriteExistingActions = false): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $action->table($this);

            if ($action instanceof ActionGroup) {
                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedFlatActions($flatActions, $shouldOverwriteExistingActions);
            } elseif ($action instanceof Action) {
                $this->cacheAction($action, $shouldOverwriteExistingActions);
            } else {
                throw new InvalidArgumentException('Table no result state actions must be an instance of ' . Action::class . ' or ' . ActionGroup::class . '.');
            }

            $this->noResultStateActions[] = $action;
        }

        return $this;
    }

    public function noResultStateHeading(string | Htmlable | Closure | null $heading): static
    {
        $this->noResultStateHeading = $heading;

        return $this;
    }

    public function noResultStateIcon(string | Closure | null $icon): static
    {
        $this->noResultStateIcon = $icon;

        return $this;
    }

    public function getNoResultState(): View | Htmlable | null
    {
        return $this->evaluate($this->noResultState);
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getNoResultStateActions(): array
    {
        return $this->noResultStateActions;
    }

    public function getNoResultStateDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->noResultStateDescription) ?? __('filament-tables::table.no_result.description', [
            'model' => $this->getPluralModelLabel(),
        ]);
    }

    public function getNoResultStateHeading(): string | Htmlable
    {
        return $this->evaluate($this->noResultStateHeading) ?? __('filament-tables::table.no_result.heading', [
            'model' => $this->getPluralModelLabel(),
        ]);
    }

    public function getNoResultStateIcon(): string
    {
        return $this->evaluate($this->noResultStateIcon)
            ?? FilamentIcon::resolve('tables::empty-state')
            ?? 'heroicon-o-x-mark';
    }
}
