<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;

class ReorderableColumnsTable extends PostsColumnManagerTable
{
    public bool $deferred = true;

    public bool $persist = true;

    public bool $toggleable = true;

    public bool $closeOnApply = false;

    public bool $introduced = false;

    public string $layout = 'dropdown';

    public function table(Table $table): Table
    {
        $publicationColumns = [
            TextColumn::make('rating')->toggleable($this->toggleable),
            TextColumn::make('internal')->hidden(),
            TextColumn::make('is_published')->label('Published')->toggleable($this->toggleable, isToggledHiddenByDefault: true),
        ];

        if ($this->introduced) {
            $publicationColumns[] = TextColumn::make('created_at')->toggleable();
        }

        $columns = [
            TextColumn::make('title')->toggleable($this->toggleable),
            ColumnGroup::make('Publication')->columns($publicationColumns),
            TextColumn::make('id'),
        ];

        if ($this->introduced) {
            array_unshift($columns, TextColumn::make('author.name')->toggleable());
        }

        return $table
            ->query(Post::query())
            ->columns($columns)
            ->reorderableColumns()
            ->reorderAnimationDuration(0)
            ->deferColumnManager($this->deferred)
            ->closeColumnManagerDropdownOnApply($this->closeOnApply)
            ->persistColumnsInSession($this->persist)
            ->columnManagerLayout($this->layout === 'modal' ? ColumnManagerLayout::Modal : ColumnManagerLayout::Dropdown)
            ->columnManagerWidth('2xl')
            ->paginated(false);
    }
}
