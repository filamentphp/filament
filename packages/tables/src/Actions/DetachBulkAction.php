<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DetachBulkAction extends BulkAction
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

        $this->label(__('filament-support::actions/detach.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/detach.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalButton(__('filament-support::actions/detach.multiple.modal.actions.detach.label'));

        $this->successNotificationTitle(__('filament-support::actions/detach.multiple.messages.detached'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(function (HasTable $livewire, Collection $records): void {
                /** @var BelongsToMany $relationship */
                $relationship = $this->getRelationship();

                if ($livewire->allowsDuplicates()) {
                    $records->each(
                        fn (Model $record) => $record->{$relationship->getPivotAccessor()}->delete(),
                    );
                } else {
                    $relationship->detach($records);
                }
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
