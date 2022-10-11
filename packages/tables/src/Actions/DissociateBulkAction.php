<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DissociateBulkAction extends BulkAction
{
    use CanCustomizeProcess;
    use Concerns\InteractsWithRelationship;

    public static function getDefaultName(): ?string
    {
        return 'dissociate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/dissociate.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/dissociate.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalButton(__('filament-support::actions/dissociate.multiple.modal.actions.dissociate.label'));

        $this->successNotificationTitle(__('filament-support::actions/dissociate.multiple.messages.dissociated'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(function (Collection $records): void {
                $records->each(function (Model $record): void {
                    /** @var BelongsTo $inverseRelationship */
                    $inverseRelationship = $this->getInverseRelationshipFor($record);

                    $inverseRelationship->dissociate();
                    $record->save();
                });
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
