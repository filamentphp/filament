<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\RestoreAction;
use Illuminate\Database\Eloquent\Model;

trait CanRestoreRecords
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions.restore.single.label'));

        $this->successNotification(__('filament-support::actions.restore.messages.restored'));

        $this->color('secondary');

        $this->icon('heroicon-s-reply');

        $this->requiresConfirmation();

        $this->action(static function (RestoreAction $action, $record): void {
            $record->restore();

            $action->sendSuccessNotification();
        });
    }
}
