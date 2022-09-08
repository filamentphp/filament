---
title: Testing
---

All examples in this guide will be written using [Pest](https://pestphp.com). However, you can easily adapt this to a PHPUnit.

Since all pages in the admin panel are Livewire components, we're just using Livewire testing helpers everywhere. If you've never tested Livewire components before, please read [this guide](https://laravel-livewire.com/docs/testing) from the Livewire docs.

## Getting started

Ensure that you are authenticated to access the admin panel in your `TestCase`:

```php
protected function setUp(): void
{
    parent::setUp();

    $this->actingAs(User::factory()->create());
}
```

## Resources

### Pages

#### List

##### Routing & render

To ensure that the List page for the `PostResource` is able to render successfully, generate a page URL, perform a request to this URL and ensure that it is successful:

```php
it('can render page', function () {
    $this->get(PostResource::getUrl('index'))->assertSuccessful();
});
```

##### Table

Filament includes a selection of helpers for testing tables. A full guide to testing tables can be found [in the Table Builder documentation](../tables/testing).

To use a table [testing helper](../tables/testing), make assertions on the resource's List page class, which holds the table:

```php
use function Pest\Livewire\livewire;

it('can list posts', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts);
});
```

#### Create

##### Routing & render

To ensure that the Create page for the `PostResource` is able to render successfully, generate a page URL, perform a request to this URL and ensure that it is successful:

```php
it('can render page', function () {
    $this->get(PostResource::getUrl('create'))->assertSuccessful();
});
```

##### Creating

You may check that data is correctly saved into the database by calling `fillForm()` with your form data, and then asserting that the database contains a matching record:

```php
use function Pest\Livewire\livewire;

it('can create', function () {
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
    ]);
});
```

##### Validation

Use `assertHasFormErrors()` to ensure that data is properly validated in a form:

```php
use function Pest\Livewire\livewire;

it('can validate input', function () {
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm([
            'title' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});
```

#### Edit

##### Routing & render

To ensure that the Edit page for the `PostResource` is able to render successfully, generate a page URL, perform a request to this URL and ensure that it is successful:

```php
it('can render page', function () {
    $this->get(PostResource::getUrl('edit', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});
```

##### Filling existing data

To check that the form is filled with the correct data from the database, you may `assertFormSet()` that the data in the form matches that of the record:

```php
use function Pest\Livewire\livewire;

it('can retrieve data', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertFormSet([
            'author_id' => $post->author->getKey(),
            'content' => $post->content,
            'tags' => $post->tags,
            'title' => $post->title,
        ]);
});
```

##### Saving

You may check that data is correctly saved into the database by calling `fillForm()` with your form data, and then asserting that the database contains a matching record:

```php
use function Pest\Livewire\livewire;

it('can save', function () {
    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh())
        ->author->toBeSameModel($newData->author)
        ->content->toBe($newData->content)
        ->tags->toBe($newData->tags)
        ->title->toBe($newData->title);
});
```

##### Validation

Use `assertHasFormErrors()` to ensure that data is properly validated in a form:

```php
use function Pest\Livewire\livewire;

it('can validate input', function () {
    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'title' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['title' => 'required']);
});
```

##### Deleting

You can test the `DeleteAction` using `callPageAction()`:

```php
use Filament\Pages\Actions\DeleteAction;
use function Pest\Livewire\livewire;

it('can delete', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->callPageAction(DeleteAction::class);

    $this->assertModelMissing($post);
});
```

You can ensure that a particular user is not able to see a `DeleteAction` using `assertPageActionHidden()`:

```php
use Filament\Pages\Actions\DeleteAction;
use function Pest\Livewire\livewire;

it('can not delete', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertPageActionHidden(DeleteAction::class);

    $this->assertModelMissing($post);
});
```

#### View

##### Routing & render

To ensure that the View page for the `PostResource` is able to render successfully, generate a page URL, perform a request to this URL and ensure that it is successful:

```php
it('can render page', function () {
    $this->get(PostResource::getUrl('view', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});
```

##### Filling existing data

To check that the form is filled with the correct data from the database, you may `assertSet()` that the data in the form matches that of the record:

```php
use function Pest\Livewire\livewire;

it('can retrieve data', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ViewPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertFormSet([
            'author_id' => $post->author->getKey(),
            'content' => $post->content,
            'tags' => $post->tags,
            'title' => $post->title,
        ]);
});
```

### Relation managers

##### Render

To ensure that a relation manager is able to render successfully, mount the Livewire component:

```php
use function Pest\Livewire\livewire;

it('can render relation manager', function () {
    $category = Category::factory()
        ->has(Post::factory()->count(10))
        ->create();

    livewire(CategoryResource\RelationManagers\PostsRelationManager::class, [
        'ownerRecord' => $category,
    ])
        ->assertSuccessful();
});
```

##### Table

Filament includes a selection of helpers for testing tables. A full guide to testing tables can be found [in the Table Builder documentation](../tables/testing).

To use a table [testing helper](../tables/testing), make assertions on the relation manager class, which holds the table:

```php
use function Pest\Livewire\livewire;

it('can list posts', function () {
    $category = Category::factory()
        ->has(Post::factory()->count(10))
        ->create();

    livewire(CategoryResource\RelationManagers\PostsRelationManager::class, [
        'ownerRecord' => $category,
    ])
        ->assertCanSeeTableRecords($category->posts);
});
```

## Page actions

You can call a [page action](pages/actions) by passing its name or class to `callPageAction()`:

```php
use function Pest\Livewire\livewire;

it('can send invoices', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callPageAction('send');

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
        ->callPageAction('send', data: [
            'email' => $email = fake()->email(),
        ])
        ->assertHasNoPageActionErrors();

    expect($invoice->refresh())
        ->isSent()->toBeTrue()
        ->recipient_email->toBe($email);
});
```

`assertHasNoPageActionErrors()` is used to assert that no validation errors occurred when submitting the action form.

To check if a validation error has occurred with the data, use `assertHasPageActionErrors()`, similar to `assertHasErrors()` in Livewire:

```php
use function Pest\Livewire\livewire;

it('can validate invoice recipient email', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->callPageAction('send', data: [
            'email' => Str::random(),
        ])
        ->assertHasPageActionErrors(['email' => ['email']]);
});
```

To check if a page action is pre-filled with data, you can use the `assertPageActionDataSet()` method:

```php
use function Pest\Livewire\livewire;

it('can send invoices to the primary contact by default', function () {
    $invoice = Invoice::factory()->create();
    $recipientEmail = $invoice->company->primaryContact->email;

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->mountPageAction('send')
        ->assertPageActionDataSet([
            'email' => $recipientEmail,
        ])
        ->callMountedPageAction()
        ->assertHasNoPageActionErrors();
        
    expect($invoice->refresh())
        ->isSent()->toBeTrue()
        ->recipient_email->toBe($recipientEmail);
});
```

To check if a page action is hidden to a user, you can use the `assertPageActionHidden()` method:

```php
use function Pest\Livewire\livewire;

it('can not send invoices', function () {
    $invoice = Invoice::factory()->create();

    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])
        ->assertPageActionHidden('send');
});
```
