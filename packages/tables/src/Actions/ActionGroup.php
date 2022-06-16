<?php

namespace Filament\Tables\Actions;

use Filament\Support\Actions\ActionGroup as BaseActionGroup;
use Filament\Support\Actions\Concerns\InteractsWithRecord;
use Filament\Tables\Actions\Concerns\InteractsWithRecords;

class ActionGroup extends BaseActionGroup
{
    use InteractsWithRecord;

    protected string $view = 'tables::actions.group';

    public function getActions(): array
    {
        return collect($this->actions)
            ->mapWithKeys(fn (Action $action): array  => [
                $action->getName() => $action->grouped()->record($this->getRecord())
            ])
            ->toArray();
    }
}
