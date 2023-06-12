---
title: Getting started
---

## Preparing your Livewire component

Implement the `HasTable` interface and use the `InteractsWithTable` trait:

```php
<?php

namespace App\Http\Livewire;

use Filament\Tables;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListPosts extends Component implements Tables\Contracts\HasTable // [tl! focus]
{
    use Tables\Concerns\InteractsWithTable; // [tl! focus]

    public function render(): View
    {
        return view('list-posts');
    }
}
```

In your Livewire component's view, render the table:

```blade
<div>
    {{ $this->table }}
</div>
```

Next, add the Eloquent query you would like the table to be based upon in the `getTableQuery()` method:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListPosts extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder // [tl! focus:start]
    {
        return Post::query();
    } // [tl! focus:end]

    public function render(): View
    {
        return view('list-posts');
    }
}
```

Finally, add any [columns](columns), [filters](filters), and [actions](actions) to the Livewire component:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ListPosts extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Post::query();
    }

    protected function getTableColumns(): array // [tl! focus:start]
    {
        return [ // [tl! collapse:start]
            Tables\Columns\ImageColumn::make('author.avatar')
                ->size(40)
                ->circular(),
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('author.name'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'danger' => 'draft',
                    'warning' => 'reviewing',
                    'success' => 'published',
                ]),
            Tables\Columns\IconColumn::make('is_featured')->boolean(),
        ]; // [tl! collapse:end]
    }

    protected function getTableFilters(): array
    {
        return [ // [tl! collapse:start]
            Tables\Filters\Filter::make('published')
                ->query(fn (Builder $query): Builder => $query->where('is_published', true)),
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'draft' => 'Draft',
                    'in_review' => 'In Review',
                    'approved' => 'Approved',
                ]),
        ]; // [tl! collapse:end]
    }

    protected function getTableActions(): array
    {
        return [ // [tl! collapse:start]
            Tables\Actions\Action::make('edit')
                ->url(fn (Post $record): string => route('posts.edit', $record)),
        ]; // [tl! collapse:end]
    }

    protected function getTableBulkActions(): array
    {
        return [ // [tl! collapse:start]
            Tables\Actions\BulkAction::make('delete')
                ->label('Delete selected')
                ->color('danger')
                ->action(function (Collection $records): void {
                    $records->each->delete();
                })
                ->requiresConfirmation(),
        ]; // [tl! collapse:end]
    } // [tl! focus:end]

    public function render(): View
    {
        return view('list-posts');
    }
}
```

Visit your Livewire component in the browser, and you should see the table.

## Pagination

By default, tables will be paginated. To disable this, you should override the `isTablePaginationEnabled()` method on your Livewire component:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListPosts extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Post::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('author.name'),
        ];
    }

    protected function isTablePaginationEnabled(): bool // [tl! focus:start]
    {
        return false;
    } // [tl! focus:end]

    public function render(): View
    {
        return view('list-posts');
    }
}
```

You may customize the options for the paginated records per page select by overriding the `getTableRecordsPerPageSelectOptions()` method on your Livewire component:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListPosts extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Post::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('author.name'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array // [tl! focus:start]
    {
        return [10, 25, 50, 100];
    } // [tl! focus:end]

    public function render(): View
    {
        return view('list-posts');
    }
}
```

By default, Livewire stores the pagination state in a `page` parameter of the URL query string. If you have multiple tables on the same page, this will mean that the pagination state of one table may be overwritten by the state of another table.

To fix this, you may define a `getTableQueryStringIdentifier()` on your component, to return a unique query string identifier for that table:

```php
protected function getTableQueryStringIdentifier(): string
{
    return 'users';
}
```

### Simple pagination

You may use simple pagination by overriding `paginateTableQuery()` method on your Livewire component:

```php
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

protected function paginateTableQuery(Builder $query): Paginator
{
    return $query->simplePaginate($this->getTableRecordsPerPage() == -1 ? $query->count() : $this->getTableRecordsPerPage());
}
```

## Searching records with Laravel Scout

While Filament doesn't provide a direct integration with [Laravel Scout](https://laravel.com/docs/scout), you may override methods to integrate it with your Livewire component.

First, you must ensure that the table search input is visible:

```php
public function isTableSearchable(): bool
{
    return true;
}
```

Now, use a `whereIn()` clause to filter the query for Scout results:

```php
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

protected function applySearchToTableQuery(Builder $query): Builder
{
    if (filled($searchQuery = $this->getTableSearchQuery())) {
        $query->whereIn('id', Post::search($searchQuery)->keys());
    }

    return $query;
}
```

Scout uses this `whereIn()` method to retrieve results internally, so there is no performance penalty for using it.

## Clickable rows

### Record URLs

You may allow table rows to be completely clickable by overriding the `getTableRecordUrlUsing()` method on your Livewire component:

```php
use Closure;
use Illuminate\Database\Eloquent\Model;

protected function getTableRecordUrlUsing(): ?Closure
{
    return fn (Model $record): string => route('posts.edit', ['record' => $record]);
}
```

In this example, clicking on each post will take you to the `posts.edit` route.

