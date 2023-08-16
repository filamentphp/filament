---
title: Livewire Component
---

## Livewire components

You may use the Livewire component directly from the infolist:

```php
\Filament\Infolists\Components\Livewire::make(\App\Livewire\Foo::class)
```

## Passing params

You can pass array of params to component:

```php
\Filament\Infolists\Components\Livewire::make(\App\Livewire\Foo::class)->componentData(['foo' => 'bar'])
```

```php
// Livewire component
namespace App\Livewire;

use Livewire\Component;

class Foo extends Component
{
    public array $componentData = [];

    public function mount(array $componentData = []): void
    {       
        $this->componentData = $componentData;
    }
}
```

## Lazy Loading

You may allow the component to load lazy:

```php
\Filament\Infolists\Components\Livewire::make(\App\Livewire\Foo::class)->lazy()
```

## Passing record

You may pass current record to the component:

```php
\Filament\Infolists\Components\Livewire::make(\App\Livewire\Foo::class)->withRecord()       
```

```php
// Livewire component
namespace App\Livewire;

use Livewire\Component;

class Foo extends Component
{
    public record;

    public function mount($record): void
    {       
        $this->record = $record;
    }
}
```
