---
title: Adding a form to a Livewire component
---

## Setting up the Livewire component

First, generate a new Livewire component:

```bash
php artisan make:livewire CreatePost
```

Then, render your Livewire component on the page:

```blade
@livewire('create-post')
```

Alternatively, you can use a full-page Livewire component:

```php
use App\Http\Livewire\CreatePost;
use Illuminate\Support\Facades\Route;

Route::get('posts/create', CreatePost::class);
```

### Adding the form

There are 5 main tasks when adding a form to a Livewire component class. Each one is essential:

1) Implement the `HasForms` interface and use the `InteractsWithForms` trait.
2) Define a public Livewire property to store your form's data. In our example, we'll call this `$data`, but you can call it whatever you want.
3) Add a `form()` method, which is where you configure the form. [Add the form's schema](getting-started#form-schemas), and tell Filament to store the form data in the `$data` property (using `statePath('data')`).
4) Initialize the form with `$this->form->fill()` in `mount()`. This is imperative for every form that you build, even if it doesn't have any initial data.
5) Define a method to handle the form submission. In our example, we'll call this `create()`, but you can call it whatever you want. Inside that method, you can validate and get the form's data using `$this->form->getState()`. It's important that you use this method instead of accessing the `$this->data` property directly, because the form's data needs to be validated and transformed into a useful format before being returned.

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreatePost extends Component implements HasForms // [tl! focus]
{
    use InteractsWithForms; // [tl! focus:start]
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),
                MarkdownEditor::make('content'),
                // ...
            ])
            ->statePath('data');
    }
    
    public function create(): void
    {
        dd($this->form->getState());
    } // [tl! focus:start]
    
    public function render(): View
    {
        return view('create-post');
    }
}
```

Finally, in your Livewire component's view, render the form:

```blade
<div>
    <form wire:submit.prevent="create">
        {{ $this->form }}
        
        <button type="submit">
            Submit
        </button>
    </form>
    
    <x-filament-actions::modals />
</div>
```

> Note: `<x-filament-actions::modals />` is used to render form component [action modals](actions). The code can be put anywhere outside of the `<form>` element, as long as it's within the Livewire component.

Visit your Livewire component in the browser, and you should see the form components from `schema()`:

![](https://user-images.githubusercontent.com/41773797/147614478-5b40c645-107e-40ac-ba41-f0feb99dd480.png)

Submit the form with data, and you'll see the form's data dumped to the screen. You can save the data to a model instead of dumping it:

```php
use App\Models\Post;

public function create(): void
{
    Post::create($this->form->getState()); // [tl! focus]
}
```

## Initializing the form with data

To fill the form with data, just pass that data to the `$this->form->fill()` method. For example, if you're editing an existing post, you might do something like this:

```php
use App\Models\Post;

public function mount(Post $post): void
{
    $this->form->fill($post->toArray());
}
```

It's important that you use the `$this->form->fill()` method instead of assigning the data directly to the `$this->data` property. This is because the post's data needs to be internally transformed into a useful format before being stored.

## Setting a form model

Giving the `$form` access to a model is useful for a few reasons:

- It allows fields within that form to load information from that model. For example, select fields can [load their options from the database](fields/select#populating-automatically-from-a-relationship) automatically.
- The form can load and save the model's relationship data automatically. For example, you have an Edit Post form, with a [Repeater](fields/repeater#populating-automatically-from-a-relationship) which manages comments associated with that post. Filament will automatically load the comments for that post when you call `$this->form->fill([...])`, and save them back to the relationship when you call `$this->form->getState()`.
- Validation rules like `exists()` and `unique()` can automatically retrieve the database table name from the model.

It is advised to always pass the model to the form when there is one. As explained, it unlocks many new powers of the Filament form builder.

To pass the model to the form, use the `$form->model()` method:

```php
use App\Models\Post;
use Filament\Forms\Form;

public Post $post;

public function form(Form $form): Form
{
    return $form
        ->schema([
            // ...
        ])
        ->statePath('data')
        ->model($this->post);
}
```

### Passing the form model after the form has been submitted

In some cases, the form's model is not available until the form has been submitted. For example, in a Create Post form, the post does not exist until the form has been submitted. Therefore, you can't pass it in to `$form->model()`. However, you can pass a model class instead:

```php
use App\Models\Post;
use Filament\Forms\Form;

