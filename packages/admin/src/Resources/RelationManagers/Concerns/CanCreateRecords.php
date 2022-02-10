<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Filament\Tables\Actions\Modal\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Model;

trait CanCreateRecords
{
    protected function canCreate(): bool
    {
        return $this->can('create');
    }

    protected function getCreateFormSchema(): array
    {
        return $this->getResourceForm()->getSchema();
    }

    protected function fillCreateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeCreateFill');

        $this->getMountedTableActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterCreateFill');
    }

    public function create(bool $another = false): void
    {
        $form = $this->getMountedTableActionForm();

        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $form->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $data = $this->mutateFormDataBeforeCreate($data);

        $this->callHook('beforeCreate');

        $record = $this->handleRecordCreation($data);

        $form->model($record)->saveRelationships();

        $this->mountedTableActionRecord = $record->getKey();

        $this->callHook('afterCreate');

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $form->model($record::class);
            $this->mountedTableActionRecord = null;

            $form->fill();
        }

        $this->notify('success', __('filament::resources/relation-managers/create.action.messages.created'));
    }

    public function createAndCreateAnother(): void
    {
        $this->create(another: true);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return $this->getRelationship()->create($data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function getCreateAction(): Tables\Actions\ButtonAction
    {
        return Tables\Actions\ButtonAction::make('create')
            ->label(__('filament::resources/relation-managers/create.action.label'))
            ->form($this->getCreateFormSchema())
            ->mountUsing(fn () => $this->fillCreateForm())
            ->modalActions([
                ButtonAction::make('create')
                    ->label(__('filament::resources/relation-managers/create.action.modal.actions.create.label'))
                    ->submit('callMountedTableAction')
                    ->color('primary'),
                ButtonAction::make('createAndCreateAnother')
                    ->label(__('filament::resources/relation-managers/create.action.modal.actions.create_and_create_another.label'))
                    ->action('createAndCreateAnother')
                    ->color('secondary'),
                ButtonAction::make('cancel')
                    ->label(__('tables::table.actions.modal.buttons.cancel.label'))
                    ->cancel()
                    ->color('secondary'),
            ])
            ->modalHeading(__('filament::resources/relation-managers/create.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->action(fn () => $this->create());
    }
}
