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
    
    protected function getFormSchema(): array // [tl! add:start]
    {
        return [
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

## Prefilling forms with data

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
    
    public $title;
    public $content;
    
    public function mount(): void // [tl! add:start]
    {
        $this->fill([
            'title' => $this->post->title,
            'content' => $this->post->content,
        ]);
    } // [tl! add:end]
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title'),
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
    
    public $title;
    public $content;
    
    public function mount(): void // [tl! add:start]
    {
        $this->fill();
    } // [tl! add:end]
    
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->default('Status Update'), // [tl! add:start]
            Forms\Components\MarkdownEditor::make('content'),
        ];
    }
    
    public function render(): View
    {
        return view('create-post');
    }
}
```

## Retrieving validated data

$this->form->getState()

## Rendering multiple forms

getForms()
