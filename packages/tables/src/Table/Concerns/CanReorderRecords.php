<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;

trait CanReorderRecords
{
    use HasReorderAnimationDuration;

    protected bool | Closure $isReorderable = true;

    protected bool | Closure $isReorderAuthorized = true;

    protected string | Closure | null $reorderColumn = null;

    protected ?Closure $modifyReorderRecordsTriggerActionUsing = null;

    public function reorderRecordsTriggerAction(?Closure $callback): static
    {
        $this->modifyReorderRecordsTriggerActionUsing = $callback;

        return $this;
    }

    public function reorderable(string | Closure | null $column = null, bool | Closure | null $condition = null): static
    {
        $this->reorderColumn = $column;

        if ($condition !== null) {
            $this->isReorderable = $condition;
        }

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
            ->icon($isReordering ? (FilamentIcon::resolve('tables::actions.disable-reordering') ?? 'heroicon-m-check') : (FilamentIcon::resolve('tables::actions.enable-reordering') ?? 'heroicon-m-arrows-up-down'))
            ->color('gray')
            ->action('toggleTableReordering')
            ->table($this);

        if ($this->modifyReorderRecordsTriggerActionUsing) {
            $action = $this->evaluate($this->modifyReorderRecordsTriggerActionUsing, [
                'action' => $action,
                'isReordering' => $isReordering,
            ]) ?? $action;
        }

        return $action;
    }

    public function getReorderColumn(): ?string
    {
        return $this->evaluate($this->reorderColumn);
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
}
