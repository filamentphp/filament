---
title: Building Tables
---

## Preparing your Livewire component

Implement the `HasTable` and `HasForms` interface, and use the `InteractsWithTable` trait:

```php
<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListPosts extends Component implements Forms\Contracts\HasForms, Table\Contracts\HasTable // [tl! focus]
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
use Filament\Forms;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListPosts extends Component implements Forms\Contracts\HasForms, Table\Contracts\HasTable // [tl! focus]
{
    use Tables\Concerns\InteractsWithTable; // [tl! focus]
    
    protected function getTableQuery(): Builder
    {
        return Post::query();
    }
    
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
use Filament\Forms;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;use Livewire\Component;

class ListPosts extends Component implements Forms\Contracts\HasForms, Table\Contracts\HasTable // [tl! focus]
{
    use Tables\Concerns\InteractsWithTable; // [tl! focus]
    
    protected function getTableQuery(): Builder
    {
        return Post::query();
    }
    
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('author.avatar')
                ->size(40)
                ->rounded(),
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\TextColumn::make('author.name'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'danger' => 'draft',
                    'warning' => 'in_review',
                    'success' => 'approved',
                ]),
            Tables\Columns\BooleanColumn::make('is_published'),
        ];
    }
    
    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('published')
                ->query(fn (Builder $query): $query => $query->where('is_published', true)),
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'draft' => 'Draft',
                    'in_review' => 'In Review',
                    'approved' => 'Approved',
                ]),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\LinkAction::make('edit')
                ->url(fn (Post $record): string => route('posts.edit', $record)),
        ];
    }
    
    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('delete')
                ->label('Delete selected')
                ->color('danger')
                ->action(function (Collection $records): void {
                    $records->each->delete();
                })
                ->requiresConfirmation(),
        ];
    }
    
    public function render(): View
    {
        return view('list-posts');
    }
}
```

Visit your Livewire component in the browser, and you should see the table.

## Pagination

By default, 
