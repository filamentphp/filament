# Filament Spatie Tags Plugin

## Installation

Install the plugin with Composer:

```bash
composer require filament/spatie-laravel-tags-plugin:"^4.0" -W
```

If you haven't already done so, you need to publish the migration to create the tags table:

```bash
php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="tags-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

You must also [prepare your Eloquent model](https://spatie.be/docs/laravel-tags/basic-usage/using-tags) for attaching tags.

> For more information, check out [Spatie's documentation](https://spatie.be/docs/laravel-tags).

## Form component

This guide assumes that you've already set up your model to attach tags as per [Spatie's documentation](https://spatie.be/docs/laravel-tags/basic-usage/using-tags).

You may use the field in the same way as the [original tags input](https://filamentphp.com/docs/forms/tags-input) field:

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags')
```

Optionally, you may pass a [`type()`](https://spatie.be/docs/laravel-tags/advanced-usage/using-types) that allows you to group tags into collections:

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags')
    ->type('categories')
```

The tags input supports all the customization options of the [original tags input component](https://filamentphp.com/docs/forms/tags-input).

## Table column

This guide assumes that you've already set up your model attach tags as per [Spatie's documentation](https://spatie.be/docs/laravel-tags/basic-usage/using-tags).

To use the tags column:

```php
use Filament\Tables\Columns\SpatieTagsColumn;

SpatieTagsColumn::make('tags')
```

Optionally, you may pass a `type()`:

```php
use Filament\Tables\Columns\SpatieTagsColumn;

SpatieTagsColumn::make('tags')
    ->type('categories')
```

The [type](https://spatie.be/docs/laravel-tags/advanced-usage/using-types) allows you to group tags into collections.

The tags column supports all the customization options of the [original tags column](https://filamentphp.com/docs/tables/columns/tags).

## Infolist entry

This guide assumes that you've already set up your model attach tags as per [Spatie's documentation](https://spatie.be/docs/laravel-tags/basic-usage/using-tags).

To use the tags entry:

```php
use Filament\Infolists\Components\SpatieTagsEntry;

SpatieTagsEntry::make('tags')
```

Optionally, you may pass a `type()`:

```php
use Filament\Infolists\Components\SpatieTagsEntry;

SpatieTagsEntry::make('tags')
    ->type('categories')
```

The [type](https://spatie.be/docs/laravel-tags/advanced-usage/using-types) allows you to group tags into collections.

The tags entry supports all the customization options of the [text entry](https://filamentphp.com/docs/infolists/entries/text).
