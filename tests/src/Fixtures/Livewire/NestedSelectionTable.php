<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;

class NestedSelectionTable extends PostsColumnManagerTable
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query()->where('is_published', false))
            ->columns([TextColumn::make('title')])
            ->extraAttributes(['data-testid' => 'nested-selection-table'])
            ->selectable()
            ->paginated(false);
    }
}
