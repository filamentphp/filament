<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\View\TablesIconAlias;

trait CanReorderRecords
{
    use HasReorderAnimationDuration;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isReorderAuthorized = true;

    protected string | Closure | null $reorderColumn = null;

    protected string | Closure | null $reorderDirection = null;

    protected ?Closure $modifyReorderRecordsTriggerActionUsing = null;

    protected ?Closure $beforeReorderCallback = null;

    protected ?Closure $afterReorderCallback = null;

    public function reorderRecordsTriggerAction(?Closure $callback): static
    {
        $this->modifyReorderRecordsTriggerActionUsing = $callback;

        return $this;
    }

    public function reorderable(string | Closure | null $column = null, bool | Closure | null $condition = null, string | Closure | null $direction = null, Closure | null $beforeReorder = null, Closure | null $afterReorder = null): static
    {
        $this->reorderColumn = $column;

        if ($condition !== null) {
            $this->isReorderable = $condition;
        }

        $this->reorderDirection = $direction;

        $this->beforeReorderCallback = $beforeReorder;
        $this->afterReorderCallback = $afterReorder;

        return $this;
    }

    public function authorizeReorder(bool | Closure $condition = true): static
    {
        $this->isReorderAuthorized = $condition;

        return $this;
    }

    public function getReorderRecordsTriggerAction(bool $isReordering): Action
    {
        $action = Action::make('reorderRecords')
            ->label($isReordering ? __('filament-tables::table.actions.disable_reordering.label') : __('filament-tables::table.actions.enable_reordering.label'))
            ->iconButton()
            ->icon($isReordering ? (FilamentIcon::resolve(TablesIconAlias::ACTIONS_DISABLE_REORDERING) ?? Heroicon::Check) : (FilamentIcon::resolve(TablesIconAlias::ACTIONS_ENABLE_REORDERING) ?? Heroicon::ArrowsUpDown))
            ->color('gray')
            ->action('toggleTableReordering')
            ->table($this)
            ->authorize(true);

        if ($this->modifyReorderRecordsTriggerActionUsing) {
            $action = $this->evaluate($this->modifyReorderRecordsTriggerActionUsing, [
                'action' => $action,
                'isReordering' => $isReordering,
            ]) ?? $action;
        }

        $action->extraAttributes(['class' => 'fi-force-enabled'], merge: true);

        return $action;
    }

    public function getReorderColumn(): ?string
    {
        return $this->evaluate($this->reorderColumn);
    }

    public function getReorderDirection(): string
    {
        return $this->evaluate($this->reorderDirection) ?? 'asc';
    }

    public function isReorderable(): bool
    {
        return filled($this->getReorderColumn()) && $this->evaluate($this->isReorderable) && $this->isReorderAuthorized();
    }

    public function isReordering(): bool
    {
        return $this->getLivewire()->isTableReordering();
    }

    public function isReorderAuthorized(): bool
    {
        return (bool) $this->evaluate($this->isReorderAuthorized);
    }

    /**
     * @param  array<int | string>  $order
     */
    public function callBeforeReorderCallback(array $order): void
    {
        if ($this->beforeReorderCallback !== null) {
            $this->evaluate($this->beforeReorderCallback, ['order' => $order]);
        }
    }

    /**
     * @param  array<int | string>  $order
     */
    public function callAfterReorderCallback(array $order): void
    {
        if ($this->afterReorderCallback !== null) {
            $this->evaluate($this->afterReorderCallback, ['order' => $order]);
        }
    }
}
