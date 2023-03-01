---
title: Validation
---

## Getting started

Validation rules may be added to any [field](fields).

In Laravel, validation rules are usually defined in arrays like `['required', 'max:255']` or a combined string like `required|max:255`. This is fine if you're exclusively working in the backend with simple form requests. But Filament is also able to give your users frontend validation, so they can fix their mistakes before any backend requests are made.

Filament includes several [dedicated validation methods](#available-rules), but you can also use any [other Laravel validation rules](#other-rules), including [custom validation rules](#custom-rules).

> Beware that some validations rely on the field name and therefore won't work when passed via `->rule()`/`->rules()`. Use the dedicated validation methods whenever you can.

## Available rules

### Active URL

The field must have a valid A or AAAA record according to the `dns_get_record()` PHP function. [See the Laravel documentation](https://laravel.com/docs/validation#rule-active-url)

```php
Field::make('name')->activeUrl()
```

### After (date)

The field value must be a value after a given date. [See the Laravel documentation](https://laravel.com/docs/validation#rule-after)

```php
Field::make('startDate')->after('tomorrow')
```

Alternatively, you may pass the name of another field to compare against:

```php
Field::make('startDate')
Field::make('endDate')->after('startDate')
```

### After or equal to (date)

The field value must be a date after or equal to the given date. [See the Laravel documentation](https://laravel.com/docs/validation#rule-after-or-equal)

```php
Field::make('startDate')->afterOrEqual('tomorrow')
```

Alternatively, you may pass the name of another field to compare against:

```php
Field::make('startDate')
Field::make('endDate')->afterOrEqual('startDate')
```

### Alpha

The field must be entirely alphabetic characters. [See the Laravel documentation](https://laravel.com/docs/validation#rule-alpha)

```php
Field::make('name')->alpha()
```

### Alpha Dash

The field may have alpha-numeric characters, as well as dashes and underscores. [See the Laravel documentation](https://laravel.com/docs/validation#rule-alpha-dash)

```php
Field::make('name')->alphaDash()
```

### Alpha Numeric

The field must be entirely alpha-numeric characters. [See the Laravel documentation](https://laravel.com/docs/validation#rule-alpha-num)

```php
Field::make('name')->alphaNum()
```

### Before (date)

The field value must be a date before a given date. [See the Laravel documentation](https://laravel.com/docs/validation#rule-before)

```php
Field::make('startDate')->before('first day of next month')
```

Alternatively, you may pass the name of another field to compare against:

```php
Field::make('startDate')->before('endDate')
Field::make('endDate')
```

### Before or equal to (date)

The field value must be a date before or equal to the given date. [See the Laravel documentation](https://laravel.com/docs/validation#rule-before-or-equal)

```php
Field::make('startDate')->beforeOrEqual('end of this month')
```

Alternatively, you may pass the name of another field to compare against:

```php
Field::make('startDate')->beforeOrEqual('endDate')
Field::make('endDate')
```

### Confirmed

The field must have a matching field of `{field}_confirmation`. [See the Laravel documentation](https://laravel.com/docs/validation#rule-confirmed)

```php
Field::make('password')->confirmed()
Field::make('password_confirmation')
```

### Different

The field value must be different to another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-different)

```php
Field::make('backupEmail')->different('email')
```

### Doesnt Start With

The field must not start with one of the given values. [See the Laravel documentation](https://laravel.com/docs/validation#rule-doesnt-start-with)

```php
Field::make('name')->doesntStartWith(['admin'])
```

### Doesnt End With

The field must not end with one of the given values. [See the Laravel documentation](https://laravel.com/docs/validation#rule-doesnt-end-with)

```php
Field::make('name')->doesntEndWith(['admin'])
```

### Ends With

The field must end with one of the given values. [See the Laravel documentation](https://laravel.com/docs/validation#rule-ends-with)

```php
Field::make('name')->endsWith(['bot'])
```

### Enum

The field must contain a valid enum value. [See the Laravel documentation](https://laravel.com/docs/validation#rule-enum)

```php
Field::make('status')->enum(MyStatus::class)
```

### Exists

The field value must exist in the database. [See the Laravel documentation](https://laravel.com/docs/validation#rule-exists).

```php
Field::make('invitation')->exists()
```

By default, the form's model will be searched, [if it is registered](adding-a-form-to-a-livewire-component#setting-a-form-model). You may specify a custom table name or model to search:

```php
use App\Models\Invitation;

Field::make('invitation')->exists(table: Invitation::class)
```

By default, the field name will be used as the column to search. You may specify a custom column to search:

```php
Field::make('invitation')->exists(column: 'id')
```

You can further customize the rule by passing a [closure](advanced#closure-customization) to the `callback` parameter:

```php
use Illuminate\Validation\Rules\Exists;

Field::make('invitation')
    ->exists(callback: function (Exists $rule) {
        return $rule->where('is_active', 1);
    })
```

### Filled

The field must not be empty when it is present. [See the Laravel documentation](https://laravel.com/docs/validation#rule-filled)

```php
Field::make('name')->filled()
```

### Greater than

The field value must be greater than another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-gt)

```php
Field::make('newNumber')->gt('oldNumber')
```

### Greater than or equal to

The field value must be greater than or equal to another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-gte)

```php
Field::make('newNumber')->gte('oldNumber')
```

### In
The field must be included in the given list of values. [See the Laravel documentation](https://laravel.com/docs/validation#rule-in)

```php
Field::make('status')->in(['pending', 'completed'])
```

### Ip Address

The field must be an IP address. [See the Laravel documentation](https://laravel.com/docs/validation#rule-ip)

```php
Field::make('ip_address')->ip()
Field::make('ip_address')->ipv4()
Field::make('ip_address')->ipv6()
```

### JSON

The field must be a valid JSON string. [See the Laravel documentation](https://laravel.com/docs/validation#rule-json)

```php
Field::make('ip_address')->json()
```

### Less than

The field value must be less than another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-lt)

```php
Field::make('newNumber')->lt('oldNumber')
```

### Less than or equal to

The field value must be less than or equal to another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-lte)

```php
Field::make('newNumber')->lte('oldNumber')
```

### Mac Address

The field must be a MAC address. [See the Laravel documentation](https://laravel.com/docs/validation#rule-mac)

```php
Field::make('mac_address')->macAddress()
```

### Multiple Of

The field must be a multiple of value. [See the Laravel documentation](https://laravel.com/docs/validation#multiple-of)

```php
Field::make('number')->multipleOf(2)
```

### Not In

The field must not be included in the given list of values. [See the Laravel documentation](https://laravel.com/docs/validation#rule-not-in)

```php
Field::make('status')->notIn(['cancelled', 'rejected'])
```

### Not Regex

The field must not match the given regular expression. [See the Laravel documentation](https://laravel.com/docs/validation#rule-not-regex)

```php
Field::make('email')->notRegex('/^.+$/i')
```

### Nullable

The field value can be empty. This rule is applied by default if the `required` rule is not present. [See the Laravel documentation](https://laravel.com/docs/validation#rule-nullable)

```php
Field::make('name')->nullable()
```

### Prohibited

The field value must be empty. [See the Laravel documentation](https://laravel.com/docs/validation#rule-prohibited)

```php
Field::make('name')->prohibited()
```

### Required

The field value must not be empty. [See the Laravel documentation](https://laravel.com/docs/validation#rule-required)

```php
Field::make('name')->required()
```

### Required With

The field value must not be empty _only if_ any of the other specified fields are not empty. [See the Laravel documentation](https://laravel.com/docs/validation#rule-required-with)

```php
Field::make('name')->requiredWith('field,another_field')
```

### Required With All

The field value must not be empty _only if_ all of the other specified fields are not empty. [See the Laravel documentation](https://laravel.com/docs/validation#rule-required-with-all)

```php
Field::make('name')->requiredWithAll('field,another_field')
```

### Required Without

The field value must not be empty _only when_ any of the other specified fields are empty. [See the Laravel documentation](https://laravel.com/docs/validation#rule-required-without)

```php
Field::make('name')->requiredWithout('field,another_field')
```

### Required Without All

The field value must not be empty _only when_ all of the other specified fields are empty. [See the Laravel documentation](https://laravel.com/docs/validation#rule-required-without-all)

```php
Field::make('name')->requiredWithoutAll('field,another_field')
```

### Regex

The field must match the given regular expression. [See the Laravel documentation](https://laravel.com/docs/validation#rule-regex)

```php
Field::make('email')->regex('/^.+@.+$/i')
```

### Same

The field value must be the same as another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-same)

```php
Field::make('password')->same('passwordConfirmation')
```

### Starts With

The field must start with one of the given values. [See the Laravel documentation](https://laravel.com/docs/validation#rule-starts-with)

```php
Field::make('name')->startsWith(['a'])
```

### String

The field must be a string. [See the Laravel documentation](https://laravel.com/docs/validation#rule-string)
```php
Field::make('name')->string()
```

### Unique

The field value must not exist in the database. [See the Laravel documentation](https://laravel.com/docs/validation#rule-unique)

```php
Field::make('email')->unique()
```

By default, the form's model will be searched, [if it is registered](adding-a-form-to-a-livewire-component#setting-a-form-model). You may specify a custom table name or model to search:

```php
use App\Models\User;

Field::make('email')->unique(table: User::class)
```

By default, the field name will be used as the column to search. You may specify a custom column to search:

```php
Field::make('email')->unique(column: 'email_address')
```

Sometimes, you may wish to ignore a given model during unique validation. For example, consider an "update profile" form that includes the user's name, email address, and location. You will probably want to verify that the email address is unique. However, if the user only changes the name field and not the email field, you do not want a validation error to be thrown because the user is already the owner of the email address in question.

```php
Field::make('email')->unique(ignorable: $ignoredUser)
```

If you're using the [app framework](../app), you can easily ignore the current record by using `ignoreRecord` instead:

```php
Field::make('email')->unique(ignoreRecord: true)
```

You can further customize the rule by passing a [closure](advanced#closure-customization) to the `callback` parameter:

```php
use Illuminate\Validation\Rules\Unique;

Field::make('email')
    ->unique(callback: function (Unique $rule) {
        return $rule->where('is_active', 1);
    })
```

### UUID

The field must be a valid RFC 4122 (version 1, 3, 4, or 5) universally unique identifier (UUID). [See the Laravel documentation](https://laravel.com/docs/validation#rule-uuid)

```php
Field::make('identifer')->uuid()
```

## Other rules

You may add other validation rules to any field using the `rules()` method:

```php
TextInput::make('slug')->rules(['alpha_dash'])
```

A full list of validation rules may be found in the [Laravel documentation](https://laravel.com/docs/validation#available-validation-rules).

## Custom rules

You may use any custom validation rules as you would do in [Laravel](https://laravel.com/docs/validation#custom-validation-rules):

```php
TextInput::make('slug')->rules([new Uppercase()])
```

You may also use [closure rules](https://laravel.com/docs/validation#using-closures):

```php
TextInput::make('slug')->rules([
    function () {
        return function (string $attribute, $value, Closure $fail) {
            if ($value === 'foo') {
                $fail("The {$attribute} is invalid.");
            }
        };
    },
])
```

## Validation attributes

When fields fail validation, their label is used in the error message. To customize the label used in field error messages, use the `validationAttribute()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')->validationAttribute('full name')
```

## Sending validation notifications

If you want to send a notification when validation error occurs, you may do so by using the `onValidationError()` method on your Livewire component:

```php
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

protected function onValidationError(ValidationException $exception): void
{
    Notification::make()
        ->title($exception->getMessage())
        ->danger()
        ->send();
}
```

Alternatively, if you are using the app framework and you want this behaviour on all the pages, add this inside the `boot()` method of your `AppServiceProvider`:

```php
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Validation\ValidationException;

Page::$reportValidationErrorUsing = function (ValidationException $exception) {
    Notification::make()
        ->title($exception->getMessage())
        ->danger()
        ->send();
};
```
