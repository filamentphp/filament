---
title: Building Forms
---

$this->form->fill() with existing data
$this->form->fill() with default data
Transforming hydrated data when filled

$this->form->getState()

## Getting started

Follow all [installation](installation) instructions to set up Livewire, Alpine.js and TailwindCSS in your project.

Create a Livewire component to contain your form, or you can use any existing component:

```bash
php artisan make:livewire MyForm
```

Implement the `HasForms` interface and use the `InteractsWithForms` trait in your Livewire component:

```php
<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MyForm extends Component implements Forms\Contracts\HasForms // [tl! add]
{
    use Forms\Concerns\InteractsWithForms; // [tl! add]
    
    public function render(): View
    {
        return view('livewire.my-form');
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

class MyForm extends Component implements Forms\Contracts\HasForms
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
        return view('livewire.my-form');
    }
}
```

Visit your Livewire component in the browser, and you should see your form!

## Lifecycle



## Prefilling forms with data

### Default data

## Retrieving validated data

## Rendering multiple forms
