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

    livewire(TestTableWithBooleanColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('is_published');
});

it('can display true state', function (): void {
    Post::factory()->create(['is_published' => true]);

    livewire(TestTableWithBooleanColumn::class)
        ->assertSuccessful();
});

it('can display false state', function (): void {
    Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithBooleanColumn::class)
        ->assertSuccessful();
});

class TestTableWithBooleanColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\BooleanColumn::make('is_published'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
