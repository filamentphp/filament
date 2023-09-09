---
title: Adding an action to a Livewire component
---

## Setting up the Livewire component

First, generate a new Livewire component:

```bash
php artisan make:livewire ManageProduct
```

Then, render your Livewire component on the page:

```blade
@livewire('manage-product')
```

Alternatively, you can use a full-page Livewire component:

```php
use App\Livewire\ManageProduct;
use Illuminate\Support\Facades\Route;

Route::get('products/{product}/manage', ManageProduct::class);
```

You must use the `InteractsWithActions` and `InteractsWithForms` traits, and implement the `HasActions` and `HasForms` interfaces on your Livewire component class:

```php
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ManagePost extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

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
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ManagePost extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    
    public Post $post;
    
    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->post->delete());
    }

    // ...
}
```

Finally, you need to render the action in your view. To do this, you can use `{{ $this->deleteAction }}`, where you replace `deleteAction` with the name of your action method:

```blade
<div>
    {{ $this->deleteAction }}
    
    <x-filament-actions::modals />
</div>
```

You also need `<x-filament-actions::modals />` which injects the HTML required to render action modals. This only needs to be included within the Livewire component once, regardless of how many actions you have for that component.

## Passing action arguments

Sometimes, you may wish to pass arguments to your action. For example, if you're rendering the same action multiple times in the same view, but each time for a different model, you could pass the model ID as an argument, and then retrieve it later. To do this, you can invoke the action in your view and pass in the arguments as an array:

```php
<div>
    @foreach ($posts as $post)
        <h2>{{ $post->title }}</h2>
        
        {{ ($this->delete)(['post' => $post->id]) }}
    @endforeach
    
    <x-filament-actions::modals />
</div>
```

Now, you can access the post ID in your action method:

```php
use App\Models\Post;
use Filament\Actions\Action;

public function deleteAction(): Action
{
    return Action::make('delete')
        ->requiresConfirmation()
        ->action(function (array $arguments) {
            $post = Post::find($arguments['post']);
            
            $post?->delete();
        });
}
```

## Grouping actions in a Livewire view

You may [group actions together into a dropdown menu](grouping-actions) by using the `<x-filament-actions::group>` Blade component, passing in the `actions` array as an attribute:

```blade
<div>
    <x-filament-actions::group :actions="[
        $this->editAction,
        $this->viewAction,
        $this->deleteAction,
    ]" />
    
    <x-filament-actions::modals />
</div>
```

You can also pass in any attributes to customize the appearance of the trigger button and dropdown:

```blade
<div>
    <x-filament-actions::group
        :actions="[
            $this->editAction,
            $this->viewAction,
            $this->deleteAction,
        ]"
        label="Actions"
        icon="heroicon-m-ellipsis-vertical"
        color="primary"
        size="md"
        tooltip="More actions"
        dropdown-placement="bottom-start"
    />
    
    <x-filament-actions::modals />
</div>
```

## Chaining actions

You can chain multiple actions together, by calling the `replaceMountedAction()` method to replace the current action with another when it has finished:

```php
use App\Models\Post;
use Filament\Actions\Action;

public function editAction(): Action
{
    return Action::make('edit')
        ->form([
            // ...
        ])
        // ...
        ->action(function (array $arguments) {
            $post = Post::find($arguments['post']);
            
            // ...
            
            $this->replaceMountedAction('publish', $arguments);        
        });
}

public function publishAction(): Action
{
    return Action::make('publish')
        ->requiresConfirmation()
        // ...
        ->action(function (array $arguments) {
            $post = Post::find($arguments['post']);
            
            $post->publish();
        });
}
```

Now, when the first action is submitted, the second action will open in its place. The [arguments](#passing-action-arguments) that were originally passed to the first action get passed to the second action, so you can use them to persist data between requests.

If the first action is canceled, the second one is not opened. If the second action is canceled, the first one has already run and cannot be cancelled.
