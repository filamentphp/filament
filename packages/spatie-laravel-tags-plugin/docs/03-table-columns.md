---
title: Table Columns
---

To use the tags column:

```php
use Filament\Tables\Columns\SpatieTagsColumn;

SpatieTagsColumn::make('tags'),
```

Optionally, you may pass a `type()`:

```php
use Filament\Tables\Columns\SpatieTagsColumn;

SpatieTagsColumn::make('tags')->type('categories'),
```

The [type](https://spatie.be/docs/laravel-tags/v4/advanced-usage/using-types) allows you to group tags into collections.

The tags column supports all the customization options of the [original tags column](/docs/tables/columns#tags-column).
