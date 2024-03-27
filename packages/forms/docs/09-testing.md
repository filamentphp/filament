---
title: Testing
---

## Overview

All examples in this guide will be written using [Pest](https://pestphp.com). However, you can easily adapt this to PHPUnit.

Since the Form Builder works on Livewire components, you can use the [Livewire testing helpers](https://livewire.laravel.com/docs/testing). However, we have custom testing helpers that you can use with forms:

## Filling a form

To fill a form with data, pass the data to `fillForm()`:

```php
use Livewire\Livewire;

Livewire::test(CreatePost::class)
    ->fillForm([
        'title' => fake()->sentence(),
        // ...
    ]);
```

> If you have multiple forms on a Livewire component, you can specify which form you want to fill using `fillForm([...], 'createPostForm')`.

To check that a form has data, use `assertFormSet()`:

```php
use Illuminate\Support\Str;
use Livewire\Livewire;

it('can automatically generate a slug from the title', function () {
    $title = fake()->sentence();

    Livewire::test(CreatePost::class)
        ->fillForm([
            'title' => $title,
        ])
        ->assertFormSet([
            'slug' => Str::slug($title),
        ]);
});
```

> If you have multiple forms on a Livewire component, you can specify which form you want to check using `assertFormSet([...], 'createPostForm')`.

## Validation

Use `assertHasFormErrors()` to ensure that data is properly validated in a form:

```php
use Livewire\Livewire;

it('can validate input', function () {
    Livewire::test(CreatePost::class)
        ->fillForm([
            'title' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});
```

And `assertHasNoFormErrors()` to ensure there are no validation errors:

```php
use Livewire\Livewire;

Livewire::test(CreatePost::class)
    ->fillForm([
        'title' => fake()->sentence(),
        // ...
    ])
    ->call('create')
    ->assertHasNoFormErrors();
```

> If you have multiple forms on a Livewire component, you can pass the name of a specific form as the second parameter like `assertHasFormErrors(['title' => 'required'], 'createPostForm')` or `assertHasNoFormErrors([], 'createPostForm')`.

## Form existence

To check that a Livewire component has a form, use `assertFormExists()`:

```php
use Livewire\Livewire;

it('has a form', function () {
    Livewire::test(CreatePost::class)
        ->assertFormExists();
});
```

> If you have multiple forms on a Livewire component, you can pass the name of a specific form like `assertFormExists('createPostForm')`.

## Fields

To ensure that a form has a given field, pass the field name to `assertFormFieldExists()`:

```php
use Livewire\Livewire;

it('has a title field', function () {
    Livewire::test(CreatePost::class)
        ->assertFormFieldExists('title');
});
```

You may pass a function as an additional argument in order to assert that a field passes a given "truth test". This is useful for asserting that a field has a specific configuration:

```php
use Livewire\Livewire;

it('has a title field', function () {
    Livewire::test(CreatePost::class)
        ->assertFormFieldExists('title', function (TextInput $field): bool {
            return $field->isDisabled();
        });
});
```

> If you have multiple forms on a Livewire component, you can specify which form you want to check for the existence of the field like `assertFormFieldExists('title', 'createPostForm')`.

### Hidden fields

To ensure that a field is visible, pass the name to `assertFormFieldIsVisible()`:

```php
use Livewire\Livewire;

test('title is visible', function () {
    Livewire::test(CreatePost::class)
        ->assertFormFieldIsVisible('title');
});
```

Or to ensure that a field is hidden you can pass the name to `assertFormFieldIsHidden()`:

```php
use Livewire\Livewire;

test('title is hidden', function () {
    Livewire::test(CreatePost::class)
        ->assertFormFieldIsHidden('title');
});
```

> For both `assertFormFieldIsHidden()` and `assertFormFieldIsVisible()` you can pass the name of a specific form the field belongs to as the second argument like `assertFormFieldIsHidden('title', 'createPostForm')`.

### Disabled fields

To ensure that a field is enabled, pass the name to `assertFormFieldIsEnabled()`:

```php
use Livewire\Livewire;

test('title is enabled', function () {
    Livewire::test(CreatePost::class)
        ->assertFormFieldIsEnabled('title');
});
```

Or to ensure that a field is disabled you can pass the name to `assertFormFieldIsDisabled()`:

```php
use Livewire\Livewire;

test('title is disabled', function () {
    Livewire::test(CreatePost::class)
        ->assertFormFieldIsDisabled('title');
});
```

> For both `assertFormFieldIsEnabled()` and `assertFormFieldIsDisabled()` you can pass the name of a specific form the field belongs to as the second argument like `assertFormFieldIsEnabled('title', 'createPostForm')`.

## Actions

You can call an action by passing its form component name, and then the name of the action to `callFormComponentAction()`:

```php
use Livewire\Livewire;

it('can send invoices', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callFormComponentAction('customer_id', 'send');

    expect($invoice->refresh())
        ->isSent()->toBeTrue();
});
```

To pass an array of data into an action, use the `data` parameter:

```php
use Livewire\Livewire;

it('can send invoices', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callFormComponentAction('customer_id', 'send', data: [
            'email' => $email = fake()->email(),
        ])
        ->assertHasNoFormComponentActionErrors();

    expect($invoice->refresh())
        ->isSent()->toBeTrue()
        ->recipient_email->toBe($email);
});
```

If you ever need to only set an action's data without immediately calling it, you can use `setFormComponentActionData()`:

```php
use Livewire\Livewire;

it('can send invoices', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->mountFormComponentAction('customer_id', 'send')
        ->setFormComponentActionData('customer_id', 'send', data: [
            'email' => $email = fake()->email(),
        ])
});
```

### Execution

To check if an action has been halted, you can use `assertFormComponentActionHalted()`:

```php
use Livewire\Livewire;

it('stops sending if invoice has no email address', function () {
    $invoice = Invoice::factory(['email' => null])->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callFormComponentAction('customer_id', 'send')
        ->assertFormComponentActionHalted('customer_id', 'send');
});
```

### Errors

`assertHasNoFormComponentActionErrors()` is used to assert that no validation errors occurred when submitting the action form.

To check if a validation error has occurred with the data, use `assertHasFormComponentActionErrors()`, similar to `assertHasErrors()` in Livewire:

```php
use Livewire\Livewire;

it('can validate invoice recipient email', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callFormComponentAction('customer_id', 'send', data: [
            'email' => Str::random(),
        ])
        ->assertHasFormComponentActionErrors(['email' => ['email']]);
});
```

To check if an action is pre-filled with data, you can use the `assertFormComponentActionDataSet()` method:

```php
use Livewire\Livewire;

it('can send invoices to the primary contact by default', function () {
    $invoice = Invoice::factory()->create();
    $recipientEmail = $invoice->company->primaryContact->email;

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->mountFormComponentAction('customer_id', 'send')
        ->assertFormComponentActionDataSet([
            'email' => $recipientEmail,
        ])
        ->callMountedFormComponentAction()
        ->assertHasNoFormComponentActionErrors();
        
    expect($invoice->refresh())
        ->isSent()->toBeTrue()
        ->recipient_email->toBe($recipientEmail);
});
```

### Action state

To ensure that an action exists or doesn't in a form, you can use the `assertFormComponentActionExists()` or  `assertFormComponentActionDoesNotExist()` method:

```php
use Livewire\Livewire;

it('can send but not unsend invoices', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertFormComponentActionExists('customer_id', 'send')
        ->assertFormComponentActionDoesNotExist('customer_id', 'unsend');
});
```

To ensure an action is hidden or visible for a user, you can use the `assertFormComponentActionHidden()` or `assertFormComponentActionVisible()` methods:

```php
use Livewire\Livewire;

it('can only print customers', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertFormComponentActionHidden('customer_id', 'send')
        ->assertFormComponentActionVisible('customer_id', 'print');
});
```

To ensure an action is enabled or disabled for a user, you can use the `assertFormComponentActionEnabled()` or `assertFormComponentActionDisabled()` methods:

```php
use Livewire\Livewire;

it('can only print a customer for a sent invoice', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertFormComponentActionDisabled('customer_id', 'send')
        ->assertFormComponentActionEnabled('customer_id', 'print');
});
```

To check if an action is hidden to a user, you can use the `assertFormComponentActionHidden()` method:

```php
use Livewire\Livewire;

it('can not send invoices', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertFormComponentActionHidden('customer_id', 'send');
});
```

### Button appearance

To ensure an action has the correct label, you can use `assertFormComponentActionHasLabel()` and `assertFormComponentActionDoesNotHaveLabel()`:

```php
use Livewire\Livewire;

it('send action has correct label', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertFormComponentActionHasLabel('customer_id', 'send', 'Email Invoice')
        ->assertFormComponentActionDoesNotHaveLabel('customer_id', 'send', 'Send');
});
```

To ensure an action's button is showing the correct icon, you can use `assertFormComponentActionHasIcon()` or `assertFormComponentActionDoesNotHaveIcon()`:

```php
use Livewire\Livewire;

it('when enabled the send button has correct icon', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertFormComponentActionEnabled('customer_id', 'send')
        ->assertFormComponentActionHasIcon('customer_id', 'send', 'envelope-open')
        ->assertFormComponentActionDoesNotHaveIcon('customer_id', 'send', 'envelope');
});
```

To ensure that an action's button is displaying the right color, you can use `assertFormComponentActionHasColor()` or `assertFormComponentActionDoesNotHaveColor()`:

```php
use Livewire\Livewire;

it('actions display proper colors', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertFormComponentActionHasColor('customer_id', 'delete', 'danger')
        ->assertFormComponentActionDoesNotHaveColor('customer_id', 'print', 'danger');
});
```

### URL

To ensure an action has the correct URL, you can use `assertFormComponentActionHasUrl()`, `assertFormComponentActionDoesNotHaveUrl()`, `assertFormComponentActionShouldOpenUrlInNewTab()`, and `assertFormComponentActionShouldNotOpenUrlInNewTab()`:

```php
use Livewire\Livewire;

it('links to the correct Filament sites', function () {
    $invoice = Invoice::factory()->create();

    Livewire::test(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertFormComponentActionHasUrl('customer_id', 'filament', 'https://filamentphp.com/')
        ->assertFormComponentActionDoesNotHaveUrl('customer_id', 'filament', 'https://github.com/filamentphp/filament')
        ->assertFormComponentActionShouldOpenUrlInNewTab('customer_id', 'filament')
        ->assertFormComponentActionShouldNotOpenUrlInNewTab('customer_id', 'github');
});
```
