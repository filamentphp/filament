<?php

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\Filter;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomFiltersApplyAction;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomFiltersRemoveAllAction;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomFiltersTriggerAction;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can set `toggle()` as the form component', function (): void {
    $filter = Filter::make('is_published')->toggle();

    expect($filter->getFormField())->toBeInstanceOf(Toggle::class);
});

it('can set `checkbox()` as the form component', function (): void {
    $filter = Filter::make('is_published')->toggle()->checkbox();

    expect($filter->getFormField())->toBeInstanceOf(Checkbox::class);
});

it('defaults form component to `Checkbox`', function (): void {
    $filter = Filter::make('is_published');

    expect($filter->getFormField())->toBeInstanceOf(Checkbox::class);
});

it('can set a custom `formComponent()`', function (): void {
    $filter = Filter::make('is_published')->formComponent(Toggle::class);

    expect($filter->getFormField())->toBeInstanceOf(Toggle::class);
});

it('can filter records by boolean column', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can filter records by relationship', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author;

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('author', $author)
        ->assertCanSeeTableRecords($posts->where('author_id', $author->getKey()))
        ->assertCanNotSeeTableRecords($posts->where('author_id', '!=', $author->getKey()));
});

it('can persist filters in the user\'s session', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->assertCanNotSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($unpublishedPosts);
});

it('can reset filters', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($unpublishedPosts);
});

it('can remove a filter', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->removeTableFilter('is_published')
        ->assertCanSeeTableRecords($posts);
});

it('can remove all table filters', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertDontSee('Clear filters')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->removeTableFilters()
        ->assertCanSeeTableRecords($posts);
});

it('can customize the `filtersRemoveAllAction()`', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithCustomFiltersRemoveAllAction::class)
        ->filterTable('is_published')
        ->assertSee('Clear filters')
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can customize the `filtersTriggerAction()`', function (): void {
    livewire(PostsTableWithCustomFiltersTriggerAction::class)
        ->assertSee('Show filters');
});

it('can customize the `filtersApplyAction()`', function (): void {
    livewire(PostsTableWithCustomFiltersApplyAction::class)
        ->assertSee('Apply filters');
});

it('can use a custom attribute for the `SelectFilter`', function (): void {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('select_filter_attribute', false)
        ->assertCanSeeTableRecords($unpublishedPosts)
        ->filterTable('select_filter_attribute', true)
        ->assertCanNotSeeTableRecords($unpublishedPosts);
});

it('can assert a filter exists with a given configuration', function (): void {
    livewire(PostsTable::class)
        ->assertTableFilterExists('is_published', function (Filter $filter): bool {
            return $filter->getLabel() === 'Is published';
        });
});

it('can check if a filter is visible', function (): void {
    livewire(PostsTable::class)
        ->assertTableFilterVisible('is_published');
});

it('can check if a filter is hidden', function (): void {
    livewire(PostsTable::class)
        ->assertTableFilterHidden('hidden');
});

it('returns `["isActive" => false]` from `getResetState()` by default', function (): void {
    $filter = Filter::make('test');

    expect($filter->getResetState())->toBe(['isActive' => false]);
});

it('returns fluent `$this` from `toggle()`', function (): void {
    $filter = Filter::make('test');

    expect($filter->toggle())->toBe($filter);
});

it('returns fluent `$this` from `checkbox()`', function (): void {
    $filter = Filter::make('test');

    expect($filter->checkbox())->toBe($filter);
});

it('returns fluent `$this` from `formComponent()`', function (): void {
    $filter = Filter::make('test');

    expect($filter->formComponent(Toggle::class))->toBe($filter);
});

it('sets the form field label from the filter label', function (): void {
    $filter = Filter::make('test')
        ->label('My Filter');

    $field = $filter->getFormField();

    expect($field->getLabel())->toBe('My Filter');
});

// BaseFilter tests (tested via Filter, which extends BaseFilter)

describe('construction (BaseFilter)', function (): void {
    it('can be constructed with a name', function (): void {
        $filter = Filter::make('status');

        expect($filter->getName())->toBe('status');
    });

    it('throws `LogicException` when name is blank', function (): void {
        Filter::make('');
    })->throws(LogicException::class);
});

it('returns `null` from `getDefaultName()`', function (): void {
    expect(Filter::getDefaultName())->toBeNull();
});

describe('label (BaseFilter)', function (): void {
    it('auto-generates label from name', function (): void {
        $filter = Filter::make('is-published');

        expect($filter->getLabel())->toBeString()->not->toBeEmpty();
    });

    it('can set label with a `Closure`', function (): void {
        $filter = Filter::make('status')
            ->label(static fn (): string => 'Dynamic');

        expect($filter->getLabel())->toBe('Dynamic');
    });
});

describe('column span (BaseFilter)', function (): void {
    it('defaults column span to `1`', function (): void {
        $filter = Filter::make('status');

        expect($filter->getColumnSpan())->toBe(1);
    });

    it('can set `columnSpan()`', function (): void {
        $filter = Filter::make('status')
            ->columnSpan(2);

        expect($filter->getColumnSpan())->toBe(2);
    });

    it('can set `columnSpanFull()`', function (): void {
        $filter = Filter::make('status')
            ->columnSpanFull();

        expect($filter->getColumnSpan())->toBe(['default' => 'full']);
    });
});
