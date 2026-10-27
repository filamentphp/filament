<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithTextColumnTags::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('tags');
});

it('can display multiple tags', function (): void {
    Post::factory()->create(['tags' => ['tag1', 'tag2', 'tag3']]);

    livewire(TestTableWithTextColumnTags::class)
        ->assertSuccessful();
});

it('can display empty tags', function (): void {
    Post::factory()->create(['tags' => []]);

    livewire(TestTableWithTextColumnTags::class)
        ->assertSuccessful();
});

class TestTableWithTextColumnTags extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('tags')->badge(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
