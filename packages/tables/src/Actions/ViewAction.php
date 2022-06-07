<?php

namespace Filament\Tables\Actions;

use Filament\Forms\ComponentContainer;
use Illuminate\Database\Eloquent\Model;

class ViewAction extends Action
{
    public static function make(string $name = 'view'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/view.single.label'));

        $this->modalHeading(fn (ViewAction $action): string => __('filament-support::actions/view.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalActions(fn (ViewAction $action): array => array_merge(
            $action->getExtraModalActions(),
            [$action->getModalCancelAction()->label(__('filament-support::actions/view.single.modal.actions.close.label'))],
        ));

        $this->color('secondary');

        $this->icon('heroicon-s-eye');

        $this->mountUsing(static function (ComponentContainer $form, Model $record): void {
            $form->fill($record->toArray());
        });

        $this->action(static function (): void {
        });
    }
}
