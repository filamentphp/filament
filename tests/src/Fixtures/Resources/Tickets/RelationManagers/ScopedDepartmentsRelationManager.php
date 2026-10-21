<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;

class ScopedDepartmentsRelationManager extends DepartmentsRelationManager
{
    #[Locked]
    public string $actionUrlIdentifier = 'departments';

    #[Locked]
    public bool $deferTableLoading = false;

    public function getDefaultActionUrlIdentifier(): ?string
    {
        return $this->actionUrlIdentifier;
    }

    public function viewAction(): Action
    {
        return Action::make('view')
            ->requiresConfirmation()
            ->modalHeading('Department overview')
            ->extraModalWindowAttributes(['data-testid' => "{$this->actionUrlIdentifier}-modal"])
            ->modalCancelAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'dismiss-department-overview']))
            ->action(static fn () => null);
    }

    public function table(Table $table): Table
    {
        $table = parent::table($table)
            ->modifyQueryUsing(static fn (Builder $query): Builder => $query->where('name', '!=', 'Excluded department'))
            ->deferLoading($this->deferTableLoading);

        $table->getAction('view')
            ->extraAttributes(['data-testid' => "{$this->actionUrlIdentifier}-view-trigger"])
            ->extraModalWindowAttributes(['data-testid' => "{$this->actionUrlIdentifier}-table-modal"])
            ->modalCancelAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'dismiss-department-record']));

        return $table;
    }
}
