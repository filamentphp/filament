<?php

namespace Filament\Tests\Tables\Filters;

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

it('can render table with `TernaryFilter`', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithTernaryFilter::class)
        ->assertSuccessful();
});

it('can filter records where boolean is `true`', function (): void {
    $posts = Post::factory()->count(10)->create();

    $publishedPosts = $posts->where('is_published', true);
    $unpublishedPosts = $posts->where('is_published', false);

    livewire(TestTableWithTernaryFilter::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published', 1)
        ->assertCanSeeTableRecords($publishedPosts)
        ->assertCanNotSeeTableRecords($unpublishedPosts);
});

it('can filter records where boolean is `false`', function (): void {
    $posts = Post::factory()->count(10)->create();

    $publishedPosts = $posts->where('is_published', true);
    $unpublishedPosts = $posts->where('is_published', false);

    livewire(TestTableWithTernaryFilter::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published', 0)
        ->assertCanSeeTableRecords($unpublishedPosts)
        ->assertCanNotSeeTableRecords($publishedPosts);
});

it('can reset `TernaryFilter` to show all records', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(TestTableWithTernaryFilter::class)
        ->filterTable('is_published', 1)
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($posts);
});

class TestTableWithTernaryFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
