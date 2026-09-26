---
title: Testing exports
---

## Introduction

You can test [exports](../actions/export) at two levels: call `test()` on your exporter to check row values, and `ExportAction::fake()` to check an action's form, column selection, options, and query without running export jobs.

## Testing row values

Call `test()` on your exporter to create a `Filament\Actions\Testing\TestableExport`, then call `export()` with an Eloquent record:

```php
use App\Filament\Exports\ProductExporter;
use App\Models\Product;

it('exports product details', function () {
    $product = Product::factory()->make([
        'name' => 'Oak desk',
        'sku' => 'DESK-01',
    ]);

    $row = ProductExporter::test(columnMap: [
        'name' => 'Name',
        'sku' => 'SKU',
    ])->export($product);

    expect($row)->toBe(['Oak desk', 'DESK-01']);
});
```

The helper calls your real exporter, including state and formatting callbacks, and returns its array unchanged. The standard exporter returns positional values in `columnMap` order, not values keyed by column names or labels. Exceptions propagate to your test.

Omitting `columnMap` or passing `null` selects visible, default-enabled columns with their labels in declaration order. An explicit map selects exactly those columns, including hidden or default-disabled columns; `[]` selects none. This does not reproduce action-specific selection such as `columnMapping(false)` or table column visibility. Test those through the action.

You may pass an `Export` model as the `export` argument when callbacks need export context. Otherwise, the helper uses an unsaved model. It does not save either model or authenticate as the export's owner. Set up authentication yourself when needed, and pass the map and options explicitly rather than relying on values stored on the export model.

### Testing formatting and options

For example, a `PostExporter` could calculate and format a score using an option:

```php
use App\Models\Post;
use Filament\Actions\Exports\ExportColumn;

ExportColumn::make('score')
    ->state(static fn (Post $record, array $options): int => $record->rating * ($options['multiplier'] ?? 1))
    ->formatStateUsing(static fn (int $state): string => "{$state} points")
```

Pass `options` to check how they change the exported value:

```php
use App\Filament\Exports\PostExporter;
use App\Models\Post;

it('exports calculated scores', function () {
    $post = Post::factory()->make(['rating' => 3]);
    $columnMap = ['score' => 'Score'];

    expect(PostExporter::test($columnMap, options: ['multiplier' => 2])->export($post))
        ->toBe(['6 points'])
        ->and(PostExporter::test($columnMap, options: ['multiplier' => 5])->export($post))
        ->toBe(['15 points']);
});
```

Options pass through unchanged: the helper does not apply options-form defaults or validation. If the form defaults `multiplier` to `2`, pass it explicitly; omitting it above uses the callback's fallback of `1`.

### Preparing relationships and aggregates

The helper does not call `modifyQuery()`, eager-load relationships, or prepare aggregates from columns such as `ExportColumn::make('posts_count')->counts('posts')`. Prepare the record yourself, matching any relationship scopes used by the exporter. For an `AuthorExporter` with `team.name`, `posts_count`, and `posts_sum_rating` columns, an author on the `Editorial` team with two posts rated `3` and `8` could be tested with:

```php
use App\Filament\Exports\AuthorExporter;

$author->load('team')->loadCount('posts')->loadSum('posts', 'rating');

expect(AuthorExporter::test()->export($author))
    ->toBe(['Editorial', '2', '11']);
```

You can reuse a helper for different records. If you change a saved record or load more attributes after exporting it, call `test()` again to create a fresh helper, since columns cache record state. Callbacks and Eloquent lazy loading may still issue queries.

## Testing export actions

Call `ExportAction::fake()` before calling the action, then use the returned fake to assert that an export was dispatched:

```php
use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\User;
use Filament\Actions\ExportAction;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

it('starts a product export', function () {
    $this->actingAs(User::factory()->create());
    $exports = ExportAction::fake();

    livewire(ListProducts::class)
        ->callAction(TestAction::make('export')->table())
        ->assertHasNoFormErrors();

    $exports->assertDispatched(ProductExporter::class)
        ->assertDispatchedTimes(ProductExporter::class);
});
```

This assumes an action named `export` in the table header. For an action outside a table, use `callAction('export')`. Set up the page's normal panel, tenant, and authentication context in these tests.

