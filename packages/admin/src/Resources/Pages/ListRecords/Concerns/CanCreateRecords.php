<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\Concerns\UsesResourceForm;
use Illuminate\Database\Eloquent\Model;

trait CanCreateRecords
{
    use UsesResourceForm;

    /**
     * @deprecated Use `->disableCreateAnother()` on the action instead.
     */
    protected static bool $canCreateAnother = true;

    protected function hasCreateAction(): bool
    {
        return static::getResource()::canCreate();
    }

    /**
     * @deprecated Use `->disableCreateAnother()` on the action instead.
     */
    protected static function canCreateAnother(): bool
    {
        return static::$canCreateAnother;
    }

    /**
     * @deprecated Use `->disableCreateAnother()` on the action instead.
     */
    public static function disableCreateAnother(): void
    {
        static::$canCreateAnother = false;
    }

    protected function getCreateAction(): Action
    {
        return parent::getCreateAction()
            ->url(null)
            ->form($this->getCreateFormSchema())
            ->mountUsing(fn () => $this->fillCreateForm())
            ->action(fn (array $arguments) => $this->create($arguments['another'] ?? false));
    }

    protected function getCreateFormSchema(): array
    {
        return $this->getResourceForm(columns: 2)->getSchema();
    }

    /**
     * @deprecated Use `->mountUsing()` on the action instead.
     */
    protected function fillCreateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeCreateFill');

        $this->getMountedActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterCreateFill');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function create(bool $another = false): void
    {
        $form = $this->getMountedActionForm();

        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $form->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $data = $this->mutateFormDataBeforeCreate($data);

        $this->callHook('beforeCreate');

        $record = $this->handleRecordCreation($data);

        $form->model($record)->saveRelationships();

        $this->callHook('afterCreate');

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $form->model($record::class);

            $form->fill();
        }

        if (filled($this->getCreatedNotificationMessage())) {
            $this->notify('success', $this->getCreatedNotificationMessage());
        }

        if ($another) {
            $this->getMountedAction()->hold();
        }
    }

    /**
     * @deprecated Use `->successNotificationMessage()` on the action instead.
     */
    protected function getCreatedNotificationMessage(): ?string
    {
        return __('filament-support::actions/create.single.messages.created');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function createAnother(): void
    {
        $this->create(another: true);
    }

    /**
     * @deprecated Use `->using()` on the action instead.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return $this->getModel()::create($data);
    }

    /**
     * @deprecated Use `->mutateFormDataUsing()` on the action instead.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}
