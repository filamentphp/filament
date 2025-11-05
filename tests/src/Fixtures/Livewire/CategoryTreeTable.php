<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CategoryTreeTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        $table
            ->query(Category::query())
            ->recordTitleAttribute('name')
            ->reorderable('position');

        $table->tree()
            ->parentColumn('parent_id')
            ->childrenRelationship('children');

        return $table->records(fn () => Category::with('children')->whereNull('parent_id')->orderBy('position')->get());
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
