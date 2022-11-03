---
title: Radio
---

The radio input provides a radio button group for selecting a single value from a list of predefined options:

```php
use Filament\Forms\Components\Radio;

Radio::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published'
    ])
```

![](https://user-images.githubusercontent.com/41773797/147613206-644bd6a4-4814-4a99-b398-d03625179bfa.png)

## Setting option descriptions

You can optionally provide descriptions to each option using the `descriptions()` method:

```php
use Filament\Forms\Components\Radio;

Radio::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published'
    ])
    ->descriptions([
        'draft' => 'Is not visible.',
        'scheduled' => 'Will be visible.',
        'published' => 'Is visible.'
    ])
```

![](https://user-images.githubusercontent.com/41773797/147613223-0114ad7e-091c-43cf-b156-67cd0aaf5c0c.png)

Be sure to use the same `key` in the descriptions array as the `key` in the options array so the right description matches the right option.

## Boolean options

If you want a simple boolean radio button group, with "Yes" and "No" options, you can use the `boolean()` method:

```php
Radio::make('feedback')
    ->label('Do you like this post?')
    ->boolean()
```

![](https://user-images.githubusercontent.com/41773797/147613274-04745624-3ddd-46bb-b25c-1e756d6f4958.png)

## Positioning the options inline with the label

You may wish to display the options `inline()` with the label:

```php
Radio::make('feedback')
    ->label('Do you like this post?')
    ->boolean()
    ->inline()
```

![](https://user-images.githubusercontent.com/41773797/147709853-198d54fb-1bb1-4e82-87d0-3034b9152f0e.png)
