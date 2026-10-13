<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;

class QueryBuilderTableTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?int $navigationSort = 8;

    public bool $hasGroupedConstraints = false;

    public function mount(bool $grouped = false): void
    {
        $this->hasGroupedConstraints = $grouped || request()->boolean('grouped');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('content'),
            ])
            ->filters([
                QueryBuilder::make('query_builder')
                    ->constraints($this->hasGroupedConstraints ? [
                        'Content' => [TextConstraint::make('title')],
                        'Details' => [TextConstraint::make('content')],
                    ] : [
                        TextConstraint::make('title'),
                        TextConstraint::make('content'),
                    ])
                    ->constraintPickerColumns($this->hasGroupedConstraints ? 3 : 1),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
