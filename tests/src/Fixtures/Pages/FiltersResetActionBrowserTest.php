<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class FiltersResetActionBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static bool $shouldRegisterNavigation = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_published')
                    ->query(static fn (Builder $query): Builder => $query->where('is_published', true))
                    ->schema([
                        Toggle::make('isActive')
                            ->label('Published')
                            ->extraAttributes(['data-testid' => 'published-filter']),
                    ]),
            ])
            ->deferFilters(false)
            ->filtersTriggerAction(
                static fn (Action $action) => $action
                    ->extraAttributes(['data-testid' => 'filters-trigger']),
            )
            ->filtersResetAction(
                static fn (Action $action) => $action
                    ->label('Reset filters')
                    ->icon(Heroicon::XMark)
                    ->iconButton()
                    ->extraAttributes(['data-testid' => 'filters-reset-action']),
            );
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
