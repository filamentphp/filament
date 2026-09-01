<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Throwable;

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

        $this->modalHeading(fn (): string => __('filament-actions::dissociate.multiple.modal.heading', ['label' => $this->getTitleCasePluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::dissociate.multiple.modal.actions.dissociate.label'));

        $this->successNotificationTitle(__('filament-actions::dissociate.multiple.notifications.dissociated.title'));

        $this->defaultColor('danger');

        $this->icon(FilamentIcon::resolve(ActionsIconAlias::DISSOCIATE_ACTION) ?? Heroicon::XMark);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve(ActionsIconAlias::DISSOCIATE_ACTION_MODAL) ?? Heroicon::OutlinedXMark);

        $this->action(function (): void {
            $this->process(function (DissociateBulkAction $action, EloquentCollection | Collection | LazyCollection $records, Table $table): void {
                if (! $action->shouldFetchSelectedRecords()) {
                    /** @var HasMany $relationship */
                    $relationship = $table->getRelationship();

                    try {
                        $action->reportBulkProcessingSuccessfulRecordsCount(
                            $action->getSelectedRecordsQuery()->update([
                                $relationship->getQualifiedForeignKeyName() => null,
                            ]),
                        );
                    } catch (Throwable $exception) {
                        $action->reportCompleteBulkProcessingFailure();

                        report($exception);
                    }

                    return;
                }

                $isFirstException = true;

                $records->each(function (Model $record) use ($action, &$isFirstException, $table): void {
                    try {
                        /** @var BelongsTo $inverseRelationship */
                        $inverseRelationship = $table->getInverseRelationshipFor($record);

                        $inverseRelationship->dissociate();
                        $record->save();
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
                });
            });
        });

        $this->deselectRecordsAfterCompletion();
    }
}
