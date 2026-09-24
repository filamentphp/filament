<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Facades\FilamentView;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('striping', function (): void {
    it('defaults `isStriped()` to `false`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->isStriped())->toBeFalse();
    });

    it('can set `striped()`', function (): void {
        $table = livewire(StripedTableTestComponent::class)->instance()->getTable();

        expect($table->isStriped())->toBeTrue();
    });
});

describe('stacked on mobile', function (): void {
    it('defaults `isStackedOnMobile()` to `false`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->isStackedOnMobile())->toBeFalse();
    });
});

describe('defer loading', function (): void {
    it('defaults `isLoadingDeferred()` to `false`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->isLoadingDeferred())->toBeFalse();
    });
});

describe('polling', function (): void {
    it('returns `null` for `getPollingInterval()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getPollingInterval())->toBeNull();
    });
});

describe('heading and description', function (): void {
    it('returns `null` for `getHeading()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getHeading())->toBeNull();
    });

    it('can set `heading()`', function (): void {
        $table = livewire(HeadingTableTestComponent::class)->instance()->getTable();

        expect($table->getHeading())->toBe('Posts');
    });

    it('returns `null` for `getDescription()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getDescription())->toBeNull();
    });

    it('can set `description()`', function (): void {
        $table = livewire(HeadingTableTestComponent::class)->instance()->getTable();

        expect($table->getDescription())->toBe('All blog posts');
    });
});

describe('empty state', function (): void {
    it('returns a default `getEmptyStateHeading()`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getEmptyStateHeading())->toBeString();
        expect((string) $table->getEmptyStateHeading())->not->toBeEmpty();
    });

    it('can set `emptyStateHeading()`', function (): void {
        $table = livewire(EmptyStateTableTestComponent::class)->instance()->getTable();

        expect($table->getEmptyStateHeading())->toBe('No posts yet');
    });

    it('can set `emptyStateDescription()`', function (): void {
        $table = livewire(EmptyStateTableTestComponent::class)->instance()->getTable();

        expect($table->getEmptyStateDescription())->toBe('Create your first post.');
    });
});

describe('query string identifier', function (): void {
    it('returns `null` for `getQueryStringIdentifier()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getQueryStringIdentifier())->toBeNull();
    });

    it('can set `queryStringIdentifier()`', function (): void {
        $table = livewire(QueryStringTableTestComponent::class)->instance()->getTable();

        expect($table->getQueryStringIdentifier())->toBe('posts');
    });
});

describe('headings', function (): void {
    it('defaults `getRootHeadingLevel()` to `2`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getRootHeadingLevel())->toBe(2);
    });

    it('can set `rootHeadingLevel()`', function (): void {
        $table = livewire(HeadingLevelTableTestComponent::class)->instance()->getTable();

        expect($table->getRootHeadingLevel())->toBe(3);
    });

    it('computes heading tag from level', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getHeadingTag())->toBe('h2');
        expect($table->getHeadingTag(1))->toBe('h3');
    });

    it('clamps `getHeadingLevel()` to a minimum of `1`', function (int $rootHeadingLevel): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable()
            ->rootHeadingLevel($rootHeadingLevel);

        expect($table->getHeadingLevel())->toBe(1)
            ->and($table->getHeadingTag())->toBe('h1');
    })->with([0, -1]);
});

describe('record URL', function (): void {
    it('reports `hasCustomRecordUrl()` as `false` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->hasCustomRecordUrl())->toBeFalse();
    });

    it('reports `hasCustomRecordUrl()` as `true` when set', function (): void {
        $table = livewire(RecordUrlTableTestComponent::class)->instance()->getTable();

        expect($table->hasCustomRecordUrl())->toBeTrue();
    });
});

describe('content grid', function (): void {
    it('returns `null` for `getContentGrid()` by default', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->getContentGrid())->toBeNull();
    });
});

