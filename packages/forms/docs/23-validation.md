---
title: Validation
---
import Aside from "@components/Aside.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Validation rules may be added to any [field](overview#available-fields).

In Laravel, validation rules are usually defined in arrays like `['required', 'max:255']` or a combined string like `required|max:255`. This is fine if you're exclusively working in the backend with simple form requests. But Filament is also able to give your users frontend validation, so they can fix their mistakes before any backend requests are made.

Filament includes many [dedicated validation methods](#available-rules), but you can also use any [other Laravel validation rules](#other-rules), including [custom validation rules](#custom-rules).

<Aside variant="warning">
    Some default Laravel validation rules rely on the correct attribute names and won't work when passed via `rule()`/`rules()`. Use the dedicated validation methods whenever you can.
</Aside>

## Available rules

### Active URL

The field must have a valid A or AAAA record according to the `dns_get_record()` PHP function. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-active-url)

```php
Field::make('name')->activeUrl()
```

### After (date)

The field value must be a value after a given date. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-after)

```php
Field::make('start_date')->after('tomorrow')
```

Alternatively, you may pass the name of another field to compare against:

```php
Field::make('start_date')
Field::make('end_date')->after('start_date')
```

### After or equal to (date)

The field value must be a date after or equal to the given date. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-after-or-equal)

```php
Field::make('start_date')->afterOrEqual('tomorrow')
```

Alternatively, you may pass the name of another field to compare against:

```php
Field::make('start_date')
Field::make('end_date')->afterOrEqual('start_date')
```

### Alpha

The field must be entirely alphabetic characters. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-alpha)

```php
Field::make('name')->alpha()
```

### Alpha Dash

The field may have alphanumeric characters, as well as dashes and underscores. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-alpha-dash)

```php
Field::make('name')->alphaDash()
```

### Alpha Numeric

The field must be entirely alphanumeric characters. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-alpha-num)

```php
Field::make('name')->alphaNum()
```

### ASCII

The field must be entirely 7-bit ASCII characters. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-ascii)

```php
Field::make('name')->ascii()
```

### Before (date)

The field value must be a date before a given date. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-before)

```php
Field::make('start_date')->before('first day of next month')
```

Alternatively, you may pass the name of another field to compare against:

```php
Field::make('start_date')->before('end_date')
Field::make('end_date')
```

### Before or equal to (date)

The field value must be a date before or equal to the given date. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-before-or-equal)

```php
Field::make('start_date')->beforeOrEqual('end of this month')
```

Alternatively, you may pass the name of another field to compare against:

```php
Field::make('start_date')->beforeOrEqual('end_date')
Field::make('end_date')
```

### Confirmed

The field must have a matching field of `{field}_confirmation`. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-confirmed)

```php
Field::make('password')->confirmed()
Field::make('password_confirmation')
```

### Different

The field value must be different to another. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-different)

```php
Field::make('backup_email')->different('email')
```

### Doesn't Start With

The field must not start with one of the given values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-doesnt-start-with)

```php
Field::make('name')->doesntStartWith(['admin'])
```

### Doesn't End With

The field must not end with one of the given values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-doesnt-end-with)

```php
Field::make('name')->doesntEndWith(['admin'])
```

### Ends With

The field must end with one of the given values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-ends-with)

```php
Field::make('name')->endsWith(['bot'])
```

### Enum

The field must contain a valid enum value. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-enum)

```php
Field::make('status')->enum(MyStatus::class)
```

### Exists

The field value must exist in the database. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-exists)

```php
Field::make('invitation')->exists()
```

By default, the form's model will be searched, [if it is registered](../components/form#setting-a-form-model). You may specify a custom table name or model to search:

```php
use App\Models\Invitation;

Field::make('invitation')->exists(table: Invitation::class)
```

By default, the field name will be used as the column to search. You may specify a custom column to search:

```php
Field::make('invitation')->exists(column: 'id')
```

