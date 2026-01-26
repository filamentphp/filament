<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CustomDataTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                    'slug' => 'first-item',
                    'is_featured' => true,
                ],
                2 => [
                    'title' => 'Second item',
                    'slug' => 'second-item',
                    'is_featured' => false,
                ],
                3 => [
                    'title' => 'Third item',
                    'slug' => 'third-item',
                    'is_featured' => true,
                ],
            ])
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('slug'),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('formatted_state')
                    ->formatStateUsing(fn () => 'formatted state'),
                TextColumn::make('extra_attributes')
                    ->extraAttributes([
                        'class' => 'text-danger-500',
                    ]),
                TextColumn::make('with_description')
                    ->description('description below')
                    ->description('description above', 'above'),

                Tables\Columns\SelectColumn::make('with_options')
                    ->options([
                        'red' => 'Red',
                        'blue' => 'Blue',
                    ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
