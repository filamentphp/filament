---
title: Testing imports
---

## Introduction

You can test your importer's row processing using `TestImporter`, and test submissions from an import action using `ImportAction::fake()`.

## Testing a row

`TestImporter` runs your application's importer, including column mapping, casting, validation, lifecycle hooks, and saving records and relationships, without uploading a CSV or dispatching import jobs:

```php
use App\Filament\Imports\ProductImporter;
use Filament\Actions\Testing\TestImporter;

it('imports a product', function () {
    $record = TestImporter::make(ProductImporter::class)->import([
        'sku' => 'MUG-001',
        'name' => 'Ceramic mug',
        'price' => '12.50',
    ])->assertImported()->getRecord();

    $this->assertDatabaseHas('products', [
        'id' => $record->getKey(),
        'sku' => 'MUG-001',
        'name' => 'Ceramic mug',
        'price' => 12.50,
    ]);
});
```

`assertImported()` checks that the importer completed without an exception and resolved a record. It does not guarantee database persistence, since your importer may customize `saveRecord()`. Use `getRecord()` with model or database assertions to check the saved values.

The helper runs your importer's database writes and other side effects without rolling them back, even when a row fails. Use your normal database isolation for tests. Test queued processing and completion notifications separately.

### Passing column mappings and options

By default, each importer column is mapped to a row key with the same name. Pass a `columnMap` to use different CSV headers, and `options` to test your [import options](../actions/import#using-import-options):

```php
use App\Filament\Imports\ProductImporter;
use App\Models\Product;
use Filament\Actions\Testing\TestImporter;

$product = Product::factory()->create(['sku' => 'MUG-001', 'name' => 'Ceramic mug']);

$record = TestImporter::make(ProductImporter::class, columnMap: [
    'sku' => 'Product code',
    'name' => 'Product name',
], options: [
    'updateExisting' => true,
])->import([
    'Product code' => 'MUG-001',
    'Product name' => 'Large ceramic mug',
])->assertImported()->getRecord();

expect($record->is($product))->toBeTrue();
expect($product->fresh()->name)->toBe('Large ceramic mug');
```

An explicit map replaces the default entirely; omitted columns remain unmapped. Options control only the behavior you implement in your importer.

`TestImporter` does not validate the column mapping or options forms, or apply options-form defaults. For example, `requiredMapping()` is enforced by the import action's form, not this helper. Test those requirements through the [import action](#testing-import-action-submissions).

### Providing import context

The helper creates an unsaved `Import` model by default. If your importer needs a particular import or associated user, pass your own model to `TestImporter::make(ProductImporter::class, import: $import)`. Associate its user using `$import->user()->associate($user)` and authenticate explicitly using `$this->actingAs($user)` when needed. The helper does not associate a user or change authentication for you.

## Asserting skipped rows

Use `assertSkipped()` when your importer's `resolveRecord()` returns `null`. For example, if your importer returns `null` for products that do not exist:

```php
use App\Filament\Imports\ProductImporter;
use Filament\Actions\Testing\TestImporter;

TestImporter::make(ProductImporter::class)->import([
    'sku' => 'MISSING-001',
])->assertSkipped();

$this->assertDatabaseMissing('products', ['sku' => 'MISSING-001']);
```

A skipped row completes without an exception and has no record. It is not a validation error or a deliberate row failure.

## Asserting validation errors

Use `assertHasErrors()` and `assertHasNoErrors()` to check validation errors, including those raised by lifecycle hooks:

```php
use App\Filament\Imports\ProductImporter;
use Filament\Actions\Testing\TestImporter;

TestImporter::make(ProductImporter::class)->import([
    'sku' => 'MUG-001',
    'name' => 'Ceramic mug',
    'price' => '-1',
])->assertHasErrors(['price' => 'min'])
    ->assertHasNoErrors(['name']);
```

Without arguments, these methods check for any validation errors or none. You can pass a field list such as `['price', 'name']`, or a field-to-rule map such as `['price' => ['numeric', 'min']]`. Only the specified fields or rules are checked. Use rule names without parameters, such as `'min'`, since rule parameters are not compared.

For column validation, use importer column names, not CSV headers or display labels. For example, assert `'price'` even when it is mapped to `'Unit price'`.

Exceptions created with `ValidationException::withMessages()` have no failed-rule metadata. Assert their field keys or exact messages. To check that a field has no errors, use `assertHasNoErrors(['custom'])`, since a rule-qualified negative assertion may pass despite a message on that field.

## Asserting deliberate row failures

Use `assertHasRowFailure()` to check for a `RowImportFailedException`. For example, the [updates-only importer](../actions/import#updating-existing-records-when-importing-only) can throw when no matching product exists:

```php
use App\Filament\Imports\ProductImporter;
use Filament\Actions\Testing\TestImporter;

TestImporter::make(ProductImporter::class)->import([
    'sku' => 'MISSING-001',
])->assertHasRowFailure('No product found with SKU [MISSING-001].');
```

Omit the message to check for any deliberate row failure, or pass a message to check an exact match. Use `assertHasNoRowFailure()` to check that none occurred.

Validation errors and deliberate row failures are separate: `assertHasNoErrors()` only checks validation, and `assertHasNoRowFailure()` only checks deliberate failures. Neither establishes an imported outcome; use `assertImported()` for that. Unexpected exceptions propagate to your test.

## Testing relationships

Use model assertions to check saved relationships. For example, for a `PostImporter` that [resolves authors by email](../actions/import#customizing-the-relationship-import-resolution):

```php
use App\Filament\Imports\PostImporter;
use App\Models\User;
use Filament\Actions\Testing\TestImporter;

it('associates the author matched by email', function () {
    User::factory()->create(['email' => 'grace@example.com']);
    $author = User::factory()->create(['email' => 'ada@example.com']);

    $record = TestImporter::make(PostImporter::class)->import([
        'title' => 'Importing posts',
        'content' => 'A practical guide',
        'author' => 'ada@example.com',
    ])->assertImported()->getRecord();

    expect($record->fresh()->author->is($author))->toBeTrue();
    $this->assertDatabaseCount('users', 2);
});
```

Also test your resolver's behavior for missing relationships, whether it rejects the row or creates a related record. For other hook side effects, use Laravel's fakes or database assertions, such as checking the record ID passed to a dispatched job.

## Testing import action submissions

Use `ImportAction::fake()` with the [action testing helpers](testing-actions) to check that your action requests an import without processing its rows. For a page with an `import` action using `ProductImporter` and its `updateExisting` option:

```php
use App\Filament\Imports\ProductImporter;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\User;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Http\UploadedFile;

use function Pest\Livewire\livewire;

it('requests a product import', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $imports = ImportAction::fake();

    livewire(ListProducts::class)
        ->mountAction('import')
        ->fillForm([
            'file' => UploadedFile::fake()->createWithContent(
                'products.csv',
                "Product code,Product name,Unit price\nMUG-001,Ceramic mug,12.50\n",
            ),
        ])
        ->fillForm([
            'columnMap' => ['sku' => 'Product code', 'name' => 'Product name', 'price' => 'Unit price'],
            'updateExisting' => true,
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $imports->assertDispatched(ProductImporter::class, function (Import $import, array $columnMap, array $options) use ($user): bool {
        return $import->user->is($user)
            && ($columnMap['sku'] === 'Product code')
            && ($options['updateExisting'] === true);
    })->assertDispatchedTimes(ProductImporter::class);
});
```

Upload the file before setting `columnMap`, so the form can read the headers and build its mapping fields. `fillForm()` preserves other form defaults. The callback receives the `Import` model, column map, and options merged from the action and form.

`assertDispatched()` checks for at least one request for the importer, optionally matching a callback. `assertDispatchedTimes()` checks its exact count, defaulting to one. Use `assertNothingDispatched()` after invalid form data or rejected authorization.

For example, if `sku` uses `requiredMapping()`, submit an otherwise valid form with `columnMap.sku` set to `null`, assert `assertHasFormErrors(['columnMap.sku' => 'required'])`, then `$imports->assertNothingDispatched()`. These are form errors, not row validation errors.

To test submission-time authorization, mount and fill a valid form while authorized, revoke permission, and invoke `->call('callMountedAction')` before asserting that nothing was dispatched. Visibility checks alone do not prove that submission is rejected.

The fake still reads the file, validates the form, and persists an `Import` record. It does not run import jobs, process rows, emit `ImportStarted` or `ImportCompleted`, or send completion notifications. It does not globally fake Laravel's bus or events: unrelated jobs and events, and your action hooks, still run. Use `TestImporter` separately to test row behavior.
