<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;
use Illuminate\Database\Eloquent\Model;

trait CanViewRecords
{
    protected static bool $hasViewAction = false;

    protected function hasViewAction(): bool
    {
        return static::$hasViewAction;
    }

    protected function canView(Model $record): bool
    {
        return $this->hasViewAction() && $this->can('view', $record);
    }

    protected function getViewFormSchema(): array
    {
        return $this->getResourceForm(columns: 2, isDisabled: true)->getSchema();
    }

    protected function fillViewForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeViewFill');

        $data = $this->getMountedTableActionRecord()->toArray();

        $this->getMountedTableActionForm()->fill($data);

        $this->callHook('afterFill');
        $this->callHook('afterViewFill');
    }

    protected function getViewAction(): ?Tables\Actions\Action
    {
        return Filament::makeTableAction('view')
            ->label(__('filament::resources/relation-managers/view.action.label'))
            ->form($this->getViewFormSchema())
            ->mountUsing(fn () => $this->fillViewForm())
            ->modalCancelAction(
                ModalAction::make('close')
                    ->label(__('filament::resources/relation-managers/view.action.modal.actions.close.label'))
                    ->cancel()
                    ->color('secondary'),
            )
            ->modalActions(fn (Tables\Actions\Action $action): array => [$action->getModalCancelAction()])
            ->modalHeading(__('filament::resources/relation-managers/view.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->action(function () {
            })
            ->icon('heroicon-s-eye')
            ->hidden(fn (Model $record): bool => ! static::canView($record));
    }
}
