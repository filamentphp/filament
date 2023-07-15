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
        'secondary' => 'draft',
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
        'secondary' => static fn ($state): bool => $state === 'draft',
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

## Allowing the text to be copied to the clipboard

You may make the text copyable, such that clicking on the cell copies the text to the clipboard, and optionally specify a custom confirmation message and duration in milliseconds. This feature only works when SSL is enabled for the app.

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('email')
    ->copyable()
    ->copyMessage('Email address copied')
    ->copyMessageDuration(1500)
```

> Filament uses tooltips to display the copy message in the admin panel. If you want to use the copyable feature outside of the admin panel, make sure you have [`@ryangjchandler/alpine-tooltip` installed](https://github.com/ryangjchandler/alpine-tooltip#installation) in your app.

### Customizing the text that is copied to the clipboard

You can customize the text that gets copied to the clipboard using the `copyableState() method:

```php
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('name')
    ->copyable()
    ->copyableState(fn (string $state): string => "Name: {$state}")
```

In this function, you can access the whole table row with `$record`:

```php
use App\Models\User;
use Filament\Tables\Columns\BadgeColumn;

BadgeColumn::make('name')
    ->copyable()
    ->copyableState(fn (User $record): string => "Name: {$record->name}")
```
