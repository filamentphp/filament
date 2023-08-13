---
title: Enums
---

## Overview

Enums are special PHP classes that represent a fixed set of constants. They are useful for modeling concepts that have a limited number of possible values, like days of the week, months in a year, or the suits in a deck of cards.

Since enum "cases" are instances of the enum class, adding interfaces to enums proves to be very useful. Filament provides a collection of interfaces that you can add to enums, which enhance your experience when working with them.

> When using an enum with an attribute on your Eloquent model, please [ensure that it is cast correctly](https://laravel.com/docs/eloquent-mutators#enum-casting).

## Enum labels

The `HasLabel` interface transforms an enum instance into a textual label. This is useful for displaying human-readable enum values in your UI.

```php
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel
{
    case Draft = 'draft';
    case Reviewing = 'reviewing';
    case Published = 'published';
    case Rejected = 'rejected';
    
    public function getLabel(): ?string
    {
        return $this->name;
        
        // or
    
        return match ($this) {
            self::Draft => 'Draft',
            self::Reviewing => 'Reviewing',
            self::Published => 'Published',
            self::Rejected => 'Rejected',
        };
    }
}
```

### Using the enum label with form field options

The `HasLabel` interface can be used to generate an array of options from an enum, where the enum's value is the key and the enum's label is the value. This applies to Form Builder fields like [`Select`](../forms/fields/select) and [`CheckboxList`](../forms/fields/checkbox-list), as well as the Table Builder's [`SelectColumn`](../tables/columns/select) and [`SelectFilter`](../tables/filters#select-filters):

```php
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;

Select::make('status')
    ->options(Status::class)

CheckboxList::make('status')
    ->options(Status::class)

SelectColumn::make('status')
    ->options(Status::class)

SelectFilter::make('status')
    ->options(Status::class)
```

In these examples, `Status::class` is the enum class which implements `HasLabel`, and the options are generated from that:

```php
[
    'draft' => 'Draft',
    'reviewing' => 'Reviewing',
    'published' => 'Published',
    'rejected' => 'Rejected',
]
```

### Using the enum label with a text column in your table

If you use a [`TextColumn`](../tables/columns/text) with the Table Builder, and it is cast to an enum in your Eloquent model, Filament will automatically use the `HasLabel` interface to display the enum's label instead of its raw value.

### Using the enum label as a group title in your table

If you use a [grouping](../tables/grouping) with the Table Builder, and it is cast to an enum in your Eloquent model, Filament will automatically use the `HasLabel` interface to display the enum's label instead of its raw value. The label will be displayed as the [title of each group](../tables/grouping#setting-a-group-title).

### Using the enum label with a text entry in your infolist

If you use a [`TextColumn`](../infolists/entries/text) with the Infolist Builder, and it is cast to an enum in your Eloquent model, Filament will automatically use the `HasLabel` interface to display the enum's label instead of its raw value.

## Enum colors

The `HasColor` interface transforms an enum instance into a [color](colors). This is useful for displaying colored enum values in your UI.

```php
use Filament\Support\Contracts\HasColor;

enum Status: string implements HasColor
{
    case Draft = 'draft';
    case Reviewing = 'reviewing';
    case Published = 'published';
    case Rejected = 'rejected';
    
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Reviewing => 'warning',
            self::Published => 'success',
            self::Rejected => 'danger',
        };
    }
}
```

### Using the enum color with a text column in your table

If you use a [`TextColumn`](../tables/columns/text) with the Table Builder, and it is cast to an enum in your Eloquent model, Filament will automatically use the `HasColor` interface to display the enum label in its color. This works best if you use the [`badge()`](../tables/columns/text#displaying-as-a-badge) method on the column.

### Using the enum color with a text column in your infolist

If you use a [`TextEntry`](../infolists/entries/text) with the Infolist Builder, and it is cast to an enum in your Eloquent model, Filament will automatically use the `HasColor` interface to display the enum label in its color. This works best if you use the [`badge()`](../infolists/entries/text#displaying-as-a-badge) method on the entry.

## Enum icons

The `HasIcon` interface transforms an enum instance into an [icon](icons). This is useful for displaying icons alongside enum values in your UI.

```php
use Filament\Support\Contracts\HasIcon;

enum Status: string implements HasIcon
{
    case Draft = 'draft';
    case Reviewing = 'reviewing';
    case Published = 'published';
    case Rejected = 'rejected';
    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-m-pencil',
            self::Reviewing => 'heroicon-m-eye',
            self::Published => 'heroicon-m-check',
            self::Rejected => 'heroicon-m-x-mark',
        };
    }
}
```

### Using the enum icon with a text column in your table

If you use a [`TextColumn`](../tables/columns/text) with the Table Builder, and it is cast to an enum in your Eloquent model, Filament will automatically use the `HasIcon` interface to display the enum's icon aside its label. This works best if you use the [`badge()`](../tables/columns/text#displaying-as-a-badge) method on the column.

### Using the enum icon with a text entry in your infolist

If you use a [`TextEntry`](../infolists/entries/text) with the Infolist Builder, and it is cast to an enum in your Eloquent model, Filament will automatically use the `HasIcon` interface to display the enum's icon aside its label. This works best if you use the [`badge()`](../infolists/entries/text#displaying-as-a-badge) method on the entry.
