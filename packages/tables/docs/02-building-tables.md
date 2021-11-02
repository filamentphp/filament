---
title: Building Tables
---

## Preparing your Livewire component

Implement the `HasTable` interface and use the `InteractsWithTable` trait:

```php
<?php

namespace App\Http\Livewire;

use Filament\Tables;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListPosts extends Component implements Table\Contracts\HasTable // [tl! focus]
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

class ListPosts extends Component implements Table\Contracts\HasTable
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

class ListPosts extends Component implements Table\Contracts\HasTable
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
                ->rounded(),
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('author.name'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'danger' => 'draft',
                    'warning' => 'reviewing',
                    'success' => 'published',
                ]),
            Tables\Columns\BooleanColumn::make('is_featured'),
        ]; // [tl! collapse:end]
    }
    
    protected function getTableFilters(): array
    {
        return [ // [tl! collapse:start]
            Tables\Filters\Filter::make('published')
                ->query(fn (Builder $query): $query => $query->where('is_published', true)),
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
            Tables\Actions\LinkAction::make('edit')
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

class ListPosts extends Component implements Table\Contracts\HasTable
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

You may customise the options for the paginated records per page select by overriding the `getTableRecordsPerPageSelectOptions()` method on your Livewire component:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListPosts extends Component implements Table\Contracts\HasTable
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
