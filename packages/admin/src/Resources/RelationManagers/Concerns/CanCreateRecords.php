<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanCreateRecords
{
    protected static bool $canCreateAnother = true;

    protected function canCreate(): bool
    {
        return $this->can('create');
    }

    protected static function canCreateAnother(): bool
    {
        return static::$canCreateAnother;
    }

    public static function disableCreateAnother(): void
    {
        static::$canCreateAnother = false;
    }

    protected function getCreateFormSchema(): array
    {
        return $this->getResourceForm(columns: 2)->getSchema();
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

        $this->mountedTableActionRecord($record->getKey());

        $this->callHook('afterCreate');

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $form->model($record::class);
            $this->mountedTableActionRecord(null);

            $form->fill();
        }

        if (filled($this->getCreatedNotificationMessage())) {
            $this->notify('success', $this->getCreatedNotificationMessage());
        }
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        return __('filament::resources/relation-managers/create.action.messages.created');
    }

    public function createAnother(): void
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

    protected function getCreateAction(): Tables\Actions\Action
    {
        return Tables\Actions\CreateAction::make()
            ->form($this->getCreateFormSchema())
            ->mountUsing(fn () => $this->fillCreateForm())
            ->action(fn (array $arguments) => $this->create($arguments['another'] ?? false));
    }
}
