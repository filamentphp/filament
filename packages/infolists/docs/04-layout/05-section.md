---
title: Section
---

## Overview

You may want to separate your entries into sections, each with a heading and description. To do this, you can use a section component:

```php
use Filament\Infolists\Components\Section;

Section::make('Heading')
    ->description('Description')
    ->schema([
        // ...
    ])
```

## Adding an icon to the section's header

You may add an icon to the section's header using the `icon()` method:

```php
use Filament\Infolists\Components\Section;

Section::make('Heading')
    ->description('Description')
    ->icon('heroicon-m-shopping-bag')
    ->schema([
        // ...
    ])
```

## Positioning the heading and description aside

You may use the `aside()` to align heading & description on the left, and the infolist components inside a card on the right:

```php
use Filament\Infolists\Components\Section;

Section::make('Heading')
    ->description('Description')
    ->aside()
    ->schema([
        // ...
    ])
```

## Collapsing sections

Sections may be `collapsible()` to optionally hide content in long infolists:

```php
use Filament\Infolists\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->collapsible()
```

Your sections may be `collapsed()` by default:

```php
use Filament\Infolists\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->collapsed()
```

## Compact section styling

When nesting sections, you can use a more compact styling:

```php
use Filament\Infolists\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->compact()
```

## Using grid columns within a section

You may use the `columns()` method to easily create a [grid](grid) within the section:

```php
use Filament\Infolists\Components\Section;

Section::make('Heading')
    ->schema([
        // ...
    ])
    ->columns(2)
```
