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
    {{ $this->form }} // [tl! add]
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

Visit your Livewire component in the browser, and you should see your form!

## Lifecycle



## Prefilling forms with data

$this->form->fill() with existing data

### Default data

$this->form->fill() with default data

### Custom hydration transformations

## Retrieving validated data

$this->form->getState()

## Rendering multiple forms

getForms()
