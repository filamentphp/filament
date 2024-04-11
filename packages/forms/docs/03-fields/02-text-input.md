---
title: Text input
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

The text input allows you to interact with a string:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
```

<AutoScreenshot name="forms/fields/text-input/simple" alt="Text input" version="3.x" />

## Setting the HTML input type

You may set the type of string using a set of methods. Some, such as `email()`, also provide validation:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('text')
    ->email() // or
    ->numeric() // or
    ->integer() // or
    ->password() // or
    ->tel() // or
    ->url()
```

You may instead use the `type()` method to pass another [HTML input type](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#input_types):

```php
use Filament\Forms\Components\TextInput;

TextInput::make('backgroundColor')
    ->type('color')
```

## Setting the HTML input mode

You may set the [`inputmode` attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#inputmode) of the input using the `inputMode()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('text')
    ->numeric()
    ->inputMode('decimal')
```

## Setting the numeric step

You may set the [`step` attribute](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#step) of the input using the `step()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('number')
    ->numeric()
    ->step(100)
```

## Autocompleting text

You may allow the text to be [autocompleted by the browser](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#autocomplete) using the `autocomplete()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password')
    ->password()
    ->autocomplete('new-password')
```

As a shortcut for `autocomplete="off"`, you may use `autocomplete(false)`:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password')
    ->password()
    ->autocomplete(false)
```

For more complex autocomplete options, text inputs also support [datalists](#autocompleting-text-with-a-datalist).

### Autocompleting text with a datalist

You may specify [datalist](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/datalist) options for a text input using the `datalist()` method:

```php
TextInput::make('manufacturer')
    ->datalist([
        'BWM',
        'Ford',
        'Mercedes-Benz',
        'Porsche',
        'Toyota',
        'Tesla',
        'Volkswagen',
    ])
```

Datalists provide autocomplete options to users when they use a text input. However, these are purely recommendations, and the user is still able to type any value into the input. If you're looking to strictly limit users to a set of predefined options, check out the [select field](select).

## Autocapitalizing text

You may allow the text to be [autocapitalized by the browser](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#autocapitalize) using the `autocapitalize()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->autocapitalize('words')
```

## Adding affix text aside the field

You may place text before and after the input using the `prefix()` and `suffix()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('domain')
    ->prefix('https://')
    ->suffix('.com')
```

<AutoScreenshot name="forms/fields/text-input/affix" alt="Text input with affixes" version="3.x" />

### Using icons as affixes

You may place an [icon](https://blade-ui-kit.com/blade-icons?set=1#search) before and after the input using the `prefixIcon()` and `suffixIcon()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('domain')
    ->url()
    ->suffixIcon('heroicon-m-globe-alt')
```

<AutoScreenshot name="forms/fields/text-input/suffix-icon" alt="Text input with suffix icon" version="3.x" />

#### Setting the affix icon's color

Affix icons are gray by default, but you may set a different color using the `prefixIconColor()` and `suffixIconColor()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('domain')
    ->url()
    ->suffixIcon('heroicon-m-check-circle')
    ->suffixIconColor('success')
```

## Revealable password inputs

When using `password()`, you can also make the input `revealable()`, so that the user can see a plain text version of the password they're typing by clicking a button:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password')
    ->password()
    ->revealable()
```

<AutoScreenshot name="forms/fields/text-input/revealable-password" alt="Text input with revealable password" version="3.x" />

## Input masking

Input masking is the practice of defining a format that the input value must conform to.

In Filament, you may use the `mask()` method to configure an [Alpine.js mask](https://alpinejs.dev/plugins/mask#x-mask):

```php
use Filament\Forms\Components\TextInput;

TextInput::make('birthday')
    ->mask('99/99/9999')
    ->placeholder('MM/DD/YYYY')
```

To use a [dynamic mask](https://alpinejs.dev/plugins/mask#mask-functions), wrap the JavaScript in a `RawJs` object:

```php
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

TextInput::make('cardNumber')
    ->mask(RawJs::make(<<<'JS'
        $input.startsWith('34') || $input.startsWith('37') ? '9999 999999 99999' : '9999 9999 9999 9999'
    JS))
```

Alpine.js will send the entire masked value to the server, so you may need to strip certain characters from the state before validating the field and saving it. You can do this with the `stripCharacters()` method, passing in a character or an array of characters to remove from the masked value:

```php
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

TextInput::make('amount')
    ->mask(RawJs::make('$money($input)'))
    ->stripCharacters(',')
    ->numeric()
```

## Making the field read-only

Not to be confused with [disabling the field](getting-started#disabling-a-field), you may make the field "read-only" using the `readOnly()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->readOnly()
```

There are a few differences, compared to [`disabled()`](getting-started#disabling-a-field):

- When using `readOnly()`, the field will still be sent to the server when the form is submitted. It can be mutated with the browser console, or via JavaScript. You can use [`dehydrated(false)`](advanced#preventing-a-field-from-being-dehydrated) to prevent this.
- There are no styling changes, such as less opacity, when using `readOnly()`.
- The field is still focusable when using `readOnly()`.

## Text input validation

As well as all rules listed on the [validation](../validation) page, there are additional rules that are specific to text inputs.

### Length validation

You may limit the length of the input by setting the `minLength()` and `maxLength()` methods. These methods add both frontend and backend validation:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->minLength(2)
    ->maxLength(255)
```

You can also specify the exact length of the input by setting the `length()`. This method adds both frontend and backend validation:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('code')
    ->length(8)
```

### Size validation

You may validate the minimum and maximum value of a numeric input by setting the `minValue()` and `maxValue()` methods:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('number')
    ->numeric()
    ->minValue(1)
    ->maxValue(100)
```

### Phone number validation

When using a `tel()` field, the value will be validated using: `/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/`.

If you wish to change that, then you can use the `telRegex()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('phone')
    ->tel()
    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
```

Alternatively, to customize the `telRegex()` across all fields, use a service provider:

```php
use Filament\Forms\Components\TextInput;

TextInput::configureUsing(function (TextInput $component): void {
    $component->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/');
});
```
