<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class CreateAction extends Action
{
    use CanCustomizeProcess;
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
                    ->label(__('filament-support::actions/create.single.modal.actions.create_another.label')),
            ];
        });

        $this->successNotificationMessage(__('filament-support::actions/create.single.messages.created'));

        $this->button();

        $this->action(static function (CreateAction $action, ComponentContainer $form, HasTable $livewire): void {
            $model = $action->getModel();

            $record = $action->process(static function (array $data) use ($action, $model) {
                if ($relationship = $action->getRelationship()) {
                    if ($relationship instanceof BelongsToMany) {
                        $pivotColumns = $relationship->getPivotColumns();
                        $data = Arr::except($data, $pivotColumns);
                    }

                    $record = $relationship->create($data);

                    if ($relationship instanceof BelongsToMany) {
                        $pivotData = Arr::only($data, $pivotColumns);

                        if (count($pivotColumns)) {
                            $record->{$relationship->getPivotAccessor()}->update($pivotData);
                        }
                    }

                    return $record;
                }

                return $model::create($data);
            });

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
