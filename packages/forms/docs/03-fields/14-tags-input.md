---
title: Tags input
---

The tags input component allows you to interact with a list of tags.

By default, tags are stored in JSON:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
```

![](https://user-images.githubusercontent.com/41773797/147614098-14d6eecb-2674-49b6-a5ee-e963c4d88a6e.png)

If you're saving the JSON tags using Eloquent, you should be sure to add an `array` [cast](https://laravel.com/docs/eloquent-mutators#array-and-json-casting) to the model property:

```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
        'tags' => 'array',
    ];

    // ...
}
```

> Filament also supports [`spatie/laravel-tags`](https://github.com/spatie/laravel-tags). See our [plugin documentation](/docs/spatie-laravel-tags-plugin) for more information.

## Comma-separated tags

You may allow the tags to be stored in a separated string, instead of JSON. To set this up, pass the separating character to the `separator()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')->separator(',')
```

## Autocompleting tag suggestions

Tags inputs may have autocomplete suggestions. To enable this, pass an array of suggestions to the `suggestions()` method:

```php
use Filament\Forms\Components\TagsInput;

TagsInput::make('tags')
    ->suggestions([
        'tailwindcss',
        'alpinejs',
        'laravel',
        'livewire',
    ])
```

![](https://user-images.githubusercontent.com/41773797/147614115-7570a6cb-dd91-4912-8adf-e54a51f1c567.png)
