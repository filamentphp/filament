---
title: Create action
---
import UtilityInjection from "@components/UtilityInjection.astro"

## Introduction

Filament includes an action that is able to create Eloquent records. When the trigger button is clicked, a modal will open with a form inside. The user fills the form, and that data is validated and saved into the database. You may use it like so:

```php
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;

CreateAction::make()
    ->schema([
        TextInput::make('title')
            ->required()
            ->maxLength(255),
        // ...
    ])
```

## Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may use the `mutateDataUsing()` method, which has access to the `$data` as an array, and returns the modified version:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->mutateDataUsing(function (array $data): array {
        $data['user_id'] = auth()->id();

        return $data;
    })
```

<UtilityInjection set="actions" version="4.x">As well as `$data`, the `mutateDataUsing()` function can inject various utilities as parameters.</UtilityInjection>

## Customizing the creation process

You can tweak how the record is created with the `using()` method:

```php
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;

CreateAction::make()
    ->using(function (array $data, string $model): Model {
        return $model::create($data);
    })
```

`$model` is the class name of the model, but you can replace this with your own hard-coded class if you wish.

<UtilityInjection set="actions" version="4.x">As well as `$data` and `$model`, the `using()` function can inject various utilities as parameters.</UtilityInjection>

## Redirecting after creation

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->successRedirectUrl(route('posts.list'))
```

If you want to redirect using the created record, use the `$record` parameter:

```php
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;

CreateAction::make()
    ->successRedirectUrl(fn (Model $record): string => route('posts.edit', [
        'post' => $record,
    ]))
```

<UtilityInjection set="actions" version="4.x">As well as `$record`, the `successRedirectUrl()` function can inject various utilities as parameters.</UtilityInjection>

## Customizing the save notification

When the record is successfully created, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->successNotificationTitle('User registered')
```

<UtilityInjection set="actions" version="4.x">As well as allowing a static value, the `successNotificationTitle()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;

CreateAction::make()
    ->successNotification(
       Notification::make()
            ->success()
            ->title('User registered')
            ->body('The user has been created successfully.'),
    )
```

<UtilityInjection set="actions" version="4.x" extras="Notification;;Filament\Notifications\Notification;;$notification;;The default notification object, which could be a useful starting point for customization.">As well as allowing a static value, the `successNotification()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

To disable the notification altogether, use the `successNotification(null)` method:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

Hooks may be used to execute code at various points within the action's lifecycle, like before a form is saved.

There are several available hooks:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->beforeFormFilled(function () {
        // Runs before the form fields are populated with their default values.
    })
    ->afterFormFilled(function () {
        // Runs after the form fields are populated with their default values.
    })
    ->beforeFormValidated(function () {
        // Runs before the form fields are validated when the form is submitted.
    })
    ->afterFormValidated(function () {
        // Runs after the form fields are validated when the form is submitted.
    })
    ->before(function () {
        // Runs before the form fields are saved to the database.
    })
    ->after(function () {
        // Runs after the form fields are saved to the database.
    })
```

<UtilityInjection set="actions" version="4.x">These hook functions can inject various utilities as parameters.</UtilityInjection>

## Halting the creation process

At any time, you may call `$action->halt()` from inside a lifecycle hook or mutation method, which will halt the entire creation process:

```php
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;

CreateAction::make()
    ->before(function (CreateAction $action, Post $record) {
        if (! $record->team->subscribed()) {
            Notification::make()
                ->warning()
                ->title('You don\'t have an active subscription!')
                ->body('Choose a plan to continue.')
                ->persistent()
                ->actions([
                    Action::make('subscribe')
                        ->button()
                        ->url(route('subscribe'), shouldOpenInNewTab: true),
                ])
                ->send();
        
            $action->halt();
        }
    })
```

If you'd like the action modal to close too, you can completely `cancel()` the action instead of halting it:

```php
$action->cancel();
```

## Using a wizard

You may easily transform the creation process into a multistep wizard. Instead of using a `schema()`, define a `steps()` array and pass your `Step` objects:

```php
use Filament\Actions\CreateAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Wizard\Step;

CreateAction::make()
    ->steps([
        Step::make('Name')
            ->description('Give the category a unique name')
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->disabled()
                    ->required()
                    ->unique(Category::class, 'slug'),
            ])
            ->columns(2),
        Step::make('Description')
            ->description('Add some extra details')
            ->schema([
                MarkdownEditor::make('description'),
            ]),
        Step::make('Visibility')
            ->description('Control who can view it')
            ->schema([
                Toggle::make('is_visible')
                    ->label('Visible to customers.')
                    ->default(true),
            ]),
    ])
```

Now, create a new record to see your wizard in action! Edit will still use the form defined within the resource class.

If you'd like to allow free navigation, so all the steps are skippable, use the `skippableSteps()` method:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->steps([
        // ...
    ])
    ->skippableSteps()
```

## Creating another record

### Modifying the create another action

If you'd like to modify the "create another" action, you may use the `createAnotherAction()` method, passing a function that returns an action. All methods that are available to [customize action trigger buttons](overview) can be used:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->createAnotherAction(fn (Action $action): Action => $action->label('Custom create another label'))
```

### Disabling create another

If you'd like to remove the "create another" button from the modal, you can use the `createAnother(false)` method:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->createAnother(false)
```

<UtilityInjection set="actions" version="4.x">As well as allowing a static value, the `createAnother()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

### Preserving data when creating another

By default, when the user uses the "create and create another" feature, all the form data is cleared so the user can start fresh. If you'd like to preserve some of the data in the form, you may use the `preserveFormDataWhenCreatingAnother()` method, passing an array of fields to preserve:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->preserveFormDataWhenCreatingAnother(['is_admin', 'organization'])
```

Alternatively, you can define a function that returns an array of the `$data` to preserve:

```php
use Filament\Actions\CreateAction;
use Illuminate\Support\Arr;

CreateAction::make()
    ->preserveFormDataWhenCreatingAnother(fn (array $data): array => Arr::only($data, ['is_admin', 'organization']))
```

To preserve all the data, return the entire `$data` array:

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->preserveFormDataWhenCreatingAnother(fn (array $data): array => $data)
```

<UtilityInjection set="actions" version="4.x">As well as allowing a static value, the `preserveFormDataWhenCreatingAnother()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>
