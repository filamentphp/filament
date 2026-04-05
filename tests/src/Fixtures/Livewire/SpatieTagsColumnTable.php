<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class SpatieTagsColumnTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public ?string $tagType = null;

    protected function getTableQuery(): Builder
    {
        return Article::query();
    }

    public function table(Table $table): Table
    {
        $column = SpatieTagsColumn::make('tags');

        if ($this->tagType !== null) {
            $column->type($this->tagType);
        }

        return $table
            ->columns([$column]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
