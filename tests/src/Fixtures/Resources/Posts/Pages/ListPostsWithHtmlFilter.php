<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Forms\Components\Select;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListPostsWithHtmlFilter extends ListPosts
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filters([
                Filter::make('rating')
                    ->schema([
                        Select::make('value')
                            ->label('Rating')
                            ->options(['5' => '<b data-testid="trusted-rating-label">Five &amp; trusted</b>'])
                            ->allowHtml()
                            ->extraAttributes(['data-testid' => 'rating-filter']),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(filled($data['value'] ?? null), fn (Builder $query): Builder => $query->where('rating', $data['value']))),
            ], layout: FiltersLayout::AboveContent);
    }
}
