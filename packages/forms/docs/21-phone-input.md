---
title: Phone input
---

## Introduction

The phone input allows the user to enter a phone number and choose its country. The number is formatted for its country as it is typed, and saved in the international [E.164](https://en.wikipedia.org/wiki/E.164) format:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
```

<AutoScreenshot name="forms/fields/phone-input/simple" alt="Phone input" />

The state of the field is a string such as `+5511987654321`, however the number was typed. A number that is incomplete or not valid for its country is saved as its digits, and fails [validation](#phone-input-validation).

A number that starts with `+` selects its country. When the input loses focus, a complete number is displayed in its national format, as the country's calling code is already shown on the country button.

The country is not saved. It is read from the number when the field is loaded. Where several countries share a calling code (such as `+1`), you will need a phone number library to tell them apart on the server.

The numbering rules come from [libphonenumber-js](https://gitlab.com/catamphetamine/libphonenumber-js). The script for the field is only loaded on pages that use it.

## Setting the default country

By default, no country is selected, so the user has to type the number with a `+` and its calling code. You may select a country while the field is empty using the `defaultCountry()` method, passing its [ISO 3166-1 alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2) code:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->defaultCountry('BR')
```

<UtilityInjection set="formFields">As well as allowing a static value, the `defaultCountry()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Choosing which countries are listed

The country button opens a list of every country, which can be searched by name, calling code or code. You may limit the list using the `countries()` method:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->countries(['BR', 'US', 'PT'])
```

<UtilityInjection set="formFields">As well as allowing a static value, the `countries()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

<AutoScreenshot name="forms/fields/phone-input/countries" alt="Phone input with its list of countries open" />

The `countries()` method only limits the list. A number from another country that is typed with its calling code is still accepted. To only accept some countries, [customize the validation pattern](#phone-input-validation).

### Listing preferred countries first

You may list some countries first, in the order given, using the `preferredCountries()` method. The other countries are listed after them, in alphabetical order:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->preferredCountries(['BR', 'US', 'PT'])
```

<UtilityInjection set="formFields">As well as allowing a static value, the `preferredCountries()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Customizing the locale

Country names are displayed and sorted in your app's locale. You may use a different locale using the `locale()` method:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->locale('pt_BR')
```

<UtilityInjection set="formFields">As well as allowing a static value, the `locale()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Allowing the number to be copied

You may add a button to copy the number to the clipboard using the `copyable()` method, as with the [text input](text-input#allowing-the-text-to-be-copied-to-the-clipboard):

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->copyable(copyMessage: 'Copied!', copyMessageDuration: 1500)
```

<UtilityInjection set="formFields">As well as allowing static values, the `copyable()` method also accepts functions to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

## Customizing the loading placeholder

The phone input displays a loading placeholder while its JavaScript is loaded and initialized. You may customize it using `loadingPlaceholder()`, passing a Blade view or an `Htmlable` instance, such as an `HtmlString` containing trusted HTML:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->loadingPlaceholder(fn () => view('forms.phone-input-loading-placeholder'))
```

<UtilityInjection set="formFields">As well as allowing a static value, the `loadingPlaceholder()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

Keep your placeholder HTML decorative and noninteractive. Filament hides it from assistive technologies and supplies an accessible loading status in the surrounding wrapper. If the JavaScript fails to load or initialize, the placeholder remains visible.

## Adding affix text aside the field

You may place text before and after the input using the `prefix()` and `suffix()` methods:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->suffix('Mobile')
```

<UtilityInjection set="formFields">As well as allowing static values, the `prefix()` and `suffix()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

### Using icons as affixes

You may place an [icon](../styling/icons) before and after the input using the `prefixIcon()` and `suffixIcon()` methods:

```php
use Filament\Forms\Components\PhoneInput;
use Filament\Support\Icons\Heroicon;

PhoneInput::make('phone')
    ->prefixIcon(Heroicon::Phone)
```

<UtilityInjection set="formFields">As well as allowing static values, the `prefixIcon()` and `suffixIcon()` methods also accept a function to dynamically calculate them. You can inject various utilities into the function as parameters.</UtilityInjection>

## Making the field read-only

Not to be confused with [disabling the field](overview#disabling-a-field), you may make the field "read-only" using the `readOnly()` method:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->readOnly()
```

The country cannot be changed while the field is read-only.

There are a few differences, compared to [`disabled()`](overview#disabling-a-field):

- When using `readOnly()`, the field will still be sent to the server when the form is submitted. It can be mutated with the browser console, or via JavaScript. You can use [`saved(false)`](overview#preventing-a-field-from-being-saved) to prevent this.
- There are no styling changes, such as less opacity, when using `readOnly()`.
- The field is still focusable when using `readOnly()`.

<UtilityInjection set="formFields">As well as allowing a static value, the `readOnly()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Phone input validation

As well as all rules listed on the [validation](validation) page, the phone input validates that the number is in the E.164 format. Anything else fails with Laravel's `regex` validation message.

The pattern only checks the shape of the number. It cannot tell a real number from a made-up one. If you need that, add your own [validation rule](validation#custom-rules) that uses a phone number library.

You may replace the pattern using the `telRegex()` method, as with the [text input](text-input#phone-number-validation). For example, to only accept Brazilian numbers:

```php
use Filament\Forms\Components\PhoneInput;

PhoneInput::make('phone')
    ->defaultCountry('BR')
    ->telRegex('/^\+55[0-9]{10,11}$/')
    ->validationMessages([
        'regex' => 'Enter a Brazilian phone number.',
    ])
```

<UtilityInjection set="formFields">As well as allowing a static value, the `telRegex()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>
