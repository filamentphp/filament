<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DetachBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'detach';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::detach.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-actions::detach.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::detach.multiple.modal.actions.detach.label'));

        $this->successNotificationTitle(__('filament-actions::detach.multiple.notifications.detached.title'));

        $this->color('danger');

        $this->icon('heroicon-m-x-mark');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-o-x-mark');

        $this->action(function (): void {
            $this->process(function (Collection $records, Table $table): void {
                /** @var BelongsToMany $relationship */
                $relationship = $table->getRelationship();

                if ($table->allowsDuplicates()) {
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
