<?php

namespace Filament\Tests\Fixtures\Tables;

use Closure;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\ColumnManagerResetActionPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\ConfigurablePostsTable;
use Filament\Tests\Fixtures\Livewire\DefaultGroupedPostsTable;
use Filament\Tests\Fixtures\Livewire\PostsColumnManagerTable;
use Filament\Tests\Fixtures\Livewire\PostsReorderableTable;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithAboveContentCollapsibleFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithAboveContentFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithAfterContentFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithBeforeContentFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithBelowContentFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithContentGrid;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCursorPagination;
use Filament\Tests\Fixtures\Livewire\PostsTableWithDeferredLoading;
use Filament\Tests\Fixtures\Livewire\PostsTableWithHiddenFilters;
use Filament\Tests\Fixtures\Livewire\PostsTableWithModalFilters;
use Filament\Tests\Fixtures\Livewire\SelectablePostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportTesting\Testable;

/**
 * Every configuration and interaction state the default table layout is
 * verified against. Each variant is a Livewire component, an optional closure
 * that configures its table after the fixture did, and an optional closure that
 * interacts with the rendered component before its HTML is captured.
 */
class DefaultLayoutVariants
{
    /**
     * @return array<string, array{class-string, ?Closure, ?Closure}>
     */
    public static function all(): array
    {
        return [
            ...static::fixtures(),
            ...static::options(),
            ...static::interactions(),
        ];
    }

    /**
     * @return array<string, array{class-string, ?Closure, ?Closure}>
     */
    protected static function fixtures(): array
    {
        return [
            'plain' => [PostsTable::class, null, null],
            'selectable' => [SelectablePostsTable::class, null, null],
            'reorderable' => [PostsReorderableTable::class, null, null],
            'filters above content' => [PostsTableWithAboveContentFilters::class, null, null],
            'filters above content collapsible' => [PostsTableWithAboveContentCollapsibleFilters::class, null, null],
            'filters below content' => [PostsTableWithBelowContentFilters::class, null, null],
            'filters before content' => [PostsTableWithBeforeContentFilters::class, null, null],
            'filters after content' => [PostsTableWithAfterContentFilters::class, null, null],
            'filters modal' => [PostsTableWithModalFilters::class, null, null],
            'filters hidden' => [PostsTableWithHiddenFilters::class, null, null],
            'content grid' => [PostsTableWithContentGrid::class, null, null],
            'column manager' => [PostsColumnManagerTable::class, null, null],
            'deferred loading' => [PostsTableWithDeferredLoading::class, null, null],
            'grouped' => [DefaultGroupedPostsTable::class, null, null],
            'cursor pagination' => [PostsTableWithCursorPagination::class, null, null],
        ];
    }

