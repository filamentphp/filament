<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DetachAction extends Action
{
    use CanCustomizeProcess;
    use Concerns\InteractsWithRelationship;

    public static function make(string $name = 'detach'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/detach.single.label'));

        $this->modalHeading(fn (DetachAction $action): string => __('filament-support::actions/detach.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/detach.single.modal.actions.detach.label'));

        $this->successNotificationMessage(__('filament-support::actions/detach.single.messages.detached'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(static function (DetachAction $action): void {
            $action->process(static function (Model $record) use ($action): void {
                /** @var BelongsToMany $relationship */
                $relationship = $this->getRelationship();

                $record->{$relationship->getPivotAccessor()}->delete();
            });

            $action->success();
        });
    }
}
