<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithCheckboxColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('is_published');
});

it('can display checked state', function (): void {
    Post::factory()->create(['is_published' => true]);

    livewire(TestTableWithCheckboxColumn::class)
        ->assertSuccessful();
});

it('can display unchecked state', function (): void {
    Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithCheckboxColumn::class)
        ->assertSuccessful();
});

it('injects raw relationship state into `tooltip()`', function (): void {
    $author = User::factory()->create(['json' => ['is_active' => null]]);
    Post::factory()->create(['author_id' => $author->getKey()]);

    livewire(TestTableWithRelationshipCheckboxColumn::class)
        ->assertSuccessful()
        ->assertSee("raw-null-{$author->getKey()}", escape: false);
});

class TestTableWithCheckboxColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\CheckboxColumn::make('is_published'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithRelationshipCheckboxColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\CheckboxColumn::make('author.json.is_active')
                    ->tooltip(static fn (mixed $state, User $relatedRecord): string => 'raw-' . ($state === null ? 'null' : get_debug_type($state)) . "-{$relatedRecord->getKey()}"),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
