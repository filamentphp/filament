---
title: Adding an action to a Livewire component
---

## Setting up the Livewire component

You must use the `InteractsWithActions` trait and implement the `HasActions` interface on your Livewire component class:

```php
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Livewire\Component;

class ManagePost extends Component implements HasActions
{
    use InteractsWithActions;

    // ...
}
```

## Adding the action

Add a method that returns your action:

```php
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Livewire\Component;

class ManagePost extends Component implements HasActions
{
    use InteractsWithActions;
    
    public Post $post;
    
    public function delete(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->post->delete());
    }

    // ...
}
```

Finally, you need to render the action in your view. To do this, you can use `{{ $this->delete }}`, where you replace `delete` with the name of your action method:

```blade
<div>
    {{ $this->delete }}
    
    <x-filament-actions::modals />
</div>
```

You also need `<x-filament-actions::modals />` which injects the HTML required to render action modals. This only needs to be included within the Livewire component once, regardless of how many actions you have for that component.

## Grouping actions

You may group actions together into a dropdown menu by using the `<x-filament-actions::group>` Blade component, passing in the `actions` array as an attribute:

```blade
<div>
    <x-filament-actions::group :actions="[
        $this->edit,
        $this->view,
        $this->delete,
    ]" />
    
    <x-filament-actions::modals />
</div>
```

You can also pass in a `label`, `icon`, `color`, `size`, `tooltip`, and `dropdown-placement` to the group, as attributes:

```blade
<div>
    <x-filament-actions::group
        :actions="[
            $this->edit,
            $this->view,
            $this->delete,
        ]"
        label="Actions"
        icon="heroicon-m-ellipsis-vertical"
        color="primary"
        size="md"
        tooltip="More actions"
        dropdown-placement="bottom-end"
    />
    
    <x-filament-actions::modals />
</div>
```