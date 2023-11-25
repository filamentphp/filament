---
title: Getting started
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Filament's Table Builder package allows you to [add an interactive datatable to any Livewire component](adding-a-table-to-a-livewire-component). It's also used within other Filament packages, such as the [Panel Builder](../panels) for displaying [resources](../panels/resources/getting-started) and [relation managers](../panels/resources/relation-managers), as well as for the [table widget](../panels/dashboard#table-widgets). Learning the features of the Table Builder will be incredibly time-saving when both building your own custom Livewire tables and using Filament's other packages.

This guide will walk you through the basics of building tables with Filament's table package. If you're planning to add a new table to your own Livewire component, you should [do that first](adding-a-table-to-a-livewire-component) and then come back. If you're adding a table to an [app resource](../panels/resources/getting-started), or another Filament package, you're ready to go!

## Defining table columns

The basis of any table is rows and columns. Filament uses Eloquent to get the data for rows in the table, and you are responsible for defining the columns that are used in that row.

Filament includes many column types prebuilt for you, and you can [view a full list here](columns/getting-started#available-columns). You can even [create your own custom column types](columns/custom) to display data in whatever way you need.

Columns are stored in an array, as objects within the `$table->columns()` method:

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title'),
            TextColumn::make('slug'),
            IconColumn::make('is_featured')
                ->boolean(),
        ]);
}
```

<AutoScreenshot name="tables/getting-started/columns" alt="Table with columns" version="3.x" />

In this example, there are 3 columns in the table. The first two display [text](columns/text) - the title and slug of each row in the table. The third column displays an [icon](columns/icon), either a green check or a red cross depending on if the row is featured or not.

### Making columns sortable and searchable

You can easily modify columns by chaining methods onto them. For example, you can make a column [searchable](columns/getting-started#searching) using the `searchable()` method. Now, there will be a search field in the table, and you will be able to filter rows by the value of that column:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->searchable()
```

<AutoScreenshot name="tables/getting-started/searchable-columns" alt="Table with searchable column" version="3.x" />

You can make multiple columns searchable, and Filament will be able to search for matches within any of them, all at once.

You can also make a column [sortable](columns/getting-started#sorting) using the `sortable()` method. This will add a sort button to the column header, and clicking it will sort the table by that column:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->sortable()
```

<AutoScreenshot name="tables/getting-started/sortable-columns" alt="Table with sortable column" version="3.x" />

### Accessing related data from columns

You can also display data in a column that belongs to a relationship. For example, if you have a `Post` model that belongs to a `User` model (the author of the post), you can display the user's name in the table:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('author.name')
```

<AutoScreenshot name="tables/getting-started/relationship-columns" alt="Table with relationship column" version="3.x" />

In this case, Filament will search for an `author` relationship on the `Post` model, and then display the `name` attribute of that relationship. We call this "dot notation" - you can use it to display any attribute of any relationship, even nested distant relationships. Filament uses this dot notation to eager-load the results of that relationship for you.

## Defining table filters

As well as making columns `searchable()`, you can allow the users to filter rows in the table in other ways. We call these components "filters", and they are defined in the `$table->filters()` method:

```php
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->filters([
            Filter::make('is_featured')
                ->query(fn (Builder $query) => $query->where('is_featured', true)),
            SelectFilter::make('status')
                ->options([
                    'draft' => 'Draft',
                    'reviewing' => 'Reviewing',
                    'published' => 'Published',
                ]),
        ]);
}
```

<AutoScreenshot name="tables/getting-started/filters" alt="Table with filters" version="3.x" />

In this example, we have defined 2 table filters. On the table, there is now a "filter" icon button in the top corner. Clicking it will open a dropdown with the 2 filters we have defined.

The first filter is rendered as a checkbox. When it's checked, only featured rows in the table will be displayed. When it's unchecked, all rows will be displayed.

The second filter is rendered as a select dropdown. When a user selects an option, only rows with that status will be displayed. When no option is selected, all rows will be displayed.

It's possible to define as many filters as you need, and use any component from the [Form Builder package](../forms) to create a UI. For example, you could create [a custom date range filter](../filters#custom-filter-forms).

## Defining table actions

Filament's tables can use [Actions](../actions/overview). They are buttons that can be added to the [end of any table row](actions#row-actions), or even in the [header](actions#header-actions) of a table. For instance, you may want an action to "create" a new record in the header, and then "edit" and "delete" actions on each row. [Bulk actions](actions#bulk-actions) can be used to execute code when records in the table are selected.

```php
use App\Models\Post;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

public function table(Table $table): Table
{
    return $table
        ->columns([
            // ...
        ])
        ->actions([
            Action::make('feature')
                ->action(function (Post $record) {
                    $record->is_featured = true;
                    $record->save();
                })
                ->hidden(fn (Post $record): bool => $record->is_featured),
            Action::make('unfeature')
                ->action(function (Post $record) {
                    $record->is_featured = false;
                    $record->save();
                })
                ->visible(fn (Post $record): bool => $record->is_featured),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
}
```

<AutoScreenshot name="tables/getting-started/actions" alt="Table with actions" version="3.x" />

In this example, we define 2 actions for table rows. The first action is a "feature" action. When clicked, it will set the `is_featured` attribute on the record to `true` - which is written within the `action()` method. Using the `hidden()` method, the action will be hidden if the record is already featured. The second action is an "unfeature" action. When clicked, it will set the `is_featured` attribute on the record to `false`. Using the `visible()` method, the action will be hidden if the record is not featured.

We also define a bulk action. When bulk actions are defined, each row in the table will have a checkbox. This bulk action is [built-in to Filament](../actions/prebuilt-actions/delete#bulk-delete), and it will delete all selected records. However, you can [write your own custom bulk actions](actions#bulk-actions) easily too.

<AutoScreenshot name="tables/getting-started/actions-modal" alt="Table with action modal open" version="3.x" />

Actions can also open modals to request confirmation from the user, as well as render forms inside to collect extra data. It's a good idea to read the [Actions documentation](../actions/overview) to learn more about their extensive capabilities throughout Filament.

## Next steps with the Table Builder package

Now you've finished reading this guide, where to next? Here are some suggestions:

- [Explore the available columns to display data in your table.](columns/getting-started#available-columns)
- [Deep dive into table actions and start using modals.](actions)
- [Discover how to build complex, responsive table layouts without touching CSS.](layout)
- [Add summaries to your tables, which give an overview of the data inside them.](summaries)
- [Find out about all advanced techniques that you can customize tables to your needs.](advanced)
- [Write automated tests for your tables using our suite of helper methods.](testing)
