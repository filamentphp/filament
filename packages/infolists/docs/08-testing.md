---
title: Testing
---

## Overview

All examples in this guide will be written using [Pest](https://pestphp.com). To use Pest's Livewire plugin for testing, you can follow the installation instructions in the Pest documentation on plugins: [Livewire plugin for Pest](https://pestphp.com/docs/plugins#livewire). However, you can easily adapt this to PHPUnit.

Since the Infolist Builder works on Livewire components, you can use the [Livewire testing helpers](https://livewire.laravel.com/docs/testing). However, we have custom testing helpers that you can use with infolists:

## Actions

You can call an action by passing its infolist component key, and then the name of the action to `callInfolistAction()`:

```php
use function Pest\Livewire\livewire;

it('can send invoices', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callInfolistAction('customer', 'send', infolistName: 'infolist');

    expect($invoice->refresh())
        ->isSent()->toBeTrue();
});
```

To pass an array of data into an action, use the `data` parameter:

```php
use function Pest\Livewire\livewire;

it('can send invoices', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callInfolistAction('customer', 'send', data: [
            'email' => $email = fake()->email(),
        ])
        ->assertHasNoInfolistActionErrors();

    expect($invoice->refresh())
        ->isSent()->toBeTrue()
        ->recipient_email->toBe($email);
});
```

If you ever need to only set an action's data without immediately calling it, you can use `setInfolistActionData()`:

```php
use function Pest\Livewire\livewire;

it('can send invoices', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->mountInfolistAction('customer', 'send')
        ->setInfolistActionData('customer', 'send', data: [
            'email' => $email = fake()->email(),
        ])
});
```

### Execution

To check if an action has been halted, you can use `assertInfolistActionHalted()`:

```php
use function Pest\Livewire\livewire;

it('stops sending if invoice has no email address', function () {
    $invoice = Invoice::factory(['email' => null])->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callInfolistAction('customer', 'send')
        ->assertInfolistActionHalted('customer', 'send');
});
```

### Errors

`assertHasNoInfolistActionErrors()` is used to assert that no validation errors occurred when submitting the action form.

To check if a validation error has occurred with the data, use `assertHasInfolistActionErrors()`, similar to `assertHasErrors()` in Livewire:

```php
use function Pest\Livewire\livewire;

it('can validate invoice recipient email', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callInfolistAction('customer', 'send', data: [
            'email' => Str::random(),
        ])
        ->assertHasInfolistActionErrors(['email' => ['email']]);
});
```

To check if an action is pre-filled with data, you can use the `assertInfolistActionDataSet()` method:

```php
use function Pest\Livewire\livewire;

it('can send invoices to the primary contact by default', function () {
    $invoice = Invoice::factory()->create();
    $recipientEmail = $invoice->company->primaryContact->email;

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->mountInfolistAction('customer', 'send')
        ->assertInfolistActionDataSet([
            'email' => $recipientEmail,
        ])
        ->callMountedInfolistAction()
        ->assertHasNoInfolistActionErrors();
        
    expect($invoice->refresh())
        ->isSent()->toBeTrue()
        ->recipient_email->toBe($recipientEmail);
});
```

### Action state

To ensure that an action exists or doesn't in an infolist, you can use the `assertInfolistActionExists()` or  `assertInfolistActionDoesNotExist()` method:

```php
use function Pest\Livewire\livewire;

it('can send but not unsend invoices', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertInfolistActionExists('customer', 'send')
        ->assertInfolistActionDoesNotExist('customer', 'unsend');
});
```

To ensure an action is hidden or visible for a user, you can use the `assertInfolistActionHidden()` or `assertInfolistActionVisible()` methods:

```php
use function Pest\Livewire\livewire;

it('can only print customers', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertInfolistActionHidden('customer', 'send')
        ->assertInfolistActionVisible('customer', 'print');
});
```

To ensure an action is enabled or disabled for a user, you can use the `assertInfolistActionEnabled()` or `assertInfolistActionDisabled()` methods:

```php
use function Pest\Livewire\livewire;

it('can only print a customer for a sent invoice', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertInfolistActionDisabled('customer', 'send')
        ->assertInfolistActionEnabled('customer', 'print');
});
```

To check if an action is hidden to a user, you can use the `assertInfolistActionHidden()` method:

```php
use function Pest\Livewire\livewire;

it('can not send invoices', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertInfolistActionHidden('customer', 'send');
});
```

### Button appearance

To ensure an action has the correct label, you can use `assertInfolistActionHasLabel()` and `assertInfolistActionDoesNotHaveLabel()`:

```php
use function Pest\Livewire\livewire;

it('send action has correct label', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertInfolistActionHasLabel('customer', 'send', 'Email Invoice')
        ->assertInfolistActionDoesNotHaveLabel('customer', 'send', 'Send');
});
```

To ensure an action's button is showing the correct icon, you can use `assertInfolistActionHasIcon()` or `assertInfolistActionDoesNotHaveIcon()`:

```php
use function Pest\Livewire\livewire;

it('when enabled the send button has correct icon', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertInfolistActionEnabled('customer', 'send')
        ->assertInfolistActionHasIcon('customer', 'send', 'envelope-open')
        ->assertInfolistActionDoesNotHaveIcon('customer', 'send', 'envelope');
});
```

To ensure that an action's button is displaying the right color, you can use `assertInfolistActionHasColor()` or `assertInfolistActionDoesNotHaveColor()`:

```php
use function Pest\Livewire\livewire;

it('actions display proper colors', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertInfolistActionHasColor('customer', 'delete', 'danger')
        ->assertInfolistActionDoesNotHaveColor('customer', 'print', 'danger');
});
```

### URL

To ensure an action has the correct URL, you can use `assertInfolistActionHasUrl()`, `assertInfolistActionDoesNotHaveUrl()`, `assertInfolistActionShouldOpenUrlInNewTab()`, and `assertInfolistActionShouldNotOpenUrlInNewTab()`:

```php
use function Pest\Livewire\livewire;

it('links to the correct Filament sites', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertInfolistActionHasUrl('customer', 'filament', 'https://filamentphp.com/')
        ->assertInfolistActionDoesNotHaveUrl('customer', 'filament', 'https://github.com/filamentphp/filament')
        ->assertInfolistActionShouldOpenUrlInNewTab('customer', 'filament')
        ->assertInfolistActionShouldNotOpenUrlInNewTab('customer', 'github');
});
```
