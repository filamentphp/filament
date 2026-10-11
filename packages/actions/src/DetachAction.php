<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DetachAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'detach';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::detach.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::detach.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::detach.single.modal.actions.detach.label'));

        $this->successNotificationTitle(__('filament-actions::detach.single.notifications.detached.title'));

        $this->defaultColor('danger');

        $this->icon(FilamentIcon::resolve(ActionsIconAlias::DETACH_ACTION) ?? Heroicon::XMark);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve(ActionsIconAlias::DETACH_ACTION_MODAL) ?? Heroicon::OutlinedXMark);

        $this->action(function (): void {
            $result = $this->process(function (Model $record, Table $table): bool {
                /** @var BelongsToMany $relationship */
                $relationship = $table->getRelationship();

                if ($table->allowsDuplicates()) {
                    return (bool) $record->getRelationValue($relationship->getPivotAccessor())->delete();
                }

                return (bool) $relationship->detach($record);
            });

            if ($result === false) {
                $this->failure();

                return;
            }

            $this->success();
        });
    }
}
