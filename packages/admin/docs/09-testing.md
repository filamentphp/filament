---
title: Testing
---

All examples in this guide will be written using [Pest](https://pestphp.com).

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

#### Create

##### Routing & render

To ensure that the Create page for the `PostResource` is able to render successfully, generate a page URL, perform a request to this URL and ensure that it is successful:

```php
it('can render page', function () {
    $this->get(PostResource::getUrl('create'))->assertSuccessful();
});
```

##### Creating

You may check that data is correctly saved into the database by `set()`ting each property of the form and then asserting that the database contains an identical record:

```php
use function Pest\Livewire\livewire;

it('can create', function () {
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\CreatePost::class)
        ->set('data.author_id', $newData->author->getKey())
        ->set('data.content', $newData->content)
        ->set('data.tags', $newData->tags)
        ->set('data.title', $newData->title)
        ->call('create');

    $this->assertDatabaseHas(Post::class, [
        'author_id' => $newData->author->getKey(),
        'content' => $newData->content,
        'tags' => json_encode($newData->tags),
        'title' => $newData->title,
    ]);
});
```

##### Validation

Livewire provides users with `assertHasErrors()` to ensure that data is properly validated in a form:

```php
use function Pest\Livewire\livewire;

it('can validate input', function () {
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\CreatePost::class)
        ->set('data.title', null)
        ->call('create')
        ->assertHasErrors(['data.title' => 'required']);
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

To check that the form is filled with the correct data from the database, you may `assertSet()` that the data in the form matches that of the record:

```php
use function Pest\Livewire\livewire;

it('can retrieve data', function () {
    $post = Post::factory()->create();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertSet('data.author_id', $post->author->getKey())
        ->assertSet('data.content', $post->content)
        ->assertSet('data.tags', $post->tags)
        ->assertSet('data.title', $post->title);
});
```

##### Saving

You may check that data is correctly saved into the database by `set()`ting each property of the form and then asserting that the database contains an identical record:

```php
use function Pest\Livewire\livewire;

it('can save', function () {
    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->set('data.author_id', $newData->author->getKey())
        ->set('data.content', $newData->content)
        ->set('data.tags', $newData->tags)
        ->set('data.title', $newData->title)
        ->call('save');

    expect($post->refresh())
        ->author->toBeSameModel($newData->author)
        ->content->toBe($newData->content)
        ->tags->toBe($newData->tags)
        ->title->toBe($newData->title);
});
```

##### Validation

Livewire provides users with `assertHasErrors()` to ensure that data is properly validated in a form:

```php
use function Pest\Livewire\livewire;

it('can validate input', function () {
    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->set('data.title', null)
        ->call('save')
        ->assertHasErrors(['data.title' => 'required']);
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
        ->assertSet('data.author_id', $post->author->getKey())
        ->assertSet('data.content', $post->content)
        ->assertSet('data.tags', $post->tags)
        ->assertSet('data.title', $post->title);
});
```