    /**
     * @return array<string, array{class-string, ?Closure, ?Closure}>
     */
    protected static function options(): array
    {
        $noRecords = fn (Table $table): Table => $table->modifyQueryUsing(fn (Builder $query) => $query->whereRaw('0 = 1'));

        return static::configured([
            'heading and description' => fn (Table $table): Table => $table->heading('Posts')->description('Every post on the blog'),
            'custom header view' => fn (Table $table): Table => $table->header(new HtmlString('<div class="custom-header">Custom header</div>')),
            'adaptive header actions' => fn (Table $table): Table => $table->headerActionsPosition(HeaderActionsPosition::Adaptive),
            'column groups' => fn (Table $table): Table => $table->columns([
                TextColumn::make('title'),
                ColumnGroup::make('Author', [
                    TextColumn::make('author.name'),
                    TextColumn::make('author.email'),
                ])->alignCenter(),
                TextColumn::make('rating'),
            ]),
            'split and collapsible panel columns' => fn (Table $table): Table => $table->columns([
                Split::make([
                    TextColumn::make('title'),
                    TextColumn::make('rating'),
                ]),
                Panel::make([
                    TextColumn::make('content'),
                ])->collapsible(),
            ]),
            'record actions before cells' => fn (Table $table): Table => $table->recordActionsPosition(RecordActionsPosition::BeforeCells),
            'record actions before columns' => fn (Table $table): Table => $table->recordActionsPosition(RecordActionsPosition::BeforeColumns),
            'record actions after content' => fn (Table $table): Table => $table->recordActionsPosition(RecordActionsPosition::AfterContent),
            'record actions column label and alignment' => fn (Table $table): Table => $table->recordActionsColumnLabel('Actions')->recordActionsAlignment('center'),
            'record checkbox after cells' => fn (Table $table): Table => $table->recordCheckboxPosition(RecordCheckboxPosition::AfterCells),
            'select current page only' => fn (Table $table): Table => $table->selectCurrentPageOnly(),
            'max selectable records' => fn (Table $table): Table => $table->maxSelectableRecords(2),
            'content footer' => fn (Table $table): Table => $table->contentFooter(view('fixtures.table-slot')),
            'custom content view' => fn (Table $table): Table => $table->content(view('fixtures.table-slot')),
            'without records' => $noRecords,
            'custom empty state view' => fn (Table $table): Table => $noRecords($table)->emptyState(new HtmlString('<div class="custom-empty-state">Nothing here</div>')),
            'empty state heading, description and icon' => fn (Table $table): Table => $noRecords($table)
                ->emptyStateHeading('No posts yet')
                ->emptyStateDescription('Write the first one.')
                ->emptyStateIcon(Heroicon::OutlinedDocumentText),
            'polling' => fn (Table $table): Table => $table->poll('5s'),
            'striped' => fn (Table $table): Table => $table->striped(),
            'simple pagination' => fn (Table $table): Table => $table->paginationMode(PaginationMode::Simple),
            'extreme pagination links' => fn (Table $table): Table => $table->extremePaginationLinks(),
            'pagination page options' => fn (Table $table): Table => $table->paginationPageOptions([5, 10, 'all']),
            'not paginated' => fn (Table $table): Table => $table->paginated(false),
            'record url in new tab' => fn (Table $table): Table => $table->recordUrl(fn (Post $record): string => "/posts/{$record->getKey()}", shouldOpenInNewTab: true),
            'record action' => fn (Table $table): Table => $table->recordAction('edit'),
            'record classes' => fn (Table $table): Table => $table->recordClasses(fn (Post $record): string => $record->is_published ? 'is-published' : 'is-draft'),
            'default sort' => fn (Table $table): Table => $table->defaultSort('title', 'desc'),
            'groups only' => fn (Table $table): Table => $table->defaultGroup('author.name')->groupsOnly(),
            'grouping settings hidden' => fn (Table $table): Table => $table->defaultGroup('author.name')->groupingSettingsHidden(),
            'grouping settings in dropdown on desktop' => fn (Table $table): Table => $table->groupingSettingsInDropdownOnDesktop(),
            'grouping direction setting hidden' => fn (Table $table): Table => $table->groupingDirectionSettingHidden(),
            'search placeholder and search on blur' => fn (Table $table): Table => $table->searchPlaceholder('Find a post')->searchOnBlur(),
            'filters form width, columns and max height' => fn (Table $table): Table => $table->filtersFormWidth(Width::Large)->filtersFormColumns(2)->filtersFormMaxHeight('400px'),
            'filters reset action in header' => fn (Table $table): Table => $table->filtersLayout(FiltersLayout::AboveContent)->filtersResetActionPosition(FiltersResetActionPosition::Header),
            'column manager modal' => fn (Table $table): Table => $table->columnManagerLayout(ColumnManagerLayout::Modal),
            'column manager reset action in header' => fn (Table $table): Table => $table->columnManagerResetActionPosition(ColumnManagerResetActionPosition::Header),
        ]);
    }

    /**
     * @return array<string, array{class-string, ?Closure, ?Closure}>
     */
    protected static function interactions(): array
    {
        return [
            'search applied' => [PostsTable::class, null, fn (Testable $livewire) => $livewire->searchTable('Alpha')],
            'column search applied' => [PostsTable::class, null, fn (Testable $livewire) => $livewire->searchTableColumns(['content' => 'Bravo'])],
            'filter applied' => [PostsTable::class, null, fn (Testable $livewire) => $livewire->filterTable('is_published')],
            'sort applied' => [PostsTable::class, null, fn (Testable $livewire) => $livewire->sortTable('title', 'desc')],
            'grouped by the user' => [PostsTable::class, null, fn (Testable $livewire) => $livewire->set('tableGrouping', 'author.name')],
            'records selected' => [PostsTable::class, null, fn (Testable $livewire) => $livewire->selectTableRecords(Post::query()->limit(2)->pluck('id')->all())],
            'records per page changed' => [PostsTable::class, null, fn (Testable $livewire) => $livewire->set('tableRecordsPerPage', 5)],
            'second page' => [PostsTable::class, null, function (Testable $livewire): void {
                // The variant may be rendered more than once per test, so the extra records are only seeded once.
                if (Post::query()->count() === 3) {
                    foreach (range(1, 12) as $index) {
                        Post::factory()->create([
                            'author_id' => Post::query()->first()->author_id,
                            'content' => "Extra content {$index}",
                            'is_published' => true,
                            'rating' => 1,
                            'sort' => $index + 10,
                            'tags' => ['tag'],
                            'title' => "Extra post {$index}",
                        ]);
                    }
                }

                $livewire->call('gotoPage', 2, 'page');
            }],
            'all columns toggled hidden' => [PostsTable::class, null, fn (Testable $livewire) => $livewire->toggleAllTableColumns(false)],
            'reordering' => [PostsReorderableTable::class, null, fn (Testable $livewire) => $livewire->call('toggleTableReordering')],
        ];
    }

    /**
     * @param  array<string, Closure>  $configurations
     * @return array<string, array{class-string, ?Closure, ?Closure}>
     */
    protected static function configured(array $configurations): array
    {
        return array_map(
            fn (Closure $configureTable): array => [ConfigurablePostsTable::class, $configureTable, null],
            $configurations,
        );
    }
}
