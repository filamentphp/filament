<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\ScopedDepartmentsRelationManager;
use Livewire\Attributes\Url;

class ScopedActionsBrowserTest extends Page
{
    protected string $view = 'pages.scoped-actions-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    #[Url(as: 'tab')]
    public string $activeTab = 'summary';

    public int $ticket;

    public bool $showDepartments = true;

    public function mount(): void
    {
        $this->ticket = (int) request()->query('ticket');
    }

    public function viewAction(): Action
    {
        return Action::make('view')
            ->requiresConfirmation()
            ->modalHeading('Ticket overview')
            ->extraAttributes(['data-testid' => 'page-view-trigger'])
            ->extraModalWindowAttributes(['data-testid' => 'page-modal'])
            ->modalCancelAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'dismiss-ticket-overview']))
            ->action(static fn () => null);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Actions::make([
                $this->viewAction(),
                Action::make('deepLink')
                    ->label('Open departments link')
                    ->extraAttributes(['data-testid' => 'department-deep-link'])
                    ->url(fn (): string => static::getUrl([
                        'ticket' => $this->ticket,
                        'tab' => 'departments',
                        'actionComponent' => 'departments',
                        'action' => 'view',
                    ])),
                Action::make('toggleDepartments')
                    ->label('Toggle departments')
                    ->extraAttributes(['data-testid' => 'toggle-departments'])
                    ->action(function (): void {
                        $this->showDepartments = ! $this->showDepartments;
                    }),
            ]),
            Tabs::make()->livewireProperty('activeTab')->tabs([
                'summary' => Tab::make('Summary'),
                'departments' => Tab::make('Departments')->schema(fn (): array => $this->showDepartments ? [
                    Livewire::make(ScopedDepartmentsRelationManager::class, fn (): array => [
                        'ownerRecord' => Ticket::findOrFail($this->ticket),
                        'pageClass' => EditTicket::class,
                        'deferTableLoading' => true,
                    ])->key('departments')->lazy(),
                    Livewire::make(ScopedDepartmentsRelationManager::class, fn (): array => [
                        'ownerRecord' => Ticket::findOrFail($this->ticket),
                        'pageClass' => EditTicket::class,
                        'actionUrlIdentifier' => 'other-departments',
                    ])->key('other-departments'),
                ] : []),
            ]),
        ]);
    }
}
