<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DissociateAction extends Action
{
    use CanCustomizeProcess;
    use Concerns\InteractsWithRelationship;

    public static function make(string $name = 'dissociate'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/dissociate.single.label'));

        $this->modalHeading(fn (DissociateAction $action): string => __('filament-support::actions/dissociate.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/dissociate.single.modal.actions.dissociate.label'));

        $this->successNotificationMessage(__('filament-support::actions/dissociate.single.messages.dissociated'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(static function (DissociateAction $action): void {
            $action->process(static function (Model $record) use ($action): void {
                /** @var BelongsTo $inverseRelationship */
                $inverseRelationship = $action->getInverseRelationshipFor($record);

                $inverseRelationship->dissociate();
                $record->save();
            });

            $action->success();
        });
    }
}
