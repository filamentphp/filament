---
title: Advanced infolists
---

## Inserting Livewire components into an infolist

You may insert a Livewire component directly into an infolist:

```php
use Filament\Infolists\Components\Livewire;
use App\Livewire\Foo;

Livewire::make(Foo::class)
```

If you are rendering multiple of the same Livewire component, please make sure to pass a unique `key()` to each:

```php
use Filament\Infolists\Components\Livewire;
use App\Livewire\Foo;

Livewire::make(Foo::class)
    ->key('foo-first')

Livewire::make(Foo::class)
    ->key('foo-second')

Livewire::make(Foo::class)
    ->key('foo-third')
```

### Passing parameters to a Livewire component

You can pass an array of parameters to a Livewire component:

```php
use Filament\Infolists\Components\Livewire;
use App\Livewire\Foo;

Livewire::make(Foo::class, ['bar' => 'baz'])
```

Now, those parameters will be passed to the Livewire component's `mount()` method:

```php
class Foo extends Component
{
    public function mount(string $bar): void
    {       
        // ...
    }
}
```

Alternatively, they will be available as public properties on the Livewire component:

```php
class Foo extends Component
{
    public string $bar;
}
```

#### Accessing the current record in the Livewire component

You can access the current record in the Livewire component using the `$record` parameter in the `mount()` method, or the `$record` property:

```php
use Illuminate\Database\Eloquent\Model;

class Foo extends Component
{
    public function mount(Model $record): void
    {       
        // ...
    }
    
    // or
    
    public Model $record;
}
```

### Lazy loading a Livewire component

You may allow the component to [lazily load](https://livewire.laravel.com/docs/lazy#rendering-placeholder-html) using the `lazy()` method:

```php
use Filament\Infolists\Components\Livewire;
use App\Livewire\Foo;

Livewire::make(Foo::class)->lazy()       
```
