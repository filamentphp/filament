<?php

namespace Filament\Tables\Actions;

use Filament\Resources\Pages\Page;
use Filament\Support\Actions\Concerns\CanNotify;
use Filament\Support\Actions\Concerns\HasLifecycleHooks;
use Illuminate\Database\Eloquent\Collection;

class ForceDeleteBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions.force_delete.label'));

        $this->icon('heroicon-s-trash');

        $this->color('danger');

        $this->visible(function () {
            /** @var Page $page */
            $page = $this->getLivewire();

            return $page::getResource()::canForceDeleteAny();
        });

        $this->successNotification(__('tables::table.bulk_actions.force_delete.messages.deleted'));

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteBulkAction $action, Collection $records): void {
            $records->each(fn ($record) => $record->forceDelete());

            $action->sendSuccessNotification();
        });
    }
}
