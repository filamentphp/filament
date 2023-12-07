---
title: Radio
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

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

<AutoScreenshot name="forms/fields/radio/simple" alt="Radio" version="3.x" />

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

<AutoScreenshot name="forms/fields/radio/option-descriptions" alt="Radio with option descriptions" version="3.x" />

Be sure to use the same `key` in the descriptions array as the `key` in the option array so the right description matches the right option.

## Boolean options

If you want a simple boolean radio button group, with "Yes" and "No" options, you can use the `boolean()` method:

```php
Radio::make('feedback')
    ->label('Like this post??')
    ->boolean()
```

<AutoScreenshot name="forms/fields/radio/boolean" alt="Boolean radio" version="3.x" />

## Positioning the options inline with the label

You may wish to display the options `inline()` with the label instead of below it:

```php
Radio::make('feedback')
    ->label('Like this post??')
    ->boolean()
    ->inline()
```

<AutoScreenshot name="forms/fields/radio/inline" alt="Inline radio" version="3.x" />

## Positioning the options inline with each other but below the label

You may wish to display the options `inline()` with each other but below the label:

```php
Radio::make('feedback')
    ->label('Like this post??')
    ->boolean()
    ->inline()
    ->inlineLabel(false)
```

<AutoScreenshot name="forms/fields/radio/inline-under-label" alt="Inline radio under label" version="3.x" />

## Disabling specific options

You can disable specific options using the `disableOptionWhen()` method. It accepts a closure, in which you can check if the option with a specific `$value` should be disabled:

```php
use Filament\Forms\Components\Radio;

Radio::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published',
    ])
    ->disableOptionWhen(fn (string $value): bool => $value === 'published')
```

<AutoScreenshot name="forms/fields/radio/disabled-option" alt="Radio with disabled option" version="3.x" />

If you want to retrieve the options that have not been disabled, e.g. for validation purposes, you can do so using `getEnabledOptions()`:

```php
use Filament\Forms\Components\Radio;

Radio::make('status')
    ->options([
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'published' => 'Published',
    ])
    ->disableOptionWhen(fn (string $value): bool => $value === 'published')
    ->in(fn (Radio $component): array => array_keys($component->getEnabledOptions()))
```
