<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DetachAction extends Action
{
    use CanCustomizeProcess;
    use Concerns\InteractsWithRelationship;

    public static function getDefaultName(): ?string
    {
        return 'detach';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/detach.single.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/detach.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/detach.single.modal.actions.detach.label'));

        $this->successNotificationMessage(__('filament-support::actions/detach.single.messages.detached'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(function (HasTable $livewire, Model $record): void {
                /** @var BelongsToMany $relationship */
                $relationship = $this->getRelationship();

                if ($livewire->allowsDuplicates()) {
                    $record->{$relationship->getPivotAccessor()}->delete();
                } else {
                    $relationship->detach($record);
                }
            });

            $this->success();
        });
    }
}
