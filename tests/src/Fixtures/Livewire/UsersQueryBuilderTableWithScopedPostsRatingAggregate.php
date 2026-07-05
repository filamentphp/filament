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

class UsersQueryBuilderTableWithScopedPostsRatingAggregate extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                QueryBuilder::make('query_builder')
                    ->constraints([
                        NumberConstraint::make('posts_rating')
                            ->relationship(name: 'posts', titleAttribute: 'rating')
                            ->modifyRelationshipQueryUsing(fn ($query) => $query->where('is_published', true)),
                    ]),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
