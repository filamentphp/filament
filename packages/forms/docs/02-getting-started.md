---
title: Getting started
---

<iframe width="560" height="315" src="https://www.youtube.com/embed/Kab21689E5A" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

## Preparing your Livewire component

Implement the `HasForms` interface and use the `InteractsWithForms` trait:

```php
<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms // [tl! focus]
{
    use Forms\Concerns\InteractsWithForms; // [tl! focus]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

In your Livewire component's view, render the form:

```blade
<form wire:submit.prevent="submit">
    {{ $this->form }}
    
    <button type="submit">
        Submit
    </button>
</form>
```

Finally, add any [fields](fields) and [layout components](layout) to the Livewire component's `getFormSchema()` method:

```php
<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public Post $post;
    
    public $title;
    public $content;
    
    public function mount(): void
    {
        $this->form->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
    }
    
    protected function getFormSchema(): array // [tl! focus:start]
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            // ...
        ];
    } // [tl! focus:end]
    
    public function submit(): void
    {
        // ...
    }
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

Visit your Livewire component in the browser, and you should see the form components from `getFormSchema()`.

![](https://user-images.githubusercontent.com/41773797/147614478-5b40c645-107e-40ac-ba41-f0feb99dd480.png)

## Initializing forms

You must initialize forms when the Livewire component is first loaded. This is done with the `fill()` form method, often called in the `mount()` method of the Livewire component.

For your fields to hold data, they should have a corresponding property on your Livewire component, just as in Livewire normally.

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreatePost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public $title = '';
    public $content = '';
    
    public function mount(): void // [tl! focus:start]
    {
        $this->form->fill();
    } // [tl! focus:end]
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->default('Status Update') // [tl! focus]
                ->required(),
            Forms\Components\MarkdownEditor::make('content'),
        ];
    }
    
    public function render(): View
    {
        return view('create-post');
    }
}
```

You may customize what happens after fields are filled [using the `afterStateHydrated()` method](advanced#hydration).

## Filling forms with data

To fill a form with data, call the `fill()` method on your form, and pass an array of data to fill it with:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public Post $post;
    
    public $title;
    public $content;
    
    public function mount(): void // [tl! focus:start]
    {
        $this->form->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
    } // [tl! focus:end]
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
        ];
    }
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

## Getting data from forms

To get all form data in an array, call the `getState()` method on your form.

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreatePost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public $title = '';
    public $content = '';
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
        ];
    }
    
    public function create(): void // [tl! focus:start]
    {
        Post::create($this->form->getState());
    } // [tl! focus:end]
    
    public function render(): View
    {
        return view('create-post');
    }
}
```

When `getState()` is run:
 
1) [Validation](validation) rules are checked, and if errors are present, the form is not submitted.
2) Any pending file uploads are stored permanently in the filesystem.
3) [Field relationships](#field-relationships), if they are defined, are saved.

> You may transform the value that is dehydrated from a field [using the `dehydrateStateUsing()` method](advanced#dehydration).

## Registering a model

You may register a model to a form. The form builder is able to use this model to unlock DX features, such as:
- Automatically retrieving the database table name when using database validation rules like `exists` and `unique`.
- Automatically attaching relationships to the model when the form is saved, when using fields such as the `Select`, `Repeater`, `SpatieMediaLibraryFileUpload`, or `SpatieTagsInput`.

Pass a model instance to a form using the `getFormModel()` method:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public Post $post;
    
    public $title;
    public $content;
    public $tags;
    
    public function mount(): void
    {
        $this->form->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
    }
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            Forms\Components\SpatieTagsInput::make('tags'),
        ];
    }
    
    protected function getFormModel(): Post // [tl! focus:start]
    {
        return $this->post;
    } // [tl! focus:end]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

Alternatively, you may pass the model instance to the field that requires it directly, using the `model()` method:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public Post $post;
    
    public $title;
    public $content;
    public $tags;
    
    public function mount(): void
    {
        $this->form->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
    }
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            Forms\Components\SpatieTagsInput::make('tags')->model($this->post), // [tl! focus]
        ];
    }
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

