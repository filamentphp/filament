---
title: Testing
---

All examples in this guide will be written using [Pest](https://pestphp.com). However, you can easily adapt this to a PHPUnit.

Since the table builder works on Livewire components, you can use the [Livewire testing helpers](https://laravel-livewire.com/docs/testing). However, we have many custom testing helpers that you can use for tables:

## Render

To ensure a table component renders, use the `assertSuccessful()` Livewire helper:

```php
use function Pest\Livewire\livewire;

it('can render page', function () {
    livewire(ListPosts::class)->assertSuccessful();
});
```

To check that you can see certain records in the table, pass them to `assertCanSeeTableRecords()`:

```php
use function Pest\Livewire\livewire;

it('can list posts', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts);
});
```

> Note that if your table uses pagination, `assertCanSeeTableRecords()` will only check for records on the first page. To switch page, call `set('page', 2)`.

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

### Authorization

To ensure that a particular user cannot see a column, you can use the `assertTableColumnHidden()` method:

```php
use function Pest\Livewire\livewire;

it('can hide the author column', function () {
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableColumnHidden(`author`);
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

To reset all filters to their original state, call `resetTableFilters()`

```php
use function Pest\Livewire\livewire;

it('can reset table filters`', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->resetTableFilters();
});
```

## Actions

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

For bulk actions, you may do the same, passing in records to execute the bulk action against to `callTableBulkAction()`:

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

For bulk actions, this method is called `assertHasTableBulkActionErrors()`.

To check if an action or bulk action is pre-filled with data, you can use the `assertTableActionDataSet()` or `assertTableBulkActionDataSet()` method:

```php
use function Pest\Livewire\livewire;

it('can load existing post data for editing', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->mountTableAction(EditAction::class, $post)
        ->assertTableActionDataSet([[
            'title' => $post->title,
        ])
        ->setTableActionData([[
            'title' => $title = fake()->words(asText: true),
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($post->refresh())
        ->title->toBe($title);
});
```

To ensure that an action or bulk action is hidden for a user, you can use the `assertTableActionHidden()` or `assertTableBulkActionHidden()` method:

```php
use function Pest\Livewire\livewire;

it('can not publish posts', function () {
    $post = Post::factory()->create();
    
    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionHidden('publish', $post)
        ->assertTableBulkActionHidden('publish');
});
```
