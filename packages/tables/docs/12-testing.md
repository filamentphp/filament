---
title: Testing
---

## Overview

All examples in this guide will be written using [Pest](https://pestphp.com). To use Pest's Livewire plugin for testing, you can follow the installation instructions in the Pest documentation on plugins: [Livewire plugin for Pest](https://pestphp.com/docs/plugins#livewire). However, you can easily adapt this to PHPUnit.

Since the Table Builder works on Livewire components, you can use the [Livewire testing helpers](https://livewire.laravel.com/docs/testing). However, we have many custom testing helpers that you can use for tables:

## Render

To ensure a table component renders, use the `assertSuccessful()` Livewire helper:

```php
use function Pest\Livewire\livewire;

it('can render page', function () {
    livewire(ListPosts::class)->assertSuccessful();
});
```

To test which records are shown, you can use `assertCanSeeTableRecords()`, `assertCanNotSeeTableRecords()` and `assertCountTableRecords()`:

```php
use function Pest\Livewire\livewire;

it('cannot display trashed posts by default', function () {
    $posts = Post::factory()->count(4)->create();
    $trashedPosts = Post::factory()->trashed()->count(6)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts)
        ->assertCanNotSeeTableRecords($trashedPosts)
        ->assertCountTableRecords(4);
});
```

> If your table uses pagination, `assertCanSeeTableRecords()` will only check for records on the first page. To switch page, call `call('gotoPage', 2)`.

> If your table uses `deferLoading()`, you should call `loadTable()` before `assertCanSeeTableRecords()`.

## Columns

To ensure that a certain column is rendered, pass the column name to `assertCanRenderTableColumn()`:

```php
use function Pest\Livewire\livewire;

it('can render post titles', function () {
    Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanRenderTableColumn('title');
});
```

This helper will get the HTML for this column, and check that it is present in the table.

For testing that a column is not rendered, you can use `assertCanNotRenderTableColumn()`:

```php
use function Pest\Livewire\livewire;

it('can not render post comments', function () {
    Post::factory()->count(10)->create()

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanNotRenderTableColumn('comments');
});
```

This helper will assert that the HTML for this column is not shown by default in the present table.

### Sorting

To sort table records, you can call `sortTable()`, passing the name of the column to sort by. You can use `'desc'` in the second parameter of `sortTable()` to reverse the sorting direction.

Once the table is sorted, you can ensure that the table records are rendered in order using `assertCanSeeTableRecords()` with the `inOrder` parameter:

```php
use function Pest\Livewire\livewire;

it('can sort posts by title', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($posts->sortBy('title'), inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('title'), inOrder: true);
});
```

### Searching

To search the table, call the `searchTable()` method with your search query.

You can then use `assertCanSeeTableRecords()` to check your filtered table records, and use `assertCanNotSeeTableRecords()` to assert that some records are no longer in the table:

```php
use function Pest\Livewire\livewire;

it('can search posts by title', function () {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(PostResource\Pages\ListPosts::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});
```

To search individual columns, you can pass an array of searches to `searchTableColumns()`:

```php
use function Pest\Livewire\livewire;

it('can search posts by title column', function () {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(PostResource\Pages\ListPosts::class)
        ->searchTableColumns(['title' => $title])
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});
```

### State

To assert that a certain column has a state or does not have a state for a record you can use `assertTableColumnStateSet()` and `assertTableColumnStateNotSet()`:

```php
use function Pest\Livewire\livewire;

it('can get post author names', function () {
    $posts = Post::factory()->count(10)->create();

    $post = $posts->first();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableColumnStateSet('author.name', $post->author->name, record: $post)
        ->assertTableColumnStateNotSet('author.name', 'Anonymous', record: $post);
});
```

To assert that a certain column has a formatted state or does not have a formatted state for a record you can use `assertTableColumnFormattedStateSet()` and `assertTableColumnFormattedStateNotSet()`:

```php
use function Pest\Livewire\livewire;

it('can get post author names', function () {
    $post = Post::factory(['name' => 'John Smith'])->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableColumnFormattedStateSet('author.name', 'Smith, John', record: $post)
        ->assertTableColumnFormattedStateNotSet('author.name', $post->author->name, record: $post);
});
```

### Existence

To ensure that a column exists, you can use the `assertTableColumnExists()` method:

```php
use function Pest\Livewire\livewire;

it('has an author column', function () {
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableColumnExists('author');
});
```

You may pass a function as an additional argument in order to assert that a column passes a given "truth test". This is useful for asserting that a column has a specific configuration. You can also pass in a record as the third parameter, which is useful if your check is dependant on which table row is being rendered:

```php
use function Pest\Livewire\livewire;
use Filament\Tables\Columns\TextColumn;

it('has an author column', function () {
    $post = Post::factory()->create();
    
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableColumnExists('author', function (TextColumn $column): bool {
            return $column->getDescriptionBelow() === $post->subtitle;
        }, $post);
});
```

### Authorization

To ensure that a particular user cannot see a column, you can use the `assertTableColumnVisible()` and `assertTableColumnHidden()` methods:

```php
use function Pest\Livewire\livewire;

it('shows the correct columns', function () {
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableColumnVisible('created_at')
        ->assertTableColumnHidden('author');
});
```

### Descriptions

To ensure a column has the correct description above or below you can use the `assertTableColumnHasDescription()` and `assertTableColumnDoesNotHaveDescription()` methods:

```php
use function Pest\Livewire\livewire;

it('has the correct descriptions above and below author', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasDescription('author', 'Author! ↓↓↓', $post, 'above')
        ->assertTableColumnHasDescription('author', 'Author! ↑↑↑', $post)
        ->assertTableColumnDoesNotHaveDescription('author', 'Author! ↑↑↑', $post, 'above')
        ->assertTableColumnDoesNotHaveDescription('author', 'Author! ↓↓↓', $post);
});
```

### Extra Attributes

To ensure that a column has the correct extra attributes, you can use the `assertTableColumnHasExtraAttributes()` and `assertTableColumnDoesNotHaveExtraAttributes()` methods:

```php
use function Pest\Livewire\livewire;

it('displays author in red', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasExtraAttributes('author', ['class' => 'text-danger-500'], $post)
        ->assertTableColumnDoesNotHaveExtraAttributes('author', ['class' => 'text-primary-500'], $post);
});
```

### Select Columns

If you have a select column, you can ensure it has the correct options with `assertTableSelectColumnHasOptions()` and `assertTableSelectColumnDoesNotHaveOptions()`:

```php
use function Pest\Livewire\livewire;

it('has the correct statuses', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableSelectColumnHasOptions('status', ['unpublished' => 'Unpublished', 'published' => 'Published'], $post)
        ->assertTableSelectColumnDoesNotHaveOptions('status', ['archived' => 'Archived'], $post);
});
```

## Filters

To filter the table records, you can use the `filterTable()` method, along with `assertCanSeeTableRecords()` and `assertCanNotSeeTableRecords()`:

```php
use function Pest\Livewire\livewire;

it('can filter posts by `is_published`', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});
```

For a simple filter, this will just enable the filter.

If you'd like to set the value of a `SelectFilter` or `TernaryFilter`, pass the value as a second argument:

```php
use function Pest\Livewire\livewire;

it('can filter posts by `author_id`', function () {
    $posts = Post::factory()->count(10)->create();

    $authorId = $posts->first()->author_id;

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('author_id', $authorId)
        ->assertCanSeeTableRecords($posts->where('author_id', $authorId))
        ->assertCanNotSeeTableRecords($posts->where('author_id', '!=', $authorId));
});
```

### Resetting filters

To reset all filters to their original state, call `resetTableFilters()`:

```php
use function Pest\Livewire\livewire;

it('can reset table filters', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->resetTableFilters();
});
```

### Removing Filters

To remove a single filter you can use `removeTableFilter()`:

```php
use function Pest\Livewire\livewire;

it('filters list by published', function () {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false)->get();

    livewire(PostsTable::class)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->removeTableFilter('is_published')
        ->assertCanSeeTableRecords($posts);
});
```

To remove all filters you can use `removeTableFilters()`:

```php
use function Pest\Livewire\livewire;

it('can remove all table filters', function () {
    $posts = Post::factory()->count(10)->forAuthor()->create();

    $unpublishedPosts = $posts
        ->where('is_published', false)
        ->where('author_id', $posts->first()->author->getKey());

    livewire(PostsTable::class)
        ->filterTable('is_published')
        ->filterTable('author', $author)
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->removeTableFilters()
        ->assertCanSeeTableRecords($posts);
});
```

### Hidden filters

To ensure that a particular user cannot see a filter, you can use the `assertTableFilterVisible()` and `assertTableFilterHidden()` methods:

```php
use function Pest\Livewire\livewire;

it('shows the correct filters', function () {
    livewire(PostsTable::class)
        ->assertTableFilterVisible('created_at')
        ->assertTableFilterHidden('author');
```

### Filter existence

To ensure that a filter exists, you can use the `assertTableFilterExists()` method:

```php
use function Pest\Livewire\livewire;

it('has an author filter', function () {
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableFilterExists('author');
});
```

You may pass a function as an additional argument in order to assert that a filter passes a given "truth test". This is useful for asserting that a filter has a specific configuration:

```php
use function Pest\Livewire\livewire;
use Filament\Tables\Filters\SelectFilter;

it('has an author filter', function () {    
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableFilterExists('author', function (SelectFilter $column): bool {
            return $column->getLabel() === 'Select author';
        });
});
```

## Actions

### Calling actions

You can call an action by passing its name or class to `callTableAction()`:

```php
use function Pest\Livewire\livewire;

it('can delete posts', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableAction(DeleteAction::class, $post);

    $this->assertModelMissing($post);
});
```

This example assumes that you have a `DeleteAction` on your table. If you have a custom `Action::make('reorder')`, you may use `callTableAction('reorder')`.

For column actions, you may do the same, using `callTableColumnAction()`:

```php
use function Pest\Livewire\livewire;

it('can copy posts', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableColumnAction('copy', $post);

    $this->assertDatabaseCount((new Post)->getTable(), 2);
});
```

For bulk actions, you may do the same, passing in multiple records to execute the bulk action against with `callTableBulkAction()`:

```php
use function Pest\Livewire\livewire;

it('can bulk delete posts', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableBulkAction(DeleteBulkAction::class, $posts);

    foreach ($posts as $post) {
        $this->assertModelMissing($post);
    }
});
```

To pass an array of data into an action, use the `data` parameter:

```php
use function Pest\Livewire\livewire;

it('can edit posts', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableAction(EditAction::class, $post, data: [
            'title' => $title = fake()->words(asText: true),
        ])
        ->assertHasNoTableActionErrors();

    expect($post->refresh())
        ->title->toBe($title);
});
```

### Execution

To check if an action or bulk action has been halted, you can use `assertTableActionHalted()` / `assertTableBulkActionHalted()`:

```php
use function Pest\Livewire\livewire;

it('will halt delete if post is flagged', function () {
    $posts= Post::factory()->count(2)->flagged()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableAction('delete', $posts->first())
        ->callTableBulkAction('delete', $posts)
        ->assertTableActionHalted('delete')
        ->assertTableBulkActionHalted('delete');

    $this->assertModelExists($post);
});
```

### Errors

`assertHasNoTableActionErrors()` is used to assert that no validation errors occurred when submitting the action form.

To check if a validation error has occurred with the data, use `assertHasTableActionErrors()`, similar to `assertHasErrors()` in Livewire:

```php
use function Pest\Livewire\livewire;

it('can validate edited post data', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableAction(EditAction::class, $post, data: [
            'title' => null,
        ])
        ->assertHasTableActionErrors(['title' => ['required']]);
});
```

For bulk actions these methods are called `assertHasTableBulkActionErrors()` and `assertHasNoTableBulkActionErrors()`.

### Pre-filled data

To check if an action or bulk action is pre-filled with data, you can use the `assertTableActionDataSet()` or `assertTableBulkActionDataSet()` method:

```php
use function Pest\Livewire\livewire;

it('can load existing post data for editing', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->mountTableAction(EditAction::class, $post)
        ->assertTableActionDataSet([
            'title' => $post->title,
        ])
        ->setTableActionData([
            'title' => $title = fake()->words(asText: true),
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($post->refresh())
        ->title->toBe($title);
});
```

You may also find it useful to pass a function to the `assertTableActionDataSet()` and `assertTableBulkActionDataSet()` methods, which allow you to access the form `$state` and perform additional assertions:

```php
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

it('can automatically generate a slug from the title without any spaces', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->mountTableAction(EditAction::class, $post)
        ->assertTableActionDataSet(function (array $state) use ($post): array {
            expect($state['slug'])
                ->not->toContain(' ');
                
            return [
                'slug' => Str::slug($post->title),
            ];
        });
});
```

### Action state

To ensure that an action or bulk action exists or doesn't in a table, you can use the `assertTableActionExists()` / `assertTableActionDoesNotExist()` or  `assertTableBulkActionExists()` / `assertTableBulkActionDoesNotExist()` method:

```php
use function Pest\Livewire\livewire;

it('can publish but not unpublish posts', function () {
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionExists('publish')
        ->assertTableActionDoesNotExist('unpublish')
        ->assertTableBulkActionExists('publish')
        ->assertTableBulkActionDoesNotExist('unpublish');
});
```

To ensure different sets of actions exist in the correct order, you can use the various "InOrder" assertions

```php
use function Pest\Livewire\livewire;

it('has all actions in expected order', function () {
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionsExistInOrder(['edit', 'delete'])
        ->assertTableBulkActionsExistInOrder(['restore', 'forceDelete'])
        ->assertTableHeaderActionsExistInOrder(['create', 'attach'])
        ->assertTableEmptyStateActionsExistInOrder(['create', 'toggle-trashed-filter'])
});
```

To ensure that an action or bulk action is enabled or disabled for a user, you can use the `assertTableActionEnabled()` / `assertTableActionDisabled()` or `assertTableBulkActionEnabled()` / `assertTableBulkActionDisabled()` methods:

```php
use function Pest\Livewire\livewire;

it('can not publish, but can delete posts', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionDisabled('publish', $post)
        ->assertTableActionEnabled('delete', $post)
        ->assertTableBulkActionDisabled('publish')
        ->assertTableBulkActionEnabled('delete');
});
```


To ensure that an action or bulk action is visible or hidden for a user, you can use the `assertTableActionVisible()` / `assertTableActionHidden()` or `assertTableBulkActionVisible()` / `assertTableBulkActionHidden()` methods:

```php
use function Pest\Livewire\livewire;

it('can not publish, but can delete posts', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionHidden('publish', $post)
        ->assertTableActionVisible('delete', $post)
        ->assertTableBulkActionHidden('publish')
        ->assertTableBulkActionVisible('delete');
});
```

### Button Style

To ensure an action or bulk action has the correct label, you can use `assertTableActionHasLabel()` / `assertTableBulkActionHasLabel()` and `assertTableActionDoesNotHaveLabel()` / `assertTableBulkActionDoesNotHaveLabel()`:

```php
use function Pest\Livewire\livewire;

it('delete actions have correct labels', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionHasLabel('delete', 'Archive Post', $post)
        ->assertTableActionDoesNotHaveLabel('delete', 'Delete', $post);
        ->assertTableBulkActionHasLabel('delete', 'Archive Post', $post)
        ->assertTableBulkActionDoesNotHaveLabel('delete', 'Delete', $post);
});
```

To ensure an action or bulk action's button is showing the correct icon, you can use `assertTableActionHasIcon()` / `assertTableBulkActionHasIcon()` or `assertTableActionDoesNotHaveIcon()` / `assertTableBulkActionDoesNotHaveIcon()`:

```php
use function Pest\Livewire\livewire;

it('delete actions have correct icons', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionHasIcon('delete', 'heroicon-m-archive-box', $post)
        ->assertTableActionDoesNotHaveIcon('delete', 'heroicon-m-trash', $post);
        ->assertTableBulkActionHasIcon('delete', 'heroicon-m-archive-box', $post)
        ->assertTableBulkActionDoesNotHaveIcon('delete', 'heroicon-m-trash', $post);
});
```

To ensure that an action or bulk action's button is displaying the right color, you can use `assertTableActionHasColor()` / `assertTableBulkActionHasColor()` or `assertTableActionDoesNotHaveColor()` / `assertTableBulkActionDoesNotHaveColor()`:

```php
use function Pest\Livewire\livewire;

it('delete actions have correct colors', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionHasColor('delete', 'warning', $post)
        ->assertTableActionDoesNotHaveColor('delete', 'danger', $post);
        ->assertTableBulkActionHasColor('delete', 'warning', $post)
        ->assertTableBulkActionDoesNotHaveColor('delete', 'danger', $post);
});
```

### URL

To ensure an action or bulk action has the correct URL traits, you can use `assertTableActionHasUrl()`, `assertTableActionDoesNotHaveUrl()`, `assertTableActionShouldOpenUrlInNewTab()`, and `assertTableActionShouldNotOpenUrlInNewTab()`:

```php
use function Pest\Livewire\livewire;

it('links to the correct Filament sites', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionHasUrl('filament', 'https://filamentphp.com/', $post)
        ->assertTableActionDoesNotHaveUrl('filament', 'https://github.com/filamentphp/filament', $post)
        ->assertTableActionShouldOpenUrlInNewTab('filament', $post)
        ->assertTableActionShouldNotOpenUrlInNewTab('github', $post);
});
```

## Summaries

To test that a summary calculation is working, you may use the `assertTableColumnSummarySet()` method:

```php
use function Pest\Livewire\livewire;

it('can average values in a column', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts)
        ->assertTableColumnSummarySet('rating', 'average', $posts->avg('rating'));
});
```

The first argument is the column name, the second is the summarizer ID, and the third is the expected value.

Note that the expected and actual values are normalized, such that `123.12` is considered the same as `"123.12"`, and `['Fred', 'Jim']` is the same as `['Jim', 'Fred']`.

You may set a summarizer ID by passing it to the `make()` method:

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\TextColumn;

TextColumn::make('rating')
    ->summarize(Average::make('average'))
```

The ID should be unique between summarizers in that column.

### Summarizing only one pagination page

To calculate the average for only one pagination page, use the `isCurrentPaginationPageOnly` argument:

```php
use function Pest\Livewire\livewire;

it('can average values in a column', function () {
    $posts = Post::factory()->count(20)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts->take(10))
        ->assertTableColumnSummarySet('rating', 'average', $posts->take(10)->avg('rating'), isCurrentPaginationPageOnly: true);
});
```

### Testing a range summarizer

To test a range, pass the minimum and maximum value into a tuple-style `[$minimum, $maximum]` array:

```php
use function Pest\Livewire\livewire;

it('can average values in a column', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts)
        ->assertTableColumnSummarySet('rating', 'range', [$posts->min('rating'), $posts->max('rating')]);
});
```
