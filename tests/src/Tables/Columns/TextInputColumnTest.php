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

    livewire(TestTableWithTextInputColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('rating');
});

it('renders showPicker onclick for date type inputs', function (): void {
    Post::factory()->count(1)->create();

    livewire(TestTableWithDateTextInputColumn::class)
        ->assertSuccessful()
        ->assertSeeHtml('showPicker');
});

it('can display different values', function (): void {
    Post::factory()->create(['rating' => 1]);
    Post::factory()->create(['rating' => 5]);
    Post::factory()->create(['rating' => 10]);

    livewire(TestTableWithTextInputColumn::class)
        ->assertSuccessful();
});

class TestTableWithDateTextInputColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\TextInputColumn::make('created_at')
                    ->type('date'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithTextInputColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\TextInputColumn::make('rating'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
