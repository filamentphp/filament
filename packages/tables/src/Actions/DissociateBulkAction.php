<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DissociateBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'dissociate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::dissociate.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-actions::dissociate.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::dissociate.multiple.modal.actions.dissociate.label'));

        $this->successNotificationTitle(__('filament-actions::dissociate.multiple.notifications.dissociated.title'));

        $this->color('danger');

        $this->icon('heroicon-m-x-mark');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-o-x-mark');

        $this->action(function (): void {
            $this->process(function (Collection $records, Table $table): void {
                $records->each(function (Model $record) use ($table): void {
                    /** @var BelongsTo $inverseRelationship */
                    $inverseRelationship = $table->getInverseRelationshipFor($record);

                    $inverseRelationship->dissociate();
                    $record->save();
                });
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
