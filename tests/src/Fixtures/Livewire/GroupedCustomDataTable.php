<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GroupedCustomDataTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => [
                1 => [
                    'title' => 'First item',
                    'status' => 'active',
                ],
                2 => [
                    'title' => 'Second item',
                    'status' => 'inactive',
                ],
                3 => [
                    'title' => 'Third item',
                    'status' => 'active',
                ],
                4 => [
                    'title' => 'Fourth item',
                    'status' => 'inactive',
                ],
                5 => [
                    'title' => 'Fifth item',
                    'status' => 'active',
                ],
            ])
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('status'),
            ])
            ->groups([
                Tables\Grouping\Group::make('status'),
            ])
            ->toolbarActions([
                BulkAction::make('delete')
                    ->action(fn () => null),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
