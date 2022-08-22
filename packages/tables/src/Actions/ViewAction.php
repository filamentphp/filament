<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Forms\ComponentContainer;
use Illuminate\Database\Eloquent\Model;

class ViewAction extends Action
{
    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'view';
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

        $this->disableForm();

        $this->mountUsing(function (ComponentContainer $form, Model $record): void {
            $data = $record->attributesToArray();

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            $form->fill($data);
        });

        $this->action(static function (): void {
        });
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }
}
