---
title: Textarea
---
import AutoScreenshot from "@components/AutoScreenshot.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

The textarea allows you to interact with a multi-line string:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
```

<AutoScreenshot name="forms/fields/textarea/simple" alt="Textarea" version="4.x" />

## Resizing the textarea

You may change the size of the textarea by defining the `rows()` and `cols()` methods:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->rows(10)
    ->cols(20)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `rows()` and `cols()` methods also accept functions to dynamically calculate them. You can inject various utilities into the functions as parameters.</UtilityInjection>

### Autosizing the textarea

You may allow the textarea to automatically resize to fit its content by setting the `autosize()` method:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->autosize()
```

Optionally, you may pass a boolean value to control if the textarea should be autosizeable or not:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->autosize(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `autosize()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Making the field read-only

Not to be confused with [disabling the field](overview#disabling-a-field), you may make the field "read-only" using the `readOnly()` method:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->readOnly()
```

There are a few differences, compared to [`disabled()`](overview#disabling-a-field):

- When using `readOnly()`, the field will still be sent to the server when the form is submitted. It can be mutated with the browser console, or via JavaScript. You can use [`dehydrated(false)`](overview#preventing-a-field-from-being-dehydrated) to prevent this.
- There are no styling changes, such as less opacity, when using `readOnly()`.
- The field is still focusable when using `readOnly()`.

Optionally, you may pass a boolean value to control if the field should be read-only or not:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->readOnly(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `readOnly()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Disabling Grammarly checks

If the user has Grammarly installed and you would like to prevent it from analyzing the contents of the textarea, you can use the `disableGrammarly()` method:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->disableGrammarly()
```

Optionally, you may pass a boolean value to control if the field should disable Grammarly checks or not:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->disableGrammarly(FeatureFlag::active())
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `disableGrammarly()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Trimming whitespace

You can automatically trim whitespace from the beginning and end of the textarea value using the `trim()` method:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->trim()
```

You may want to enable trimming globally for all textareas, similar to Laravel's `TrimStrings` middleware. You can do this in a service provider using the `configureUsing()` method:

```php
use Filament\Forms\Components\Textarea;

Textarea::configureUsing(function (Textarea $component): void {
    $component->trim();
});
```

## Textarea validation

As well as all rules listed on the [validation](validation) page, there are additional rules that are specific to textareas.

### Length validation

You may limit the length of the textarea by setting the `minLength()` and `maxLength()` methods. These methods add both frontend and backend validation:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->minLength(2)
    ->maxLength(1024)
```

You can also specify the exact length of the textarea by setting the `length()`. This method adds both frontend and backend validation:

```php
use Filament\Forms\Components\Textarea;

Textarea::make('question')
    ->length(100)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing static values, the `minLength()`, `maxLength()` and `length()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>
