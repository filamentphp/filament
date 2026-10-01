<?php

namespace Filament\Tests\Fixtures\Pages;

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
                DissociateBulkAction::make()
                    ->extraAttributes(['data-testid' => 'dissociate-posts'])
                    ->modalSubmitAction(static fn ($action) => $action->extraAttributes(['data-testid' => 'confirm-dissociate']))
                    ->failureNotificationTitle(static fn (int $successCount, int $failureCount): string => "{$successCount} dissociated, {$failureCount} failed"),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([EmbeddedTable::make()]);
    }
}
