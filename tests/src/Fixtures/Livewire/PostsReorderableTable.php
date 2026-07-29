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

class PostsReorderableTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public string $direction = 'asc';

    public bool $reorderable = true;

    /**
     * @var array<int | string> | null
     */
    public ?array $beforeReorderingOrder = null;

    /**
     * @var array<int | string> | null
     */
    public ?array $afterReorderingOrder = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->reorderable('sort', condition: $this->reorderable, direction: $this->direction)
            ->beforeReordering(function (array $order): void {
                $this->beforeReorderingOrder = $order;
            })
            ->afterReordering(function (array $order): void {
                $this->afterReorderingOrder = $order;
            })
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
