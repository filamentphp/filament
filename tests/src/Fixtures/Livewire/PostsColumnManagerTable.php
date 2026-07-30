<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PostsColumnManagerTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('rating')
                    ->toggleable(),
            ])
            // A live column manager applies a toggle immediately, so the test can assert its effect without an `Apply columns` step.
            ->deferColumnManager(false)
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
