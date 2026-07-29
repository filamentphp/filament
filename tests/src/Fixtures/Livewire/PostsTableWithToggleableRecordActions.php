<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PostsTableWithToggleableRecordActions extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public bool $hasVisibleRecordActions = true;

    public ?int $visibleRecordActionForKey = null;

    public function mount(bool $hasVisibleRecordActions = true, ?int $visibleRecordActionForKey = null): void
    {
        $this->hasVisibleRecordActions = $hasVisibleRecordActions;
        $this->visibleRecordActionForKey = $visibleRecordActionForKey;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->recordActions([
                Action::make('test')
                    ->visible(fn (Post $record): bool => $this->hasVisibleRecordActions
                        && (($this->visibleRecordActionForKey === null) || ($record->getKey() === $this->visibleRecordActionForKey))),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
