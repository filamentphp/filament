<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanEditRecords
{
    protected function canEdit(Model $record): bool
    {
        return $this->can('update', $record);
    }

    protected function getEditFormSchema(): array
    {
        return $this->getResourceForm()->getSchema();
    }

    protected function fillEditForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeEditFill');

        $this->getMountedTableActionForm()->fill(
            $this->getMountedTableActionRecord()->toArray(),
        );

        $this->callHook('afterFill');
        $this->callHook('afterEditFill');
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');
        $this->callHook('beforeEditValidate');

        $data = $this->getMountedTableActionForm()->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterEditValidate');

        $data = $this->mutateFormDataBeforeSave($data);

        $this->callHook('beforeSave');

        $this->getMountedTableActionRecord()->update($data);

        $this->callHook('afterSave');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function getEditLinkTableAction(): Tables\Actions\LinkAction
    {
        return Tables\Actions\LinkAction::make('edit')
            ->label(__('filament::resources/relation-managers/edit.action.label'))
            ->form($this->getEditFormSchema())
            ->mountUsing(fn () => $this->fillEditForm())
            ->modalButton(__('filament::resources/relation-managers/edit.action.modal.actions.save.label'))
            ->modalHeading(__('filament::resources/relation-managers/edit.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->action(fn () => $this->save())
            ->hidden(fn (Model $record): bool => ! static::canEdit($record));
    }
}
