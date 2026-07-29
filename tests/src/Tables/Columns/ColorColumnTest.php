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

    livewire(TestTableWithColorColumn::class)
        ->assertSuccessful();
});

it('does not inject CSS from a malicious color value', function (): void {
    Post::factory()->create([
        'title' => 'red;position:fixed;inset:0;background-image:url(//attacker)',
    ]);

    livewire(TestTableWithColorColumn::class)
        ->assertSuccessful()
        ->assertDontSee('position:fixed', escape: false);
});

it('renders a legitimate hex color into the `background-color` style', function (): void {
    Post::factory()->create([
        'title' => '#ff0000',
    ]);

    livewire(TestTableWithColorColumn::class)
        ->assertSuccessful()
        ->assertSee('background-color: #ff0000', escape: false);
});

class TestTableWithColorColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\ColorColumn::make('title'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