You can further customize the rule by passing a [closure](overview#closure-customization) to the `modifyRuleUsing` parameter:

```php
use Illuminate\Validation\Rules\Exists;

Field::make('invitation')
    ->exists(modifyRuleUsing: function (Exists $rule) {
        return $rule->where('is_active', 1);
    })
```

Laravel's `exists` validation rule does not use the Eloquent model to query the database by default, so it will not use any global scopes defined on the model, including for soft-deletes. As such, even if there is a soft-deleted record with the same value, the validation will pass.

Since global scopes are not applied, Filament's multi-tenancy feature also does not scope the query to the current tenant by default.

To do this, you should use the `scopedExists()` method instead, which replaces Laravel's `exists` implementation with one that uses the model to query the database, applying any global scopes defined on the model, including for soft-deletes and multi-tenancy:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('email')
    ->scopedExists()
```

If you would like to modify the Eloquent query used to check for presence, including to remove a global scope, you can pass a function to the `modifyQueryUsing` parameter:

```php
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

TextInput::make('email')
    ->scopedExists(modifyQueryUsing: function (Builder $query) {
        return $query->withoutGlobalScope(SoftDeletingScope::class);
    })
```

### Filled

The field must not be empty when it is present. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-filled)

```php
Field::make('name')->filled()
```

### Greater than

The field value must be greater than another. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-gt)

```php
Field::make('newNumber')->gt('oldNumber')
```

### Greater than or equal to

The field value must be greater than or equal to another. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-gte)

```php
Field::make('newNumber')->gte('oldNumber')
```

### Hex color

The field value must be a valid color in hexadecimal format. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-hex-color)

```php
Field::make('color')->hexColor()
```

### In

The field must be included in the given list of values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-in)

```php
Field::make('status')->in(['pending', 'completed'])
```

The [toggle buttons](toggle-buttons), [checkbox list](checkbox-list), [radio](radio) and [select](select#valid-options-validation-in-rule) fields automatically apply the `in()` rule based on their available options, so you do not need to add it manually.

### Ip Address

The field must be an IP address. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-ip)

```php
Field::make('ip_address')->ip()
Field::make('ip_address')->ipv4()
Field::make('ip_address')->ipv6()
```

### JSON

The field must be a valid JSON string. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-json)

```php
Field::make('ip_address')->json()
```

### Less than

The field value must be less than another. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-lt)

```php
Field::make('newNumber')->lt('oldNumber')
```

### Less than or equal to

The field value must be less than or equal to another. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-lte)

```php
Field::make('newNumber')->lte('oldNumber')
```

### Mac Address

The field must be a MAC address. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-mac)

```php
Field::make('mac_address')->macAddress()
```

### Multiple Of

The field must be a multiple of value. [See the Laravel documentation.](https://laravel.com/docs/validation#multiple-of)

```php
Field::make('number')->multipleOf(2)
```

### Not In

The field must not be included in the given list of values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-not-in)

```php
Field::make('status')->notIn(['cancelled', 'rejected'])
```

### Not Regex

The field must not match the given regular expression. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-not-regex)

```php
Field::make('email')->notRegex('/^.+$/i')
```

### Nullable

The field value can be empty. This rule is applied by default if the `required` rule is not present. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-nullable)

```php
Field::make('name')->nullable()
```

### Prohibited

The field value must be empty. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-prohibited)

```php
Field::make('name')->prohibited()
```

### Prohibited If

The field must be empty *only if* the other specified field has any of the given values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-prohibited-if)

```php
Field::make('name')->prohibitedIf('field', 'value')
```

### Prohibited Unless

The field must be empty *unless* the other specified field has any of the given values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-prohibited-unless)

```php
Field::make('name')->prohibitedUnless('field', 'value')
```

### Prohibits

If the field is not empty, all other specified fields must be empty. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-prohibits)

```php
Field::make('name')->prohibits('field')

Field::make('name')->prohibits(['field', 'another_field'])
```

### Required

The field value must not be empty. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-required)

```php
Field::make('name')->required()
```

#### Marking a field as required

By default, required fields will show an asterisk `*` next to their label. You may want to hide the asterisk on forms where all fields are required, or where it makes sense to add a [hint](#adding-a-hint-next-to-the-label) to optional fields instead:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->required() // Adds validation to ensure the field is required
    ->markAsRequired(false) // Removes the asterisk
```

If your field is not `required()`, but you still wish to show an asterisk `*` you can use `markAsRequired()` too:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->markAsRequired()
```

### Required If

The field value must not be empty _only if_ the other specified field has any of the given values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-required-if)

```php
Field::make('name')->requiredIf('field', 'value')
```

### Required If Accepted

The field value must not be empty _only if_ the other specified field is equal to "yes", "on", 1, "1", true, or "true". [See the Laravel documentation.](https://laravel.com/docs/validation#rule-required-if-accepted)

```php
Field::make('name')->requiredIfAccepted('field')
```

### Required Unless

The field value must not be empty _unless_ the other specified field has any of the given values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-required-unless)

```php
Field::make('name')->requiredUnless('field', 'value')
```

### Required With

The field value must not be empty _only if_ any of the other specified fields are not empty. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-required-with)

```php
Field::make('name')->requiredWith('field,another_field')
```

### Required With All

The field value must not be empty _only if_ all the other specified fields are not empty. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-required-with-all)

```php
Field::make('name')->requiredWithAll('field,another_field')
```

### Required Without

The field value must not be empty _only when_ any of the other specified fields are empty. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-required-without)

```php
Field::make('name')->requiredWithout('field,another_field')
```

### Required Without All

The field value must not be empty _only when_ all the other specified fields are empty. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-required-without-all)

```php
Field::make('name')->requiredWithoutAll('field,another_field')
```

### Regex

The field must match the given regular expression. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-regex)

```php
Field::make('email')->regex('/^.+@.+$/i')
```

### Same

The field value must be the same as another. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-same)

```php
Field::make('password')->same('passwordConfirmation')
```

### Starts With

The field must start with one of the given values. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-starts-with)

```php
Field::make('name')->startsWith(['a'])
```

### String

The field must be a string. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-string)
```php
Field::make('name')->string()
```

### Unique

The field value must not exist in the database. [See the Laravel documentation.](https://laravel.com/docs/validation#rule-unique)

```php
Field::make('email')->unique()
```

If your Filament form already has an Eloquent model associated with it, such as in a [panel resource](../resources), Filament will use that. You may also specify a custom table name or model to search:

```php
use App\Models\User;

Field::make('email')->unique(table: User::class)
```

By default, the field name will be used as the column to search. You may specify a custom column to search:

```php
Field::make('email')->unique(column: 'email_address')
```

Usually, you wish to ignore a given model during unique validation. For example, consider an "update profile" form that includes the user's name, email address, and location. You will probably want to verify that the email address is unique. However, if the user only changes the name field and not the email field, you do not want a validation error to be thrown because the user is already the owner of the email address in question. If your Filament form already has an Eloquent model associated with it, such as in a [panel resource](../resources), Filament will ignore it.

To prevent Filament from ignoring the current Eloquent record, you can pass `false` to the `ignoreRecord` parameter:

```php
Field::make('email')->unique(ignoreRecord: false)
```

Alternatively, to ignore an Eloquent record of your choice, you can pass it to the `ignorable` parameter:

```php
Field::make('email')->unique(ignorable: $ignoredUser)
```

You can further customize the rule by passing a [closure](overview#closure-customization) to the `modifyRuleUsing` parameter:

```php
use Illuminate\Validation\Rules\Unique;

Field::make('email')
    ->unique(modifyRuleUsing: function (Unique $rule) {
        return $rule->where('is_active', 1);
    })
```

Laravel's `unique` validation rule does not use the Eloquent model to query the database by default, so it will not use any global scopes defined on the model, including for soft-deletes. As such, even if there is a soft-deleted record with the same value, the validation will fail.

Since global scopes are not applied, Filament's multi-tenancy feature also does not scope the query to the current tenant by default.

To do this, you should use the `scopedUnique()` method instead, which replaces Laravel's `unique` implementation with one that uses the model to query the database, applying any global scopes defined on the model, including for soft-deletes and multi-tenancy:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('email')
    ->scopedUnique()
```

If you would like to modify the Eloquent query used to check for uniqueness, including to remove a global scope, you can pass a function to the `modifyQueryUsing` parameter:

```php
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

TextInput::make('email')
    ->scopedUnique(modifyQueryUsing: function (Builder $query) {
        return $query->withoutGlobalScope(SoftDeletingScope::class);
    })
```

### ULID

The field under validation must be a valid [Universally Unique Lexicographically Sortable Identifier](https://github.com/ulid/spec) (ULID). [See the Laravel documentation.](https://laravel.com/docs/validation#rule-ulid)

```php
Field::make('identifier')->ulid()
```

### UUID

The field must be a valid RFC 4122 (version 1, 3, 4, or 5) universally unique identifier (UUID). [See the Laravel documentation.](https://laravel.com/docs/validation#rule-uuid)

```php
Field::make('identifier')->uuid()
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
use Closure;

TextInput::make('slug')->rules([
    fn (): Closure => function (string $attribute, $value, Closure $fail) {
        if ($value === 'foo') {
            $fail('The :attribute is invalid.');
        }
    },
])
```

You may [inject utilities](overview#field-utility-injection) like [`$get`](overview#injecting-the-state-of-another-field) into your custom rules, for example if you need to reference other field values in your form. To do this, wrap the closure rule in another function that returns it:

```php
use Filament\Schemas\Components\Utilities\Get;

TextInput::make('slug')->rules([
    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
        if ($get('other_field') === 'foo' && $value !== 'bar') {
            $fail("The {$attribute} is invalid.");
        }
    },
])
```

## Customizing validation attributes

When fields fail validation, their label is used in the error message. To customize the label used in field error messages, use the `validationAttribute()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->validationAttribute('full name')
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `validationAttribute()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

## Validation messages

By default Laravel's validation error message is used. To customize the error messages, use the `validationMessages()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('email')
    ->unique(// ...)
    ->validationMessages([
        'unique' => 'The :attribute has already been registered.',
    ])
```

<UtilityInjection set="formFields" version="4.x">As well as allowing an array of static value, the `validationMessages()` method also accepts a function for each message. You can inject various utilities into the functions as parameters.</UtilityInjection>

### Allowing HTML in validation messages

By default, validation messages are rendered as plain text to prevent XSS attacks. However, you may need to render HTML in your validation messages, such as when displaying lists or links. To enable HTML rendering for validation messages, use the `allowHtmlValidationMessages()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('password')
    ->required()
    ->rules([
        new CustomRule(), // Custom rule that returns a validation message that contains HTML
    ])
    ->allowHtmlValidationMessages()
```

Be aware that you will need to ensure that the HTML in all validation messages is safe to render, otherwise your application will be vulnerable to XSS attacks.

## Disabling validation when fields are not saved

When a field is [not saved](overview#preventing-a-field-from-being-saved), it is still validated. To disable validation for fields that are not saved, use the `validatedWhenNotDehydrated()` method:

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->required()
    ->saved(false)
    ->validatedWhenNotDehydrated(false)
```

<UtilityInjection set="formFields" version="4.x">As well as allowing a static value, the `validatedWhenNotDehydrated()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>
