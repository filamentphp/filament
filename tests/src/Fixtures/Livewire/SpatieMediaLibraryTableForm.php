<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\MediaPost;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class SpatieMediaLibraryTableForm extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public ?string $collection = 'avatars';

    public bool $allCollections = false;

    protected function getTableQuery(): Builder
    {
        return MediaPost::query();
    }

    public function table(Table $table): Table
    {
        $column = SpatieMediaLibraryImageColumn::make('media');

        if ($this->allCollections) {
            $column->allCollections();
        } else {
            $column->collection($this->collection);
        }

        return $table
            ->columns([$column]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