If you'd like to [override the URL](columns/getting-started#opening-urls) for a specific column, or instead [run a Livewire action](columns#running-actions) when a column is clicked, see the [columns documentation](columns#opening-urls).

### Record actions

Alternatively, you may configure table rows to trigger an action instead of opening a URL:

```php
use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;

protected function getTableRecordActionUsing(): ?Closure
{
    return fn (): string => 'edit';
}
```

In this case, if an `EditAction` or another action with the name `edit` exists on the table row, that will be called. If not, a Livewire public method with the name `edit()` will be called, and the selected record will be passed.

### Disabling clickable rows

If you'd like to completely disable the click action for the entire row, you may override the `getTableRecordActionUsing()` method on your Livewire component, and return `null`:

```php
use Closure;

protected function getTableRecordActionUsing(): ?Closure
{
    return null;
}
```

## Record classes

You may want to conditionally style rows based on the record data. This can be achieved by specifying a string or array of CSS classes to be applied to the row using the `getTableRecordClassesUsing()` method:

```php
use Closure;
use Illuminate\Database\Eloquent\Model;

protected function getTableRecordClassesUsing(): ?Closure
{
    return fn (Model $record) => match ($record->status) {
        'draft' => 'opacity-30',
        'reviewing' => [
            'border-l-2 border-orange-600',
            'dark:border-orange-300' => config('tables.dark_mode'),
        ],
        'published' => 'border-l-2 border-green-600',
        default => null,
    };
}
```

These classes are not automatically compiled by Tailwind CSS. If you want to apply Tailwind CSS classes that are not already used in Blade files, you should update your `content` configuration in `tailwind.config.js` to also scan for classes in your desired PHP files:

```js
export default {
    content: ['./app/Filament/**/*.php'],
}
```

Alternatively, you may add the classes to your [safelist](https://tailwindcss.com/docs/content-configuration#safelisting-classes):

```js
export default {
    safelist: [
        'border-green-600',
        'border-l-2',
        'border-orange-600',
        'dark:border-orange-300',
        'opacity-30',
    ],
}
```

## Empty state

By default, an "empty state" card will be rendered when the table is empty. To customize this, you may define methods on your Livewire component:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ListPosts extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Post::query();
    }

    protected function getTableColumns(): array
    {
        return [ // [tl! collapse:start]
            Tables\Columns\ImageColumn::make('author.avatar')
                ->size(40)
                ->circular(),
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('author.name'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'danger' => 'draft',
                    'warning' => 'reviewing',
                    'success' => 'published',
                ]),
            Tables\Columns\IconColumn::make('is_featured')->boolean(),
        ]; // [tl! collapse:end]
    }

    protected function getTableEmptyStateIcon(): ?string // [tl! focus:start]
    {
        return 'heroicon-o-bookmark';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No posts yet';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'You may create a post using the button below.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Create post')
                ->url(route('posts.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    } // [tl! focus:end]

    public function render(): View
    {
        return view('list-posts');
    }
}
```

## Query string

Livewire ships with a feature to store data in the URL's query string, to access across requests.

With Filament, this allows you to store your table's filters, sort, search and pagination state in the URL.

To store the filters, sorting, and search state of your table in the query string:

```php
protected $queryString = [
    'tableFilters',
    'tableSortColumn',
    'tableSortDirection',
    'tableSearchQuery' => ['except' => ''],
    'tableColumnSearchQueries',
];
```

## Reordering records

To allow the user to reorder records using drag and drop in your table, you can use the `getTableReorderColumn()` method:

```php
protected function getTableReorderColumn(): ?string
{
    return 'sort';
}
```

When making the table reorderable, a new button will be available on the table to toggle reordering.

The `getTableReorderColumn()` method returns the name of a column to store the record order in. If you use something like [`spatie/eloquent-sortable`](https://github.com/spatie/eloquent-sortable) with an order column such as `order_column`, you may return this instead:

```php
protected function getTableReorderColumn(): ?string
{
    return 'order_column';
}
```

### Enabling pagination while reordering

Pagination will be disabled in reorder mode to allow you to move records between pages. It is generally bad UX to re-enable pagination while reordering, but if you are sure then you can use:

```php
protected function isTablePaginationEnabledWhileReordering(): bool
{
    return true;
}
```

## Polling content

You may poll table content so that it refreshes at a set interval, using the `getTablePollingInterval()` method:

```php
protected function getTablePollingInterval(): ?string
{
    return '10s';
}
```

## Using the form builder

Internally, the table builder uses the [form builder](/docs/forms) to implement filtering, actions, and bulk actions. Because of this, the form builder is already set up on your Livewire component and ready to use with your own custom forms.

You may use the default `form` out of the box:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ListPosts extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            // ...
        ];
    }

    protected function getTableQuery(): Builder // [tl! collapse:start]
    {
        return Post::query();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('author.avatar')
                ->size(40)
                ->circular(),
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('author.name'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'danger' => 'draft',
                    'warning' => 'reviewing',
                    'success' => 'published',
                ]),
            Tables\Columns\IconColumn::make('is_featured')->boolean(),
        ];
    } // [tl! collapse:end]

    public function render(): View
    {
        return view('list-posts');
    }
}
```
