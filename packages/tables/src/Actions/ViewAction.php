<?php

namespace Filament\Tables\Actions;

use Closure;
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

        $this->label(__('filament-actions::view.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::view.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalActions(fn (): array => array_merge(
            $this->getExtraModalActions(),
            [$this->getModalCancelAction()->label(__('filament-actions::view.single.modal.actions.close.label'))],
        ));

        $this->color('gray');

        $this->icon('heroicon-m-eye');

        $this->disabledForm();

        $this->fillForm(function (Model $record): array {
            $data = $record->attributesToArray();

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
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
