<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Resources\Pages\Concerns\UsesResourceForm;
use Filament\Tables;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait CanViewRecords
{
    use UsesResourceForm;

    protected function hasViewAction(): bool
    {
        return true;
    }

    protected function getViewAction(): Tables\Actions\Action
    {
        $resource = static::getResource();

        return parent::getViewAction()
            ->url(null)
            ->form($this->getViewFormSchema())
            ->mountUsing(fn () => $this->fillViewForm())
            ->modalHeading(fn (Model $record) => __('filament::resources/pages/list-records.table.actions.view.modal.heading', ['label' => $resource::hasRecordTitle() ? $resource::getRecordTitle($record) : Str::title($resource::getLabel())]))
            ->modalCancelAction(
                ModalAction::make('close')
                    ->label(__('filament::resources/pages/list-records.table.actions.view.modal.actions.close.label'))
                    ->cancel()
                    ->color('secondary'),
            )
            ->modalActions(fn (Tables\Actions\Action $action): array => [$action->getModalCancelAction()])
            ->action(function () {
            })
            ->hidden(fn (Model $record) => ! $resource::canView($record));
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
}
