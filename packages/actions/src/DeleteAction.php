<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class DeleteAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::delete.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::delete.single.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::delete.single.notifications.deleted.title'));

        $this->defaultColor('danger');

        $this->tableIcon(FilamentIcon::resolve(ActionsIconAlias::DELETE_ACTION) ?? Heroicon::Trash);
        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::DELETE_ACTION_GROUPED) ?? Heroicon::Trash);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve(ActionsIconAlias::DELETE_ACTION_MODAL) ?? Heroicon::OutlinedTrash);

        $this->keyBindings(['mod+d']);

        $this->hidden(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });

        $this->action(function (): void {
            $result = $this->process(static fn (Model $record): ?bool => $record->delete());

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });
    }
}
