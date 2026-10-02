<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Table;

class FiltersFocusTable extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filters([
                Tables\Filters\Filter::make('is_published')
                    ->schema([
                        Toggle::make('isActive')
                            ->label('Published')
                            ->extraAttributes(['data-testid' => 'published-filter']),
                    ]),
            ])
            ->filtersTriggerAction(
                static fn (Action $action): Action => $action
                    ->extraAttributes(['data-testid' => 'filters-trigger']),
            );
    }
}
