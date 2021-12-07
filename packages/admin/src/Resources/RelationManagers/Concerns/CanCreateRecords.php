<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Filament\Tables\Actions\Modal\Actions\ButtonAction;

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

        $record = $this->getRelationship()->create($data);
        $form->model($record)->saveRelationships();

        $this->callHook('afterCreate');

        if ($another) {
            $form->fill();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function getCreateButtonTableHeaderAction(): Tables\Actions\ButtonAction
    {
        return Tables\Actions\ButtonAction::make('create')
            ->label(__('filament::resources/relation-managers/create.action.label'))
            ->form($this->getCreateFormSchema())
            ->mountUsing(fn () => $this->fillCreateForm())
            ->modalActions([
                ButtonAction::make('submit')
                    ->label(__('filament::resources/relation-managers/create.action.modal.actions.create.label'))
                    ->submit()
                    ->color('primary'),
                ButtonAction::make('submit')
                    ->label(__('filament::resources/relation-managers/create.action.modal.actions.create_and_create_another.label'))
                    ->action('create(true)')
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
