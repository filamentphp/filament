<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\AttachSpatieTagsBulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DetachSpatieTagsBulkAction;
use Filament\Actions\ManageSpatieTagsBulkAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

/**
 * The Spatie tags bulk actions mounted on a table whose model is not taggable. `Post` has no
 * `tags()` relationship method (its `tags` column is an unrelated array cast), so
 * `method_exists($record, 'tags')` is `false` for every record. This exercises the defensive
 * branch in each action that reports a processing failure instead of throwing a
 * `BadMethodCallException` when a record cannot receive tags.
 */
class SpatieTagsBulkActionsNonTaggableTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Post::query();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
            ])
            ->toolbarActions([
                AttachSpatieTagsBulkAction::make(),
                DetachSpatieTagsBulkAction::make(),
                ManageSpatieTagsBulkAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
