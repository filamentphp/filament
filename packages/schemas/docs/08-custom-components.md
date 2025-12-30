---
title: Custom components
---
import Aside from "@components/Aside.astro"
import UtilityInjection from "@components/UtilityInjection.astro"

## Inserting a Blade view into a schema

You may use a "view" component to insert a Blade view into a schema arbitrarily:

```php
use Filament\Schemas\Components\View;

View::make('filament.schemas.components.chart')
```

This assumes that you have a `resources/views/filament/schemas/components/chart.blade.php` file.

You may pass data to this view through the `viewData()` method:

```php
use Filament\Schemas\Components\View;

View::make('filament.schemas.components.chart')
    ->viewData(['data' => $data])
```

### Rendering the component's child schema

You may pass an array of child schema components to the `schema()` method of the component:

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\View;

View::make('filament.schemas.components.chart')
    ->schema([
        TextInput::make('subtotal'),
        TextInput::make('total'),
    ])
```

Inside the Blade view, you may render the component's `schema()` using the `$getChildSchema()` function:

```blade
<div>
    {{ $getChildSchema() }}
</div>
```

### Accessing the state of another component in the Blade view

Inside the Blade view, you may access the state of another component in the schema using the `$get()` function:

```blade
<div>
    {{ $get('email') }}
</div>
```

<Aside variant="tip">
    Unless a form field is [reactive](../forms/overview#the-basics-of-reactivity), the Blade view will not refresh when the value of the field changes, only when the next user interaction occurs that makes a request to the server. If you need to react to changes in a field's value, it should be `live()`.
</Aside>

### Accessing the Eloquent record in the Blade view

Inside the Blade view, you may access the current Eloquent record using the `$record` variable:

```blade
<div>
    {{ $record->name }}
</div>
```

### Accessing the current operation in the Blade view

Inside the Blade view, you may access the current operation, usually `create`, `edit` or `view`, using the `$operation` variable:

```blade
<p>
    @if ($operation === 'create')
        This is a new post.
    @else
        This is an existing post.
    @endif
</p>
```

### Accessing the current Livewire component instance in the Blade view

Inside the Blade view, you may access the current Livewire component instance using `$this`:

```blade
@php
    use Filament\Resources\Users\RelationManagers\PostsRelationManager;
@endphp

<p>
    @if ($this instanceof PostsRelationManager)
        You are editing posts the of a user.
    @endif
</p>
```

### Accessing the current component instance in the Blade view

Inside the Blade view, you may access the current component instance using `$schemaComponent`. You can call public methods on this object to access other information that may not be available in variables:

```blade
<p>
    @if ($schemaComponent->getState())
        This is a new post.
    @endif
</p>
```

## Inserting a Livewire component into a schema

You may insert a Livewire component directly into a schema:

```php
use App\Livewire\Chart;
use Filament\Schemas\Components\Livewire;

Livewire::make(Chart::class)
```

<Aside variant="info">
    When inserting a Livewire component into the schema, there are limited capabilities. Only serializable data is accessible from the nested Livewire component, since they are rendered separately. As such, you can't [render a child schema](#rendering-the-components-child-schema), [access another component's live state](#accessing-the-state-of-another-component-in-the-blade-view), [access the current Livewire component instance](#accessing-the-current-livewire-component-instance-in-the-blade-view), or [access the current component instance](#accessing-the-current-component-instance-in-the-blade-view). Only [static data that you pass to the Livewire component](#passing-parameters-to-a-livewire-component), and [the current record](#accessing-the-current-record-in-the-livewire-component) are accessible. Situations where you should render a nested Livewire component instead of a [Blade view](#inserting-a-blade-view-into-a-schema) are rare because of these limitations.
</Aside>

If you are rendering multiple of the same Livewire component, please make sure to pass a unique `key()` to each:

```php
use App\Livewire\Chart;
use Filament\Schemas\Components\Livewire;

Livewire::make(Chart::class)
    ->key('chart-first')

Livewire::make(Chart::class)
    ->key('chart-second')

Livewire::make(Chart::class)
    ->key('chart-third')
```

### Passing parameters to a Livewire component

You can pass an array of parameters to a Livewire component:

```php
use App\Livewire\Chart;
use Filament\Schemas\Components\Livewire;

Livewire::make(Chart::class, ['bar' => 'baz'])
```

<UtilityInjection set="schemaComponents" version="4.x">As well as allowing a static value, the `make()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

Now, those parameters will be passed to the Livewire component's `mount()` method:

```php
class Chart extends Component
{
    public function mount(string $bar): void
    {       
        // ...
    }
}
```

Alternatively, they will be available as public properties on the Livewire component:

```php
class Chart extends Component
{
    public string $bar;
}
```

