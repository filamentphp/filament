<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
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

        $this->modalHeading(fn (): string => __('filament-actions::detach.multiple.modal.heading', ['label' => $this->getTitleCasePluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::detach.multiple.modal.actions.detach.label'));

        $this->successNotificationTitle(__('filament-actions::detach.multiple.notifications.detached.title'));

        $this->color('danger');

        $this->icon(FilamentIcon::resolve('actions::detach-action') ?? Heroicon::XMark);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve('actions::detach-action.modal') ?? Heroicon::OutlinedXMark);

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
