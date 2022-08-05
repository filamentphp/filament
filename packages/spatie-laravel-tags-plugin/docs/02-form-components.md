---
title: Form components
---

This guide assumes that you've already set up your model attach tags as per [Spatie's documentation](https://spatie.be/docs/laravel-tags/basic-usage/using-tags).

You may use the field in the same way as the [original tags input](/docs/forms/fields#tags-input) field:

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags'),
```

> The field will automatically load and save its tags to your model. To set this functionality up, **you must also follow the instructions set out in the [field relationships](/docs/forms/getting-started#field-relationships) section**. If you're using the [admin panel](/docs/admin), you can skip this step.

Optionally, you may pass a [`type()`](https://spatie.be/docs/laravel-tags/advanced-usage/using-types) allows you to group tags into collections:

```php
use Filament\Forms\Components\SpatieTagsInput;

SpatieTagsInput::make('tags')->type('categories'),
```

The tags input supports all the customization options of the [original tags input component](/docs/forms/fields#tags-input).
