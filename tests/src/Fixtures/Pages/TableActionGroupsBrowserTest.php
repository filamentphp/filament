<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class TableActionGroupsBrowserTest extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    public string $lastAction = 'None';

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([TextColumn::make('title')])
            ->recordActions([
                ActionGroup::make([
                    Action::make('inspect')->extraAttributes(['data-testid' => 'inspect-item'])->action(function (Post $record): void {
                        $this->lastAction = $record->title;
                    }),
                    Action::make('disabled')->extraAttributes(['data-testid' => 'disabled-item'])->disabled()->action(function (): void {
                        $this->lastAction = 'Disabled action ran';
                    }),
                ])->label('Inspect actions')->extraDropdownAttributes(fn (Post $record): array => ['data-testid' => "inspect-{$record->id}"]),
                ActionGroup::make([
                    Action::make('link')->extraAttributes(['data-testid' => 'link-item'])->url('#top'),
                ])->label('More actions')->extraDropdownAttributes(fn (Post $record): array => ['data-testid' => "more-{$record->id}"]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('count')->extraAttributes(['data-testid' => 'count-item'])->action(function (Collection $records): void {
                        $this->lastAction = "Selected {$records->count()}";
                    }),
                    BulkAction::make('disabledBulk')->extraAttributes(['data-testid' => 'disabled-bulk-item'])->disabled()->action(function (): void {
                        $this->lastAction = 'Disabled bulk action ran';
                    }),
                ])->extraDropdownAttributes(['data-testid' => 'bulk-group']),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
            Text::make(fn (): string => $this->lastAction)->extraAttributes(['data-testid' => 'last-action', 'role' => 'status']),
        ]);
    }
}
