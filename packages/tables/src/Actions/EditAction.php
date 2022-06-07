<?php

namespace Filament\Tables\Actions;

use Filament\Forms\ComponentContainer;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class EditAction extends Action
{
    use CanCustomizeProcess;
    use Concerns\InteractsWithRelationship;

    public static function make(string $name = 'edit'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/edit.single.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/edit.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/edit.single.modal.actions.edit.label'));

        $this->successNotificationMessage(__('filament-support::actions/edit.single.messages.saved'));

        $this->icon('heroicon-s-pencil');

        $this->mountUsing(static function (ComponentContainer $form, Model $record): void {
            $form->fill($record->toArray());
        });

        $this->action(function (): void {
            $this->process(function (array $data, Model $record) {
                $relationship = $this->getRelationship();

                if ($relationship instanceof BelongsToMany) {
                    $pivotColumns = $relationship->getPivotColumns();
                    $pivotData = Arr::only($data, $pivotColumns);

                    if (count($pivotColumns)) {
                        $record->{$relationship->getPivotAccessor()}->update($pivotData);
                    }

                    $data = Arr::except($data, $pivotColumns);
                }

                $record->update($data);
            });

            $this->success();
        });
    }
}