public function form(Form $form): Form
{
    return $form
        ->schema([
            // ...
        ])
        ->statePath('data')
        ->model(Post::class);
}
```

On its own, this isn't as powerful as passing a model instance. For example, relationships won't be saved to the post after it is created. To do that, you'll need to pass the post to the form after it has been created, and call `saveRelationships()` to save the relationships to it:

```php
use App\Models\Post;

public function create(): void
{
    $post = Post::create($this->form->getState());
    
    // Save the relationships from the form to the post after it is created.
    $this->form->model($post)->saveRelationships();
}
```

## Saving form data to individual properties

In all of our previous examples, we've been saving the form's data to the public `$data` property on the Livewire component. However, you can save the data to individual properties instead. For example, if you have a form with a `title` field, you can save the form's data to the `$title` property instead. To do this, don't pass a `statePath()` to the form at all. Ensure that all of your fields have their own **public** properties on the class.

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;

public ?string $title = null;

public ?string $content = null;

public function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('title')
                ->required(),
            MarkdownEditor::make('content'),
            // ...
        ]);
}
```

## Using multiple forms

By default, the `InteractsWithForms` trait only handles one form per Livewire component - `form()`. To add more forms to the Livewire component, you can define them in the `getForms()` method, and return an array containing the name of each form:

```php
protected function getForms(): array
{
    return [
        'editPostForm',
        'createCommentForm',
    ];
}
```

Each of these forms can now be defined within the Livewire component, using a method with the same name:

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;

public function editPostForm(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('title')
                ->required(),
            MarkdownEditor::make('content'),
            // ...
        ])
        ->statePath('postData')
        ->model($this->post);
}

public function createCommentForm(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')
                ->required(),
            TextInput::make('email')
                ->email()
                ->required(),
            MarkdownEditor::make('content')
                ->required(),
            // ...
        ])
        ->statePath('commentData')
        ->model(Comment::class);
}
```

Now, each form is addressable by its name instead of `form`. For example, to fill the post form, you can use `$this->editPostForm->fill([...])`, or to get the data from the comment form you can use `$this->createCommentForm->getState()`.

You'll notice that each form has its own unique `statePath()`. Each form will write it's state to a different array on your Livewire component, so it's important to define these:

```php
public ?array $postData = [];
public ?array $commentData = [];
```

## Resetting a form's data

You can reset a form back to its default data at any time by calling `$this->form->fill()`. For example, you may wish to clear the contents of a form every time it's submitted:

```php
use App\Models\Comment;

public function createComment(): void
{
    Comment::create($this->form->getState());

    // Reinitialize the form to clear its data.
    $this->form->fill();
}
```

## Generating form Livewire components with the CLI

It's advised that you learn how to set up a Livewire component with the form builder manually, but once you are confident, you can use the CLI to generate a form for you.

```bash
php artisan make:livewire-form RegistrationForm
```

This will generate a new `app/Http/Livewire/RegistrationForm.php` component, which you can customize.

### Generating a form for an Eloquent model

Filament is also able to generate forms for a specific Eloquent model. These are more powerful, as they will automatically save the data in the form for you, and [ensure the form fields are properly configured](#setting-a-form-model) to access that model.

When generating a form with the `make:livewire-form` command, it will ask for the name of the model:

```bash
php artisan make:livewire-form Products/CreateProduct
```

#### Generating an edit form for an Eloquent record

By default, passing a model to the `make:livewire-form` command will result in a form that creates a new record in your database. If you pass the `--edit` flag to the command, it will generate an edit form for a specific record. This will automatically fill the form with the data from the record, and save the data back to the model when the form is submitted.

```bash
php artisan make:livewire-form Products/EditProduct --edit
```

### Automatically generating form schemas

Filament is also able to guess which form fields you want in the schema, based on the model's database columns. The `doctrine/dbal` package is required to use this functionality:

```bash
composer require doctrine/dbal --dev
```

Now, you can use the `--generate` flag when generating your form:

```bash
php artisan make:livewire-form Products/CreateProduct --generate
```

> Note: If your table contains ENUM columns, `doctrine/dbal` is unable to scan your table and will crash. Hence Filament is unable to generate the schema for your table if it contains an ENUM column. Read more about this issue [here](https://github.com/doctrine/dbal/issues/3819#issuecomment-573419808).