describe('session persistence', function (): void {
    it('can toggle all session persistence with `persistInSession()`', function (): void {
        $table = livewire(TableTestComponent::class)->instance()->getTable();

        expect($table->persistsRecordsPerPageInSession())->toBeTrue();

        $table->persistInSession(false);

        expect($table->persistsFiltersInSession())->toBeFalse()
            ->and($table->persistsSearchInSession())->toBeFalse()
            ->and($table->persistsColumnSearchesInSession())->toBeFalse()
            ->and($table->persistsSortInSession())->toBeFalse()
            ->and($table->persistsGroupInSession())->toBeFalse()
            ->and($table->persistsColumnsInSession())->toBeFalse()
            ->and($table->persistsRecordsPerPageInSession())->toBeFalse();

        $table->persistInSession();

        expect($table->persistsFiltersInSession())->toBeTrue()
            ->and($table->persistsSearchInSession())->toBeTrue()
            ->and($table->persistsColumnSearchesInSession())->toBeTrue()
            ->and($table->persistsSortInSession())->toBeTrue()
            ->and($table->persistsGroupInSession())->toBeTrue()
            ->and($table->persistsColumnsInSession())->toBeTrue()
            ->and($table->persistsRecordsPerPageInSession())->toBeTrue();
    });
});

describe('rendering', function (): void {
    it('can render the table', function (): void {
        Post::factory()->count(3)->create();

        livewire(TableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render an empty table', function (): void {
        livewire(TableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render a striped table', function (): void {
        Post::factory()->count(3)->create();

        livewire(StripedTableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render a table with heading and description', function (): void {
        Post::factory()->count(3)->create();

        livewire(HeadingTableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render a table with custom empty state', function (): void {
        livewire(EmptyStateTableTestComponent::class)
            ->assertSuccessful();
    });

    it('can render `CONTENT_BEFORE` and `CONTENT_AFTER` hooks around the table content', function (): void {
        $contentBeforeData = null;
        $contentAfterData = null;

        FilamentView::registerRenderHook(
            TablesRenderHook::CONTENT_BEFORE,
            static function (array $data) use (&$contentBeforeData): string {
                $contentBeforeData = $data;

                return '<div data-testid="table-content-before-hook"></div>';
            },
            scopes: TableTestComponent::class,
        );

        FilamentView::registerRenderHook(
            TablesRenderHook::CONTENT_AFTER,
            static function (array $data) use (&$contentAfterData): string {
                $contentAfterData = $data;

                return '<div data-testid="table-content-after-hook"></div>';
            },
            scopes: TableTestComponent::class,
        );

        Post::factory()->count(3)->create();

        $component = livewire(TableTestComponent::class)
            ->assertSuccessful();

        $html = $component->html();

        $contentBeforePosition = strpos($html, 'data-testid="table-content-before-hook"');
        $tableContentPosition = strpos($html, 'class="fi-ta-content-ctn fi-fixed-positioning-context"');
        $contentAfterPosition = strpos($html, 'data-testid="table-content-after-hook"');
        $paginationPosition = strpos($html, '<nav');

        expect($contentBeforePosition)->not->toBeFalse()
            ->and($tableContentPosition)->not->toBeFalse()
            ->and($contentAfterPosition)->not->toBeFalse()
            ->and($paginationPosition)->not->toBeFalse()
            ->and($contentBeforePosition)->toBeLessThan($tableContentPosition)
            ->and($tableContentPosition)->toBeLessThan($contentAfterPosition)
            ->and($contentAfterPosition)->toBeLessThan($paginationPosition)
            ->and($contentBeforeData)->toHaveKeys(['hasPagination', 'livewire', 'records', 'table'])
            ->and($contentBeforeData['hasPagination'])->toBeTrue()
            ->and($contentBeforeData['livewire'])->toBeInstanceOf(TableTestComponent::class)
            ->and($contentBeforeData['records'])->toBeInstanceOf(LengthAwarePaginator::class)
            ->and($contentBeforeData['table'])->toBeInstanceOf(Table::class)
            ->and($contentAfterData['hasPagination'])->toBeTrue()
            ->and($contentAfterData['livewire'])->toBe($contentBeforeData['livewire'])
            ->and($contentAfterData['records'])->toBe($contentBeforeData['records'])
            ->and($contentAfterData['table'])->toBe($contentBeforeData['table']);
    });
});

class TableTestComponent extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class StripedTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->striped();
    }
}

class HeadingTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->heading('Posts')
            ->description('All blog posts');
    }
}

class EmptyStateTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->emptyStateHeading('No posts yet')
            ->emptyStateDescription('Create your first post.');
    }
}

class QueryStringTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->queryStringIdentifier('posts');
    }
}

class HeadingLevelTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->rootHeadingLevel(3);
    }
}

class RecordUrlTableTestComponent extends TableTestComponent
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl(static fn (Post $record): string => "/posts/{$record->getKey()}");
    }
}
