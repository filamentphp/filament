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
                Filter::make('title')
                    ->schema([
                        Select::make('value')
                            ->label('Title')
                            ->options(['known' => '<b data-testid="trusted-title-label">Known &amp; trusted</b>'])
                            ->allowHtml()
                            ->extraAttributes(['data-testid' => 'title-filter']),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(filled($data['value'] ?? null), fn (Builder $query): Builder => $query->where('title', $data['value']))),
            ], layout: FiltersLayout::AboveContent);
    }
}
