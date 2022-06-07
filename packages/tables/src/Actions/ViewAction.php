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

        $this->modalHeading(fn (): string => __('filament-support::actions/view.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalActions(fn (): array => array_merge(
            $this->getExtraModalActions(),
            [$this->getModalCancelAction()->label(__('filament-support::actions/view.single.modal.actions.close.label'))],
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
