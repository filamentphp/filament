<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Tables\Contracts\HasTable;

class CreateAction extends Action
{
    use Concerns\InteractsWithRelationship;

    protected bool | Closure $isCreateAnotherDisabled = false;

    public static function make(string $name = 'create'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/create.single.label'));

        $this->modalHeading(fn (CreateAction $action): string => __('filament-support::actions/create.single.modal.heading', ['label' => $action->getModelLabel()]));

        $this->modalButton(__('filament-support::actions/create.single.modal.actions.create.label'));

        $this->extraModalActions(function (CreateAction $action): array {
            return $action->isCreateAnotherDisabled() ? [] : [
                $this->makeExtraModalAction('createAnother', ['another' => true])
                    ->label(__('filament-support::actions/create.single.modal.actions.create_and_create_another.label')),
            ];
        });

        $this->successNotificationMessage(__('filament-support::actions/create.single.messages.created'));

        $this->button();

        $this->action(static function (CreateAction $action, array $data, ComponentContainer $form, HasTable $livewire): void {
            $model = $action->getModel();

            if ($relationship = $action->getRelationship()) {
                $record = $relationship->create($data);
            } else {
                $record = $action->getModel()::create($data);
            }

            $form->model($record)->saveRelationships();

            if ($arguments['another'] ?? false) {
                // Ensure that the form record is anonymized so that relationships aren't loaded.
                $form->model($model);
                $livewire->mountedTableActionRecord = null;

                $form->fill();

                $action->sendSuccessNotification();
                $action->callAfter();
                $action->hold();

                return;
            }

            $action->success();
        });
    }

    public function disableCreateAnother(bool | Closure $condition = true): static
    {
        $this->isCreateAnotherDisabled = $condition;

        return $this;
    }

    public function isCreateAnotherDisabled(): bool
    {
        return $this->evaluate($this->isCreateAnotherDisabled);
    }
}
