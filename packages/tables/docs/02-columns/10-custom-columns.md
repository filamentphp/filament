---
title: Custom columns
---
import Aside from "@components/Aside.astro"

## Introduction

You may create your own custom column classes and views, which you can reuse across your project, and even release as a plugin to the community.

To create a custom column class and view, you may use the following command:

```bash
php artisan make:filament-table-column AudioPlayerColumn
```

This will create the following component class:

```php
use Filament\Tables\Columns\Column;

class AudioPlayerColumn extends Column
{
    protected string $view = 'filament.tables.columns.audio-player-column';
}
```

It will also create a view file at `resources/views/filament/tables/columns/audio-player-column.blade.php`.

<Aside variant="info">
    Filament table columns are **not** Livewire components. Defining public properties and methods on a table column class will not make them accessible in the Blade view.
</Aside>

## Adding a loading skeleton

When a table uses [`loadingSkeleton()`](../overview#displaying-a-loading-skeleton), Filament adds the `.fi-ta-content-loading` class to the table content while its records are updating. You may use this class in your theme's CSS to add a loading skeleton to a custom column.

First, add a CSS hook class to the outer element of the column's Blade view:

```blade
<div class="fi-ta-audio-player" {{ $getExtraAttributeBag() }}>
    <audio controls src="{{ $getState() }}"></audio>
</div>
```

Then, style the column while it is inside the loading table content. The loading skeleton CSS variables ensure that your column uses the same animation, background color, and border radius as Filament's built-in columns:

```css
.fi-ta-content-loading .fi-ta-audio-player {
    position: relative;
    width: fit-content;
    max-width: 100%;
    color: transparent;

    &::after {
        content: '';
        position: absolute;
        inset: 0;
        animation: var(--loading-skeleton-animation);
        background-color: var(--loading-skeleton-background-color);
        border-radius: var(--loading-skeleton-border-radius);
    }

    & > * {
        opacity: 0;
    }
}
```

The pseudo-element uses the dimensions of your column's existing content, so no additional skeleton HTML is required. You may adjust its inset, width, height, or border radius to match the shape of your column without changing the shared loading skeleton styles.

## Accessing the state of the column in the Blade view

Inside the Blade view, you may access the [state](overview#column-content-state) of the column using the `$getState()` function:

```blade
<div>
    {{ $getState() }}
</div>
```

## Accessing the Eloquent record in the Blade view

Inside the Blade view, you may access the current table row's Eloquent record using the `$record` variable:

```blade
<div>
    {{ $record->name }}
</div>
```

## Accessing the current Livewire component instance in the Blade view

Inside the Blade view, you may access the current Livewire component instance using `$this`:

```blade
@php
    use Filament\Resources\Users\RelationManagers\ConferencesRelationManager;
@endphp

<div>
    @if ($this instanceof ConferencesRelationManager)
        You are editing the conferences of a user.
    @endif
</div>
```

## Accessing the current column instance in the Blade view

Inside the Blade view, you may access the current column instance using `$column`. You can call public methods on this object to access other information that may not be available in variables:

```blade
<div>
    @if ($column->isLabelHidden())
        This is a new conference.
    @endif
</div>
```

## Adding a configuration method to a custom column class

You may add a public method to the custom column class that accepts a configuration value, stores it in a protected property, and returns it again from another public method:

```php
use Filament\Tables\Columns\Column;

class AudioPlayerColumn extends Column
{
    protected string $view = 'filament.tables.columns.audio-player-column';
    
    protected ?float $speed = null;

    public function speed(?float $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }
}
```

Now, in the Blade view for the custom column, you may access the speed using the `$getSpeed()` function:

```blade
<div>
    {{ $getSpeed() }}
</div>
```

Any public method that you define on the custom column class can be accessed in the Blade view as a variable function in this way.

To pass the configuration value to the custom column class, you may use the public method:

```php
use App\Filament\Tables\Columns\AudioPlayerColumn;

AudioPlayerColumn::make('recording')
    ->speed(0.5)
```

## Allowing utility injection in a custom column configuration method

[Utility injection](overview#column-utility-injection) is a powerful feature of Filament that allows users to configure a component using functions that can access various utilities. You can allow utility injection by ensuring that the parameter type and property type of the configuration allows the user to pass a `Closure`. In the getter method, you should pass the configuration value to the `$this->evaluate()` method, which will inject utilities into the user's function if they pass one, or return the value if it is static:

```php
use Closure;
use Filament\Tables\Columns\Column;

class AudioPlayerColumn extends Column
{
    protected string $view = 'filament.tables.columns.audio-player-column';
    
    protected float | Closure | null $speed = null;

    public function speed(float | Closure | null $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->evaluate($this->speed);
    }
}
```

Now, you can pass a static value or a function to the `speed()` method, and [inject any utility](overview#column-utility-injection) as a parameter:

```php
use App\Filament\Tables\Columns\AudioPlayerColumn;

AudioPlayerColumn::make('recording')
    ->speed(fn (Conference $record): float => $record->isGlobal() ? 1 : 0.5)
```
