---
title: Badge column
---

Badge columns render a colored badge with the cell's contents:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->enum([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
```

## Customizing the color

Badges may have a color. It may be either `primary`, `secondary`, `success`, `warning` or `danger`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->colors([
        'primary',
        'gray' => 'draft',
        'warning' => 'reviewing',
        'success' => 'published',
        'danger' => 'rejected',
    ])
```

You may instead activate a color using a callback, accepting the cell's `$state`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->colors([
        'primary',
        'gray' => static fn ($state): bool => $state === 'draft',
        'warning' => static fn ($state): bool => $state === 'reviewing',
        'success' => static fn ($state): bool => $state === 'published',
        'danger' => static fn ($state): bool => $state === 'rejected',
    ])
```

Or dynamically calculate the color based on the `$record` and / or `$state`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->color(static function ($state): string {
        if ($state === 'published') {
            return 'success';
        }
        
        return 'gray';
    })
```

## Adding an icon

Badges may also have an icon:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icons([
        'heroicon-m-x-mark',
        'heroicon-m-document' => 'draft',
        'heroicon-m-arrow-path' => 'reviewing',
        'heroicon-m-truck' => 'published',
    ])
```

Alternatively, you may conditionally display an icon using a closure:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icons([
        'heroicon-m-x-mark',
        'heroicon-m-document' => static fn ($state): bool => $state === 'draft',
        'heroicon-m-arrow-path' => static fn ($state): bool => $state === 'reviewing',
        'heroicon-m-truck' => static fn ($state): bool => $state === 'published',
    ])
```

Or dynamically calculate the icon based on the `$record` and / or `$state`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icon(static function ($state): string {
        if ($state === 'published') {
            return 'heroicon-m-truck';
        }
        
        return 'heroicon-m-x-mark';
    })
```

You may set the position of an icon using `iconPosition()`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icons([
        'heroicon-m-x-mark',
        'heroicon-m-document' => 'draft',
        'heroicon-m-arrow-path' => 'reviewing',
        'heroicon-m-truck' => 'published',
    ])
    ->iconPosition('after') // `before` or `after`
```

## Formatting the text

All formatting options available for [text columns](text) are also available for badge columns.
