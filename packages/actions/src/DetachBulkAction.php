<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Throwable;

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

        $this->modalHeading(fn (): string => __('filament-actions::detach.multiple.modal.heading', ['label' => $this->getTitleCasePluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::detach.multiple.modal.actions.detach.label'));

        $this->successNotificationTitle(__('filament-actions::detach.multiple.notifications.detached.title'));

        $this->defaultColor('danger');

        $this->icon(FilamentIcon::resolve(ActionsIconAlias::DETACH_ACTION) ?? Heroicon::XMark);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve(ActionsIconAlias::DETACH_ACTION_MODAL) ?? Heroicon::OutlinedXMark);

        $this->action(function (): void {
            $this->process(function (DetachBulkAction $action, EloquentCollection | Collection | LazyCollection $records, Table $table): void {
                /** @var BelongsToMany $relationship */
                $relationship = $table->getRelationship();

                if (! $action->shouldFetchSelectedRecords()) {
                    try {
                        $action->reportBulkProcessingSuccessfulRecordsCount(
                            $relationship->detach($records),
                        );
                    } catch (Throwable $exception) {
                        $action->reportCompleteBulkProcessingFailure();

                        report($exception);
                    }

                    return;
                }

                $relationshipPivotAccessor = $relationship->getPivotAccessor();

                $isFirstException = true;

                $records->each(
                    function (Model $record) use ($action, &$isFirstException, $relationshipPivotAccessor): void {
                        try {
                            $record->getRelationValue($relationshipPivotAccessor)->delete();
                        } catch (Throwable $exception) {
                            $action->reportBulkProcessingFailure();

                            if ($isFirstException) {
                                // Only report the first exception so as to not flood error logs. Even
                                // if Filament did not catch exceptions like this, only the first
                                // would be reported as the rest of the process would be halted.
                                report($exception);

                                $isFirstException = false;
                            }
                        }
                    },
                );
            });
        });

        $this->deselectRecordsAfterCompletion();
    }
}