`assertDispatchedTimes()` checks the exact count, defaulting to `1`. Use `assertNothingDispatched()` after a rejected submission. `ExportBulkAction::fake()` captures both action types too. Calling either `fake()` method resets the recorded exports, so call it once before actions you want to assert together.

Use `assertNotDispatched(ProductExporter::class)` to check that a particular exporter was not dispatched, while allowing other exporters. It accepts the same optional callback as `assertDispatched()` and fails if any export matches:

```php
use App\Filament\Exports\ProductExporter;
use Filament\Actions\Exports\Models\Export;

$exports->assertNotDispatched(ProductExporter::class, static fn (Export $export): bool => $export->user_id === $otherUser->getKey());
```

### Inspecting the query and submitted configuration

Pass a callback to `assertDispatched()` to inspect the saved `Export`, a fresh query builder, column map, merged options, formats, and selected record IDs, in that order. Return `true` for a match or `false` to try another export. You may use Pest or PHPUnit assertions inside the callback.

For a table with a searchable `name` column and a `ProductExporter` with only a `name` export column:

```php
use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Builder;

use function Pest\Livewire\livewire;

$matchingProduct = Product::factory()->create(['name' => 'Oak desk']);
Product::factory()->create(['name' => 'Walnut chair']);
$exports = ExportAction::fake();

livewire(ListProducts::class)
    ->searchTable('Oak')
    ->callAction(TestAction::make('export')->table(), data: [
        'columnMap' => [
            'name' => ['isEnabled' => true, 'label' => 'Product name'],
        ],
    ])
    ->assertHasNoFormErrors();

$exports->assertDispatched(ProductExporter::class, function (Export $export, Builder $query, array $columnMap) use ($matchingProduct): bool {
    expect($query->pluck('id')->all())->toBe([$matchingProduct->getKey()])
        ->and($columnMap)->toBe(['name' => 'Product name'])
        ->and($export->total_rows)->toBe(1);

    return true;
});
```

A partial `columnMap` in `callAction(data: ...)` does not replace the whole mapping: other default-enabled columns remain selected unless you set their `isEnabled` to `false`. Form defaults and validation run normally, and submitted options override keys from the action's static `options()`.

The query includes table filters, search, ordering, exporter `modifyQuery()`, and action `modifyQueryUsing()`. The fake deserializes a fresh builder for each callback without executing it or preparing relationships and aggregates. Calling `pluck()` executes it against your test database; changing the builder does not affect later assertions. The action itself still queries and counts records during preparation.

### Inspecting bulk selections

Selected IDs are captured separately from the base query. For a table and exporter that include all three products:

```php
use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Builder;

use function Pest\Livewire\livewire;

$products = Product::factory()->count(3)->create();
$exports = ExportBulkAction::fake();

livewire(ListProducts::class)
    ->selectTableRecords([$products[1]->getKey()])
    ->callAction(TestAction::make('export')->table()->bulk())
    ->assertHasNoFormErrors();

$exports->assertDispatched(ProductExporter::class, function (
    Export $export,
    Builder $query,
    array $columnMap,
    array $options,
    array $formats,
    ?array $records,
) use ($products): bool {
    expect($query->reorder('id')->pluck('id')->all())->toBe($products->modelKeys())
        ->and($records)->toBe([$products[1]->getKey()])
        ->and($export->total_rows)->toBe(1);

    return true;
});
```

Do not assume the query is restricted to the selection. Non-bulk exports pass `null` for the selected IDs. Scope the query to records the user may access; the fake does not add [per-record authorization](../actions/export#per-record-authorization).

### Understanding the fake's boundaries

The action still persists an `Export`, enforces row limits, and deletes its existing export directory. Configuration callbacks, action hooks, and model events still run. Use your normal test database and Laravel's `Storage::fake()` on the configured export disk to isolate storage cleanup. Started notifications follow the action's queue configuration; unrelated jobs and listeners are not faked.

The fake only intercepts dispatch through Filament's `ExportDispatcher`. If your custom action dispatches jobs directly instead, use Laravel's bus or queue fakes to test that workflow.

The fake does not process rows, generate files, or complete exports and send completion notifications. Keep separate unfaked integration tests for worker processing, CSV/XLSX contents, and downloads. Use your exporter's `test()` method for row values, including any [formula injection protection](../actions/export#csv-formula-injection) configured on your columns.
