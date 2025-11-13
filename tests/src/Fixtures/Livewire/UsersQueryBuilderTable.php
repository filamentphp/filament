<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UsersQueryBuilderTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->filters([
                QueryBuilder::make('query_builder')
                    ->constraints([
                        NumberConstraint::make('posts_rating')
                            ->label('Posts Rating Aggregate')
                            ->relationship(name: 'posts', titleAttribute: 'rating'),
                        NumberConstraint::make('teams_budget')
                            ->label('Teams Budget Aggregate')
                            ->relationship(name: 'teams', titleAttribute: 'budget'),
                    ]),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
