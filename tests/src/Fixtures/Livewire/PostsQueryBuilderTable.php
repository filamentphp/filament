<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\QueryBuilder\Constraints\BooleanConstraint;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PostsQueryBuilderTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('content'),
                Tables\Columns\TextColumn::make('author.name'),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('rating'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                QueryBuilder::make('query_builder')
                    ->constraints([
                        TextConstraint::make('title'),
                        TextConstraint::make('content')
                            ->nullable(),
                        BooleanConstraint::make('is_published'),
                        NumberConstraint::make('rating'),
                        NumberConstraint::make('rating_integer')
                            ->attribute('rating')
                            ->integer(),
                        DateConstraint::make('created_at'),
                        DateConstraint::make('published_at')
                            ->time(),
                        SelectConstraint::make('rating_select')
                            ->attribute('rating')
                            ->options([
                                1 => 'One Star',
                                2 => 'Two Stars',
                                3 => 'Three Stars',
                                4 => 'Four Stars',
                                5 => 'Five Stars',
                            ]),
                        SelectConstraint::make('rating_select_multiple')
                            ->attribute('rating')
                            ->multiple()
                            ->options([
                                1 => 'One Star',
                                2 => 'Two Stars',
                                3 => 'Three Stars',
                                4 => 'Four Stars',
                                5 => 'Five Stars',
                            ]),
                        TextConstraint::make('author_name')
                            ->label('Author Name')
                            ->relationship(name: 'author', titleAttribute: 'name'),
                        NumberConstraint::make('author_id')
                            ->label('Author ID')
                            ->integer(),
                        RelationshipConstraint::make('author')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                            ),
                        TextConstraint::make('author_email')
                            ->label('Author Email')
                            ->relationship(name: 'author', titleAttribute: 'email'),
                        BooleanConstraint::make('author_has_email_auth')
                            ->label('Author Has Email Auth')
                            ->relationship(name: 'author', titleAttribute: 'has_email_authentication'),
                        NumberConstraint::make('author_score')
                            ->label('Author Score')
                            ->relationship(name: 'author', titleAttribute: 'score'),
                        SelectConstraint::make('author_status')
                            ->label('Author Status')
                            ->relationship(name: 'author', titleAttribute: 'status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'pending' => 'Pending',
                            ]),
                        DateConstraint::make('author_verified_at')
                            ->label('Author Verified At')
                            ->relationship(name: 'author', titleAttribute: 'email_verified_at'),
                        TextConstraint::make('author.email')
                            ->label('Author Email (Dot Syntax)'),
                        BooleanConstraint::make('author.has_email_authentication')
                            ->label('Author Has Email Auth (Dot Syntax)'),
                        NumberConstraint::make('author.score')
                            ->label('Author Score (Dot Syntax)'),
                        SelectConstraint::make('author.status')
                            ->label('Author Status (Dot Syntax)')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'pending' => 'Pending',
                            ]),
                        DateConstraint::make('author.email_verified_at')
                            ->label('Author Verified At (Dot Syntax)'),
                    ]),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
