<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\Action;

trait CanToggleColumns
{
    /**
     * @var int | array<string, int | null> | Closure
     */
    protected int | array | Closure $columnToggleFormColumns = 1;

    protected string | Closure | null $columnToggleFormMaxHeight = null;

    protected string | Closure | null $columnToggleFormWidth = null;

    protected ?Closure $modifyToggleColumnsTriggerActionUsing = null;

    public function toggleColumnsTriggerAction(?Closure $callback): static
    {
        $this->modifyToggleColumnsTriggerActionUsing = $callback;

        return $this;
    }

    /**
     * @param  int | array<string, int | null> | Closure  $columns
     */
    public function columnToggleFormColumns(int | array | Closure $columns): static
    {
        $this->columnToggleFormColumns = $columns;

        return $this;
    }

    public function columnToggleFormMaxHeight(string | Closure | null $height): static
    {
        $this->columnToggleFormMaxHeight = $height;

        return $this;
    }

    public function columnToggleFormWidth(string | Closure | null $width): static
    {
        $this->columnToggleFormWidth = $width;

        return $this;
    }

    public function getToggleColumnsTriggerAction(): Action
    {
        $action = Action::make('toggleColumns')
            ->label(__('filament-tables::table.actions.toggle_columns.label'))
            ->iconButton()
            ->icon('heroicon-m-view-columns')
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->table($this);

        if ($this->modifyToggleColumnsTriggerActionUsing) {
            $action = $this->evaluate($this->modifyToggleColumnsTriggerActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        if ($action->getView() === Action::BUTTON_VIEW) {
            $action->defaultSize(ActionSize::Small);
        }

        return $action;
    }

    public function getColumnToggleForm(): Form
    {
        return $this->getLivewire()->getTableColumnToggleForm();
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getColumnToggleFormColumns(): int | array
    {
        return $this->evaluate($this->columnToggleFormColumns) ?? 1;
    }

    public function getColumnToggleFormMaxHeight(): ?string
    {
        return $this->evaluate($this->columnToggleFormMaxHeight);
    }

    public function getColumnToggleFormWidth(): ?string
    {
        return $this->evaluate($this->columnToggleFormWidth) ?? match ($this->getColumnToggleFormColumns()) {
            2 => '2xl',
            3 => '4xl',
            4 => '6xl',
            default => null,
        };
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
