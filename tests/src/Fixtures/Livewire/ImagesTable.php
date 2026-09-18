<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Image;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ImagesTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Image::query())
            ->columns([
                Tables\Columns\TextColumn::make('url'),
                Tables\Columns\TextColumn::make('imageable.team.name')
                    ->label('Imageable Team (MorphTo -> BelongsTo)'),
                Tables\Columns\TextColumn::make('imageable.company.name')
                    ->label('Imageable Company (MorphTo -> BelongsTo / BelongsToThrough)'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
