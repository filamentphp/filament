<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Tables\Table;

class PostsTableWithGuardedFiltersResetAction extends PostsTable
{
    public string $resetActionState = 'hidden';

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersResetAction(
                static fn (Action $action, self $livewire): Action => match ($livewire->resetActionState) {
                    'disabled' => $action->disabled(),
                    'unauthorized' => $action->authorize(false),
                    'invisible' => $action->visible(false),
                    default => $action->hidden(),
                },
            );
    }
}
