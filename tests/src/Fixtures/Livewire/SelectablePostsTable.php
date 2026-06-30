<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class SelectablePostsTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->checkIfRecordIsSelectableUsing(fn (Post $record): bool => (bool) $record->is_published)
            ->columns([
                TextColumn::make('title'),
            ])
            ->toolbarActions([
                BulkAction::make('customBulk')
                    ->action(function (Collection $records): void {
                        $this->dispatch('customBulk-called', records: $records->pluck('id')->toArray());
                    }),
                DeleteBulkAction::make('queryBulkDelete')
                    ->fetchSelectedRecords(false),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
