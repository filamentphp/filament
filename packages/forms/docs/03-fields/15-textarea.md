---
title: Textarea
---

The textarea allows you to interact with a multi-line string:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
```

![](https://user-images.githubusercontent.com/41773797/147614131-e3db8d23-5045-4e0e-8de4-30823a4af362.png)

## Resizing the textarea

You may change the size of the textarea by defining the `rows()` and `cols()` methods:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->rows(10)
    ->cols(20)
```

## Validation

You may limit the length of the content by setting the `minLength()` and `maxLength()` methods. These methods add both frontend and backend validation:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->minLength(50)
    ->maxLength(500)
```
