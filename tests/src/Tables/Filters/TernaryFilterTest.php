<?php

namespace Filament\Tests\Tables\Filters;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can set `trueLabel()` and get with `getTrueLabel()`', function (): void {
    expect(TernaryFilter::make('is_published')->trueLabel('Yes')->getTrueLabel())->toBe('Yes');
});

it('can set `falseLabel()` and get with `getFalseLabel()`', function (): void {
    expect(TernaryFilter::make('is_published')->falseLabel('No')->getFalseLabel())->toBe('No');
});

it('can set `trueLabel()` with a closure and get with `getTrueLabel()`', function (): void {
    expect(TernaryFilter::make('is_published')->trueLabel(static fn (): string => 'Yes')->getTrueLabel())->toBe('Yes');
});

it('can set `falseLabel()` with a closure and get with `getFalseLabel()`', function (): void {
    expect(TernaryFilter::make('is_published')->falseLabel(static fn (): string => 'No')->getFalseLabel())->toBe('No');
});

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

describe('`nullable()` filtering', function (): void {
    it('can filter records where attribute is not null', function (): void {
        Post::factory()->count(3)->create(['content' => 'Some content']);
        Post::factory()->count(2)->create(['content' => null]);

        livewire(TestTableWithNullableTernaryFilter::class)
            ->assertCanSeeTableRecords(Post::all())
            ->filterTable('content', 1)
            ->assertCanSeeTableRecords(Post::whereNotNull('content')->get())
            ->assertCanNotSeeTableRecords(Post::whereNull('content')->get());
    });

    it('can filter records where attribute is null', function (): void {
        Post::factory()->count(3)->create(['content' => 'Some content']);
        Post::factory()->count(2)->create(['content' => null]);

        livewire(TestTableWithNullableTernaryFilter::class)
            ->filterTable('content', 0)
            ->assertCanSeeTableRecords(Post::whereNull('content')->get())
            ->assertCanNotSeeTableRecords(Post::whereNotNull('content')->get());
    });
});

describe('`getDefaultState()` logic', function (): void {
    it('converts boolean `true` default to `1`', function (): void {
        $filter = TernaryFilter::make('is_published')
            ->default(true);

        expect($filter->getDefaultState())->toBe(1);
    });

    it('converts boolean `false` default to `0`', function (): void {
        $filter = TernaryFilter::make('is_published')
            ->default(false);

        expect($filter->getDefaultState())->toBe(0);
    });

    it('passes through non-boolean default unchanged', function (): void {
        $filter = TernaryFilter::make('is_published')
            ->default(null);

        expect($filter->getDefaultState())->toBeNull();
    });
});

describe('`getFormField()`', function (): void {
    it('returns a `Select` component', function (): void {
        $filter = TernaryFilter::make('is_published');

        $field = $filter->getFormField();

        expect($field)->toBeInstanceOf(Select::class);
    });
});

class TestTableWithNullableTernaryFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\TextColumn::make('content'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('content')
                    ->nullable(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

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
