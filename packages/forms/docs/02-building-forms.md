---
title: Building Forms
---

## Preparing your Livewire component

Implement the `HasForms` interface and use the `InteractsWithForms` trait:

```php
<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms // [tl! add]
{
    use Forms\Concerns\InteractsWithForms; // [tl! add]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

In your Livewire component's view, render the form:

```blade
<div>
    {{ $this->form }}
</div>
```

Finally, add any [fields](fields), [layout components](layout), and [custom components](building-custom-components) to the Livewire component's `getFormSchema()` method:

```php
<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditPost extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    
    public $title = '';
    public $content = '';
    
    protected function getFormSchema(): array // [tl! add:start]
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            // ...
        ];
    } // [tl! add:end]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

Visit your Livewire component in the browser, and you should see the form components from `getFormSchema()`!

## Filling forms with data

Often, you will need to prefill your form fields with data. In normal Livewire components, this is often done in the `mount()` method, as this is only run once, immediately after the component is instantiated.

For your fields to hold data, they should have a corresponding property on your Livewire component, just as in Livewire normally.

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
    
    public $title = '';
    public $content = '';
    
    public function mount(): void // [tl! add:start]
    {
        $this->form->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
    } // [tl! add:end]
    
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

### Default data

Fields can use a `default()` configuration method, which allows you to specify a default values to fill your form with.

To fill a form with default values, call the `fill()` method on your form without any parameters:

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
    
    public function mount(): void // [tl! add:start]
    {
        $this->form->fill();
    } // [tl! add:end]
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->default('Status Update') // [tl! add]
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
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
        ];
    }
    
    public function create(): void // [tl! add:start]
    {
        Post::create($this->form->getState());
    } // [tl! add:end]
    
    public function render(): View
    {
        return view('create-post');
    }
}
```

When `getState()` is run:
 
1) Validation rules are checked, and if errors are present, the form is not submitted.
2) Any pending file uploads are stored permanently in the filesystem.
3) [Field relationships](#working-with-field-relationships), if they are defined, are saved.

## Using Eloquent model binding

Using Eloquent model binding for fields works out of the box, using Livewire's dot-syntax:

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
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('post.title')->required(), // [tl! add:start]
            Forms\Components\MarkdownEditor::make('post.content'), // [tl! add:end]
        ];
    }
    
    public function render(): View
    {
        return view('edit-post');
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
    
    protected function getPostFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('post.title')->required(),
            Forms\Components\MarkdownEditor::make('post.content'),
        ];
    }
    
    protected function getAuthorFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('author.name')->required(),
            Forms\Components\TextInput::make('author.email')->email()->required(),
        ];
    }
    
    public function save(): void
    {
        $this->post->update(
            $this->postForm->getState(),
        );
        
        $this->author->update(
            $this->authorForm->getState(),
        );
    }
    
    protected function getForms(): array // [tl! add:start]
    {
        return [
            'postForm' => $this->makeForm()->schema($this->getPostFormSchema()),
            'authorForm' => $this->makeForm()->schema($this->getAuthorFormSchema()),
        ];
    } // [tl! add:end]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

## Working with field relationships

Some fields, such as the `BelongsToSelect`, `SpatieMediaLibraryFileUpload` and `SpatieLaravelTagsInput` require you to pass an Eloquent model instance to the form directly. This allows them to attach Eloquent relationships automatically when the form is saved.

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
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('post.title')->required(),
            Forms\Components\MarkdownEditor::make('post.content'),
            Forms\Components\SpatieTagsInput::make('post.tags'),
        ];
    }
    
    protected function getFormModel(): Post // [tl! add:start]
    {
        return $this->post;
    } // [tl! add:end]
    
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
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('post.title')->required(),
            Forms\Components\MarkdownEditor::make('post.content'),
            Forms\Components\SpatieTagsInput::make('post.tags')->model($this->post), // [tl! add]
        ];
    }
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

Now, [when `getState()` is called on this form](#getting-data-from-forms), any relationships are saved automatically to this model.

### Saving field relationships manually

In some cases, the model instance is not available until the form has been submitted. For example, in a form that creates a post, the post model instance cannot be passed to the form before it has been submitted.

In this situation, you may call the `model()` and `saveRelationships()` methods on the form after the instance has been created:

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
    public $tags = [];
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            Forms\Components\SpatieTagsInput::make('tags'),
        ];
    }
    
    public function create(): void
    {
        $post = Post::create($this->form->getState());
        
        $this->form->model($post)->saveRelationships(); // [tl! add]
    }
    
    public function render(): View
    {
        return view('create-post');
    }
}
```
