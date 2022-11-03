---
title: Section
---

You may want to separate your fields into sections, each with a heading and description. To do this, you can use a section component:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->description('Description')
    ->schema([
        // ...
    ])
```

## Grid columns

You may use the `columns()` method to easily create a [grid](grid) within the section:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->columns(2)
```

## Positioning the heading and description aside

You may use the `aside()` to align heading & description on the left, and the form components inside a card on the right:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->description('Description')
    ->aside()
    ->schema([
        // ...
    ])
```

## Collapsing sections

Sections may be `collapsible()` to optionally hide content in long forms:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->collapsible()
```

Your sections may be `collapsed()` by default:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->collapsed()
```

## Compact section styling

When nesting sections, you can use a more compact styling:

```php
use Filament\Forms\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->compact()
```