You may now use [field relationships](#field-relationships);

### Registering a model class

In some cases, the model instance is not available until the form has been submitted. For example, in a form that creates a post, the post model instance cannot be passed to the form before it has been submitted.

You may receive some of the same benefits of registering a model by registering its class instead:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreatePost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public $title = '';
    public $content = '';
    public $categories = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            Forms\Components\MultiSelect::make('categories')->relationship('categories', 'name'),
        ];
    }
    
    protected function getFormModel(): string // [tl! focus:start]
    {
        return Post::class;
    } // [tl! focus:end]
    
    public function render(): View
    {
        return view('create-post');
    }
}
```

You may now use [field relationships](#field-relationships).

## Field relationships

Some fields, such as the `Select`, `Repeater`, `SpatieMediaLibraryFileUpload`, or `SpatieTagsInput` are able to interact with model relationships.

For example, `Select` can be used to attach multiple records to a `BelongstoMany` relationship. When [registering a model](#registering-a-model) to the form or component, these relationships will be automatically saved to the pivot table [when `getState()` is called](#getting-data-from-forms):

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public Post $post;
    
    public $title;
    public $content;
    public $categories;
    
    public function mount(): void
    {
        $this->form->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
    }
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            Forms\Components\MultiSelect::make('categories')->relationship('categories', 'name'),
        ];
    }
    
    protected function getFormModel(): Post // [tl! focus:start]
    {
        return $this->post;
    }
    
    public function save(): void
    {
        $this->post->update(
            $this->form->getState(),
        );
    } // [tl! focus:end]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

### Saving field relationships manually

In some cases, the model instance is not available until the form has been submitted. For example, in a form that creates a post, the post model instance cannot be passed to the form before it has been submitted. In this case, you will [pass the model class](#registering-a-model-class) instead, but any field relationships will need to be saved manually after.

In this situation, you may call the `model()` and `saveRelationships()` methods on the form after the instance has been created:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class CreatePost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public $title = '';
    public $content = '';
    public $tags = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            Forms\Components\SpatieTagsInput::make('tags'),
        ];
    }
    
    protected function getFormModel(): string
    {
        return Post::class;
    }
    
    public function create(): void
    {
        $post = Post::create($this->form->getState());
        
        $this->form->model($post)->saveRelationships(); // [tl! focus]
    }
    
    public function render(): View
    {
        return view('create-post');
    }
}
```

## Using multiple forms

By default, the `InteractsWithForms` trait only handles one form per Livewire component. To change this, you can override the `getForms()` method to return more than one form, each with a unique name:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Author;
use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public Author $author;
    public Post $post;
    
    public $title;
    public $content;
    
    public $name;
    public $email;
    
    public function mount(): void
    {
        $this->postForm->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
        
        $this->authorForm->fill([
            'name' => $this->author->name,
            'email' => $this->author->email,
        ]);
    }
    
    protected function getPostFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
        ];
    }
    
    protected function getAuthorFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
        ];
    }
    
    public function savePost(): void
    {
        $this->post->update(
            $this->postForm->getState(),
        );
    }
    
    public function saveAuthor(): void
    {
        $this->author->update(
            $this->authorForm->getState(),
        );
    }
    
    protected function getForms(): array // [tl! focus:start]
    {
        return [
            'postForm' => $this->makeForm()
                ->schema($this->getPostFormSchema())
                ->model($this->post),
            'authorForm' => $this->makeForm()
                ->schema($this->getAuthorFormSchema())
                ->model($this->author),
        ];
    } // [tl! focus:end]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

## Scoping form data to an array property

You may scope the entire form data to a single array property on your Livewire component. This will allow you to avoid having to define a new property for each field:

```php
<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public Post $post;
    
    public $data; // [tl! focus]
    
    public function mount(): void
    {
        $this->form->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
    }
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            Forms\Components\SpatieTagsInput::make('tags'),
        ];
    }
    
    protected function getFormModel(): Post
    {
        return $this->post;
    }
    
    protected function getFormStatePath(): string // [tl! focus:start]
    {
        return 'data';
    } // [tl! focus:end]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

In this example, all data from your form will be stored in the `$data` array.