#### Accessing the current record in the Livewire component

You can access the current record in the Livewire component using the `$record` parameter in the `mount()` method, or the `$record` property:

```php
use Illuminate\Database\Eloquent\Model;

class Chart extends Component
{
    public function mount(?Model $record = null): void
    {       
        // ...
    }
    
    // or
    
    public ?Model $record = null;
}
```

Please be aware that when the record has not yet been created, it will be `null`. If you'd like to hide the Livewire component when the record is `null`, you can use the `hidden()` method:

```php
use Filament\Schemas\Components\Livewire;
use Illuminate\Database\Eloquent\Model;

Livewire::make(Chart::class)
    ->hidden(fn (?Model $record): bool => $record === null)
```

### Lazy loading a Livewire component

You may allow the component to [lazily load](https://livewire.laravel.com/docs/lazy#rendering-placeholder-html) using the `lazy()` method:

```php
use Filament\Schemas\Components\Livewire;
use App\Livewire\Chart;

Livewire::make(Chart::class)
    ->lazy()       
```

## Custom component classes

You may create your own custom component classes and views, which you can reuse across your project, and even release as a plugin to the community.

<Aside variant="tip">
    If you're just creating a simple custom component to use once, you could instead use a [view component](#inserting-a-blade-view-into-a-schema) to render any custom Blade file.
</Aside>

To create a custom component class and view, you may use the following command:

```bash
php artisan make:filament-schema-component Chart
```

This will create the following component class:

```php
use Filament\Schemas\Components\Component;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';

    public static function make(): static
    {
        return app(static::class);
    }
}
```

It will also create a view file at `resources/views/filament/schemas/components/chart.blade.php`.

You may use the same utilities as you would when [inserting a Blade view into a schema](#inserting-a-blade-view-into-a-schema) to [render the component's child schema](#rendering-the-components-child-schema), [access another component's live state](#accessing-the-state-of-another-component-in-the-blade-view), [access the current Eloquent record](#accessing-the-eloquent-record-in-the-blade-view), [access the current operation](#accessing-the-current-operation-in-the-blade-view), [access the current Livewire component instance](#accessing-the-current-livewire-component-instance-in-the-blade-view), and [access the current component instance](#accessing-the-current-component-instance-in-the-blade-view).

<Aside variant="info">
    Filament schema components are **not** Livewire components. Defining public properties and methods on a schema component class will not make them accessible in the Blade view.
</Aside>

### Adding a configuration method to a custom component class

You may add a public method to the custom component class that accepts a configuration value, stores it in a protected property, and returns it again from another public method:

```php
use Filament\Schemas\Components\Component;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';
    
    protected ?string $heading = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function heading(?string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): ?string
    {
        return $this->heading;
    }
}
```

Now, in the Blade view for the custom component, you may access the heading using the `$getHeading()` function:

```blade
<div>
    {{ $getHeading() }}
</div>
```

Any public method that you define on the custom component class can be accessed in the Blade view as a variable function in this way.

To pass the configuration value to the custom component class, you may use the public method:

```php
use App\Filament\Schemas\Components\Chart;

Chart::make()
    ->heading('Sales')
```

#### Allowing utility injection in a custom component configuration method

[Utility injection](overview#component-utility-injection) is a powerful feature of Filament that allows users to configure a component using functions that can access various utilities. You can allow utility injection by ensuring that the parameter type and property type of the configuration allows the user to pass a `Closure`. In the getter method, you should pass the configuration value to the `$this->evaluate()` method, which will inject utilities into the user's function if they pass one, or return the value if it is static:

```php
use Closure;
use Filament\Schemas\Components\Component;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';
    
    protected string | Closure | null $heading = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function heading(string | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): ?string
    {
        return $this->evaluate($this->heading);
    }
}
```

Now, you can pass a static value or a function to the `heading()` method, and [inject any utility](overview#component-utility-injection) as a parameter:

```php
use App\Filament\Schemas\Components\Chart;

Chart::make()
    ->heading(fn (Product $record): string => "{$record->name} Sales")
```

### Accepting a configuration value in the constructor of a custom component class

You may accept a configuration value in the `make()` constructor method of the custom component and pass it to the corresponding setter method:

```php
use Closure;
use Filament\Schemas\Components\Component;

class Chart extends Component
{
    protected string $view = 'filament.schemas.components.chart';
    
    protected string | Closure | null $heading = null;

    public function __construct(string | Closure | null $heading = null)
    {
        $this->heading($heading)
    }

    public static function make(string | Closure | null $heading = null): static
    {
        return app(static::class, ['heading' => $heading]);
    }
    
    public function heading(string | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): ?string
    {
        return $this->evaluate($this->heading);
    }
}
```
