<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Actions\Action;
use Filament\Tables\Table;

class ListPostsWithCustomFiltersRemoveAllAction extends ListPosts
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersRemoveAllAction(
                fn (Action $action) => $action
                    ->label('Clear filters')
                    ->link(),
            );
    }
}
