<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'forceDelete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::force-delete.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::force-delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::force-delete.single.modal.actions.delete.label'));

        $this->defaultColor('danger');

        $this->tableIcon(FilamentIcon::resolve(ActionsIconAlias::FORCE_DELETE_ACTION) ?? Heroicon::Trash);
        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::FORCE_DELETE_ACTION_GROUPED) ?? Heroicon::Trash);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve(ActionsIconAlias::FORCE_DELETE_ACTION_MODAL) ?? Heroicon::OutlinedTrash);

        $this->action(function (): void {
            $result = $this->process(static fn (Model $record): ?bool => $record->forceDelete());

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });
    }
}
