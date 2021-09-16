---
title: Form Components
---

## Tags input

To use the tags input, you may pass a `model()` to the component:

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags')
    ->model(Post::find(1)),
```

When a model instance is attached, tags will automatically be populated [and saved](/docs/forms/building-forms#field-relationships) to that model. Alternatively, if you do not have access to a model instance, you may [attach the model instance after the form is saved](/docs/forms/building-forms#saving-field-relationships-manually).

Optionally, you may pass a `type()`:

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags')
    ->type('categories')
    ->model(Post::find(1)),
```

The [type](https://spatie.be/docs/laravel-tags/v4/advanced-usage/using-types) allows you to group tags into collections.

The tags input supports all the customization options of the [original tags input component](/docs/forms/fields#tags-input).
