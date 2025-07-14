<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\View\TablesIconAlias;

trait HasColumnManager
{
    protected bool | Closure | null $hasColumnManager = null;

    protected bool | Closure $hasReorderableColumns = false;

    /**
     * @var int | array<string, int | null> | Closure
     */
    protected int | array | Closure $columnManagerColumns = 1;

    protected string | Closure | null $columnManagerMaxHeight = null;

    protected Width | string | Closure | null $columnManagerWidth = null;

    protected ?Closure $modifyColumnManagerTriggerActionUsing = null;

    protected bool | Closure $hasDeferredColumnManager = true;

    protected ?Closure $modifyColumnManagerApplyActionUsing = null;

    public function columnManager(bool | Closure | null $condition = true): static
    {
        $this->hasColumnManager = $condition;

        return $this;
    }

    public function deferColumnManager(bool | Closure $condition = true): static
    {
        $this->hasDeferredColumnManager = $condition;

        return $this;
    }

    public function reorderableColumns(bool | Closure $condition = true): static
    {
        $this->hasReorderableColumns = $condition;

        return $this;
    }

    public function hasColumnManager(): bool
    {
        return (bool) (
            $this->evaluate($this->hasColumnManager) ?? (
                ($hasReorderableColumns = $this->hasReorderableColumns())
                    ? $hasReorderableColumns
                    : $this->hasToggleableColumns()
            )
        );
    }

    public function hasReorderableColumns(): bool
    {
        return (bool) $this->evaluate($this->hasReorderableColumns);
    }

    public function hasDeferredColumnManager(): bool
    {
        return (bool) $this->evaluate($this->hasDeferredColumnManager);
    }

    public function columnManagerApplyAction(?Closure $callback): static
    {
        $this->modifyColumnManagerApplyActionUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `columnManagerTriggerAction()` instead.
     */
    public function toggleColumnsTriggerAction(?Closure $callback): static
    {
        return $this->columnManagerTriggerAction($callback);
    }

    public function columnManagerTriggerAction(?Closure $callback): static
    {
        $this->modifyColumnManagerTriggerActionUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `columnManagerColumns()` instead.
     *
     * @param  int | array<string, int | null> | Closure  $columns
     */
    public function columnToggleFormColumns(int | array | Closure $columns): static
    {
        return $this->columnManagerColumns($columns);
    }

    /**
     * @param  int | array<string, int | null> | Closure  $columns
     */
    public function columnManagerColumns(int | array | Closure $columns): static
    {
        $this->columnManagerColumns = $columns;

        return $this;
    }

    /**
     * @deprecated Use `columnManagerMaxHeight()` instead.
     */
    public function columnToggleFormMaxHeight(string | Closure | null $height): static
    {
        return $this->columnManagerMaxHeight($height);
    }

    public function columnManagerMaxHeight(string | Closure | null $height): static
    {
        $this->columnManagerMaxHeight = $height;

        return $this;
    }

    /**
     * @deprecated Use `columnManagerWidth()` instead.
     */
    public function columnToggleFormWidth(Width | string | Closure | null $width): static
    {
        return $this->columnManagerWidth($width);
    }

    public function columnManagerWidth(Width | string | Closure | null $width): static
    {
        $this->columnManagerWidth = $width;

        return $this;
    }

    /**
     * @deprecated Use `getColumnManagerColumns()` instead.
     *
     * @return int | array<string, int | null>
     */
    public function getColumnToggleFormColumns(): int | array
    {
        return $this->getColumnManagerColumns();
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getColumnManagerColumns(): int | array
    {
        return $this->evaluate($this->columnManagerColumns) ?? 1;
    }

    /**
     * @deprecated Use `getColumnManagerMaxHeight()` instead.
     */
    public function getColumnToggleFormMaxHeight(): ?string
    {
        return $this->getColumnManagerMaxHeight();
    }

    public function getColumnManagerMaxHeight(): ?string
    {
        return $this->evaluate($this->columnManagerMaxHeight);
    }

    /**
     * @deprecated Use `getColumnManagerWidth()` instead.
     */
    public function getColumnToggleFormWidth(): ?string
    {
        return $this->getColumnManagerWidth();
    }

    public function getColumnManagerWidth(): Width | string | null
    {
        return $this->evaluate($this->columnManagerWidth) ?? match ($this->getColumnManagerColumns()) {
            2 => Width::TwoExtraLarge,
            3 => Width::FourExtraLarge,
            4 => Width::SixExtraLarge,
            default => Width::ExtraSmall,
        };
    }

    /**
     * @deprecated Use `getColumnManagerTriggerAction()` instead.
     */
    public function getToggleColumnsTriggerAction(): Action
    {
        return $this->getColumnManagerTriggerAction();
    }

    public function getColumnManagerTriggerAction(): Action
    {
        $action = Action::make('openColumnManager')
            ->label(__('filament-tables::table.actions.column_manager.label'))
            ->iconButton()
            ->icon(FilamentIcon::resolve(TablesIconAlias::ACTIONS_COLUMN_MANAGER) ?? Heroicon::ViewColumns)
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->table($this)
            ->authorize(true);

        if ($this->modifyColumnManagerTriggerActionUsing) {
            $action = $this->evaluate($this->modifyColumnManagerTriggerActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        if ($action->getView() === Action::BUTTON_VIEW) {
            $action->defaultSize(Size::Small);
        }

        return $action;
    }

    public function getColumnManagerApplyAction(): Action
    {
        $action = Action::make('applyTableColumnManager')
            ->label(__('filament-tables::table.column_manager.actions.apply.label'))
            ->button()
            ->visible($this->hasDeferredColumnManager())
            ->alpineClickHandler('applyTableColumnManager')
            ->table($this)
            ->authorize(true);

        if ($this->modifyColumnManagerApplyActionUsing) {
            $action = $this->evaluate($this->modifyColumnManagerApplyActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function hasToggleableColumns(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            return true;
        }

        return false;
    }
}
