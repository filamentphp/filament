---
title: Validation
---

## Getting started

Validation rules may be added to any [field](fields).

Filament includes several [dedicated validation methods](#available-rules), but you can also use any [other Laravel validation rules](#other-rules), including [custom validation rules](#custom-rules).

> Beware that some validations rely on the field name and therefore won't work when passed via `->rule()`/`->rules()`. Use the dedicated validation methods whenever you can.

## Available rules

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

### Different

The field value must be different to another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-different)

```php
Field::make('backupEmail')->different('email')
```

### Exists

The field value must exist in the database. [See the Laravel documentation](https://laravel.com/docs/validation#rule-exists).

```php
Field::make('invitation')->exists()
```

By default, the form's model will be searched, [if it is registered](getting-started#registering-a-model). You may specify a custom table name or model to search:

```php
use App\Models\Invitation;

Field::make('invitation')->exists(table: Invitation::class)
```

By default, the field name will be used as the column to search. You may specify a custom column to search:

```php
Field::make('invitation')->exists(column: 'id')
```

You can further customize the rule by passing a [closure](advanced#closure-customisation) to the `callback` parameter:

```php
use Illuminate\Validation\Rules\Exists;

Field::make('invitation')
    ->exists(callback: function (Exists $rule) {
        return $rule->where('is_active', 1);
    })
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

### Nullable

The field value can be empty. This rule is applied by default if the `required` rule is not present. [See the Laravel documentation](https://laravel.com/docs/validation#rule-nullable)

```php
Field::make('name')->nullable()
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

### Same

The field value must be the same as another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-same)

```php
Field::make('password')->same('passwordConfirmation')
```

### Unique

The field value must not exist in the database. [See the Laravel documentation](https://laravel.com/docs/validation#rule-unique)

```php
Field::make('email')->unique()
```

By default, the form's model will be searched, [if it is registered](#registering-a-model). You may specify a custom table name or model to search:

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

If you're using the [admin panel](/docs/admin), you can easily ignore the current record by using `ignoreRecord` instead:

```php
Field::make('email')->unique(ignoreRecord: true)
```

You can further customize the rule by passing a [closure](advanced#closure-customisation) to the `callback` parameter:

```php
use Illuminate\Validation\Rules\Unique;

Field::make('email')
    ->unique(callback: function (Unique $rule) {
        return $rule->where('is_active', 1);
    })
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
