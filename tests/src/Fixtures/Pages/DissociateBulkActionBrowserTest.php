<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DissociateBulkAction;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DissociateBulkActionBrowserTest extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    public function table(Table $table): Table
    {
        return $table
            ->relationship(static fn (): HasMany => auth()->user()->posts())
            ->inverseRelationship('author')
            ->columns([TextColumn::make('title')])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make()
                        ->extraAttributes(['data-testid' => 'dissociate-posts'])
                        ->modalSubmitAction(static fn ($action) => $action->extraAttributes(['data-testid' => 'confirm-dissociate']))
                        ->failureNotificationTitle(static fn (int $successCount, int $failureCount): string => "{$successCount} dissociated, {$failureCount} failed"),
                ])
                    ->extraAttributes([
                        'data-testid' => 'bulk-actions-trigger',
                        'data-group-only' => 'group',
                    ])
                    ->extraDropdownAttributes([
                        'data-testid' => 'bulk-actions-dropdown',
                        'data-dropdown-only' => 'array',
                        'data-merge-precedence' => 'first',
                    ])
                    ->extraDropdownAttributes(static fn (): array => [
                        'data-closure-only' => 'closure',
                        'data-merge-precedence' => 'second',
                    ], merge: true),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([EmbeddedTable::make()]);
    }
}
