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
    
    protected function getFormSchema(): array // [tl! focus:start]
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            // ...
        ];
    } // [tl! focus:end]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

Visit your Livewire component in the browser, and you should see the form components from `getFormSchema()`.

## Registering a model

You may register a model to a form. The form builder is able to use this model to unlock DX features, such as:
- Automatically retrieving the database table name when using database validation rules like `exists` and `unique`.
- Automatically attaching relationships to the model when the form is saved, when using fields such as the `BelongsToManyMultiSelect`, `SpatieMediaLibraryFileUpload`, or `SpatieTagsInput`.

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
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('post.title')->required(),
            Forms\Components\MarkdownEditor::make('post.content'),
            Forms\Components\SpatieTagsInput::make('post.tags')->model($this->post), // [tl! focus]
        ];
    }
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```

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
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            Forms\Components\BelongsToManyMultiSelect::make('categories')->relationship('categories', 'name'),
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
 
1) [Validation](#validation) rules are checked, and if errors are present, the form is not submitted.
2) Any pending file uploads are stored permanently in the filesystem.
3) [Field relationships](#field-relationships), if they are defined, are saved.

## Validation

You may add validation rules to any field using the `rules()` method:

```php
TextInput::make('slug')->rules(['alpha_dash'])
```

A full list of validation rules may be found in the [Laravel documentation](https://laravel.com/docs/validation#available-validation-rules).

### Dedicated methods

There are also dedicated methods for some validation rules, some of which are able to add frontend validation as well as backend validation.

We recommend that you use dedicated validation methods wherever possible.

#### Exists

The field value must exist in the database. [See the Laravel documentation](https://laravel.com/docs/validation#rule-exists).

```php
Field::make('invitation')->exists()
```

By default, the form's model will be searched, [if it is registered](#registering-a-model). You may specify a custom table name or model to search:

```php
use App\Models\Invitation;

Field::make('invitation')->exists(table: Invitation::class)
```

By default, the field name will be used as the column to search. You may specify a custom column to search:

```php
Field::make('invitation')->exists(column: 'id')
```

#### Nullable

The field value can be empty. This rule is applied by default if the `required` rule is not present. [See the Laravel documentation](https://laravel.com/docs/validation#rule-nullable)

```php
Field::make('name')->nullable()
```

#### Required

The field value must not be empty. [See the Laravel documentation](https://laravel.com/docs/validation#rule-required)

```php
Field::make('name')->required()
```

#### Same

The field value must be the same as another. [See the Laravel documentation](https://laravel.com/docs/validation#rule-same)

```php
Field::make('password')->same('passwordConfirmation')
```

#### Unique

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

## Field relationships

Some fields, such as the `BelongsToManyMultiSelect`, `SpatieMediaLibraryFileUpload`, or `SpatieTagsInput` are able to interact with model relationships.

For example, `BelongsToManyMultiSelect` is a multi-select field that can be used to attach records to a `BelongstoMany` relationship. When [registering a model](#registering-a-model) to the form, these relationships will be automatically saved to the pivot table [when `getState()` is called](#getting-data-from-forms):

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
            Forms\Components\BelongsToManyMultiSelect::make('post.categories')->relationship('categories', 'name'),
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
use Illuminate\Database\Eloquent\Model;use Livewire\Component;

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
            'postForm' => $this->makeForm()->schema($this->getPostFormSchema()),
            'authorForm' => $this->makeForm()->schema($this->getAuthorFormSchema()),
        ];
    } // [tl! focus:end]
    
    public function render(): View
    {
        return view('edit-post');
    }
}
```
