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
use Illuminate\Support\HtmlString;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithToggleColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('is_published');
});

it('can display true state', function (): void {
    Post::factory()->create(['is_published' => true]);

    livewire(TestTableWithToggleColumn::class)
        ->assertSuccessful();
});

it('can display false state', function (): void {
    Post::factory()->create(['is_published' => false]);

    livewire(TestTableWithToggleColumn::class)
        ->assertSuccessful();
});

it('injects boolean relationship state into `tooltip()`', function (mixed $state, string $expectedState): void {
    $author = User::factory()->create(['json' => ['is_active' => $state]]);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);

    livewire(TestTableWithRelationshipToggleColumn::class)
        ->assertSuccessful()
        ->assertSee("bool-{$expectedState}-{$author->getKey()}-{$post->getKey()}", escape: false);
})->with([
    'null' => [null, 'false'],
    'false' => [false, 'false'],
    'true' => [true, 'true'],
    'zero' => [0, 'false'],
    'one' => [1, 'true'],
]);

it('renders one error-aware toggle tooltip with or without a configured hint', function (bool $hasTooltip): void {
    $post = Post::factory()->create();
    $column = livewire(TestTableWithToggleColumn::class)->instance()->getTable()->getColumn('is_published');
    $html = $column->record($post)->tooltip($hasTooltip ? new HtmlString('<strong>Publish</strong>') : null)->toEmbeddedHtml();

    expect(substr_count($html, 'x-tooltip='))->toBe(1)
        ->and($html)->toContain('error === undefined ? ' . ($hasTooltip ? '{' : 'false'))
        ->toContain('content: error')
        ->toContain('allowHTML: false');
})->with([false, true]);

class TestTableWithToggleColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\ToggleColumn::make('is_published'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithRelationshipToggleColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\ToggleColumn::make('author.json.is_active')
                    ->tooltip(static fn (mixed $state, User $relatedRecord, Post $record): string => get_debug_type($state) . '-' . ($state ? 'true' : 'false') . "-{$relatedRecord->getKey()}-{$record->getKey()}"),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
