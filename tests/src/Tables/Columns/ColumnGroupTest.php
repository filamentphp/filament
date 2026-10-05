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
use Illuminate\Support\HtmlString;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithColumnGroup::class)
        ->assertSuccessful();
});

it('can render grouped columns', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithColumnGroup::class)
        ->assertCanRenderTableColumn('author.name')
        ->assertCanRenderTableColumn('author.email');
});

it('can use `HtmlString` as label', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithColumnGroupWithHtmlStringLabel::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('author.name')
        ->assertCanRenderTableColumn('author.email');
});

it('can toggle all table columns inside a column group', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithToggleableColumnGroup::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('author.name')
        ->assertCanNotRenderTableColumn('author.email')
        ->toggleAllTableColumns()
        ->assertCanRenderTableColumn('author.email')
        ->toggleAllTableColumns(false)
        ->assertCanNotRenderTableColumn('author.email');
});

class TestTableWithColumnGroup extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\ColumnGroup::make('Author', [
                    Tables\Columns\TextColumn::make('author.name'),
                    Tables\Columns\TextColumn::make('author.email'),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithColumnGroupWithHtmlStringLabel extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\ColumnGroup::make(new HtmlString('<span>Author</span>'), [
                    Tables\Columns\TextColumn::make('author.name'),
                    Tables\Columns\TextColumn::make('author.email'),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithToggleableColumnGroup extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\ColumnGroup::make('author', [
                    Tables\Columns\TextColumn::make('author.name'),
                    Tables\Columns\TextColumn::make('author.email')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
