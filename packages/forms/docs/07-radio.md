---
title: Radio
---
import Aside from "@components/Aside.astro"
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

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

<UtilityInjection set="formFields" version="4.x">As well as allowing a static array, the `options()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/radio/simple" alt="Radio" version="4.x" />

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

<UtilityInjection set="formFields" version="4.x">As well as allowing a static array, the `descriptions()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/radio/option-descriptions" alt="Radio with option descriptions" version="4.x" />

<Aside variant="info">
    Be sure to use the same `key` in the descriptions array as the `key` in the option array so the right description matches the right option.
</Aside>

## Positioning the options inline with each other

You may wish to display the options `inline()` with each other:

```php
use Filament\Forms\Components\Radio;

Radio::make('feedback')
    ->label('Like this post?')
    ->boolean()
    ->inline()
```

<AutoScreenshot name="forms/fields/radio/inline" alt="Inline toggle buttons" version="4.x" />

Optionally, you may pass a boolean value to control if the options should be inline or not:

```php
use Filament\Forms\Components\Radio;

Radio::make('feedback')
    ->label('Like this post?')
    ->boolean()
    ->inline(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `inline()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

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

<UtilityInjection set="formFields" version="4.x" extras="Option value;;mixed;;$value;;The value of the option to disable.||Option label;;string | Illuminate\Contracts\Support\Htmlable;;$label;;The label of the option to disable.">You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/radio/disabled-option" alt="Radio with disabled option" version="4.x" />

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

For more information about the `in()` function, please see the [Validation documentation](validation#in).

## Boolean options

If you want a simple boolean radio button group, with "Yes" and "No" options, you can use the `boolean()` method:

```php
use Filament\Forms\Components\Radio;

Radio::make('feedback')
    ->label('Like this post?')
    ->boolean()
```

<AutoScreenshot name="forms/fields/radio/boolean" alt="Boolean radio" version="4.x" />

To customize the "Yes" label, you can use the `trueLabel` argument on the `boolean()` method:

```php
use Filament\Forms\Components\Radio;

Radio::make('feedback')
    ->label('Like this post?')
    ->boolean(trueLabel: 'Absolutely!')
```

To customize the "No" label, you can use the `falseLabel` argument on the `boolean()` method:

```php
use Filament\Forms\Components\Radio;

Radio::make('feedback')
    ->label('Like this post?')
    ->boolean(falseLabel: 'Not at all!')
```
