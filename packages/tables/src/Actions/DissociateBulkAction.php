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

    public static function make(string $name = 'dissociate'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/dissociate.multiple.label'));

        $this->modalHeading(__('filament-support::actions/dissociate.multiple.modal.heading'));

        $this->modalButton(__('filament-support::actions/dissociate.multiple.modal.actions.dissociate.label'));

        $this->successNotificationMessage(__('filament-support::actions/dissociate.multiple.messages.dissociated'));

        $this->color('danger');

        $this->icon('heroicon-s-x');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(function (Collection $records): void {
                $records->each(function (Model $recordToDissociate): void {
                    /** @var BelongsTo $inverseRelationship */
                    $inverseRelationship = $this->getInverseRelationshipFor($recordToDissociate);

                    $inverseRelationship->dissociate();
                    $recordToDissociate->save();
                });
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
