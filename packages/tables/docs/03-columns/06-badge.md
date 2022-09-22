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

Badges may have a color. It may be either `primary`, `success`, `warning` or `danger`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->colors([
        'primary',
        'danger' => 'draft',
        'warning' => 'reviewing',
        'success' => 'published',
    ])
```

You may instead activate a color using a callback, accepting the cell's `$state`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->colors([
        'primary',
        'danger' => static fn ($state): bool => $state === 'draft',
        'warning' => static fn ($state): bool => $state === 'reviewing',
        'success' => static fn ($state): bool => $state === 'published',
    ])
```

Or dynamically calculate the color based on the `$record` and / or `$state`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icon(static function ($state): string {
        if ($state === 'published') {
            return 'success';
        }
        
        return 'secondary';
    })
```

## Adding an icon

Badges may also have an icon:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icons([
        'heroicon-o-x',
        'heroicon-o-document' => 'draft',
        'heroicon-o-refresh' => 'reviewing',
        'heroicon-o-truck' => 'published',
    ])
```

Alternatively, you may conditionally display an icon using a closure:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icons([
        'heroicon-o-x',
        'heroicon-o-document' => static fn ($state): bool => $state === 'draft',
        'heroicon-o-refresh' => static fn ($state): bool => $state === 'reviewing',
        'heroicon-o-truck' => static fn ($state): bool => $state === 'published',
    ])
```

Or dynamically calculate the icon based on the `$record` and / or `$state`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icon(static function ($state): string {
        if ($state === 'published') {
            return 'heroicon-o-truck';
        }
        
        return 'heroicon-o-x';
    })
```

You may set the position of an icon using `iconPosition()`:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->icons([
        'heroicon-o-x',
        'heroicon-o-document' => 'draft',
        'heroicon-o-refresh' => 'reviewing',
        'heroicon-o-truck' => 'published',
    ])
    ->iconPosition('after') // `before` or `after`
```

## Formatting the text

All formatting options available for [text columns](text) are also available for badge columns.
