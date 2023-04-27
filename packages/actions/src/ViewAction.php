<?php

namespace Filament\Actions;

class ViewAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'view';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::view.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::view.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitAction(false);
        $this->modalCancelAction(fn (StaticAction $action) => $action->label(__('filament-actions::view.single.modal.actions.close.label')));

        $this->color('gray');

        $this->groupedIcon('heroicon-m-eye');

        $this->disabledForm();
    }
}
