---
title: Edit action
---

## Overview

Filament includes a prebuilt action that is able to edit Eloquent records. When the trigger button is clicked, a modal will open with a form inside. The user fills the form, and that data is validated and saved into the database. You may use it like so:

```php
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;

EditAction::make()
    ->record($this->post)
    ->form([
        TextInput::make('title')
            ->required()
            ->maxLength(255),
        // ...
    ])
```

If you want to edit table rows, you can use the `Filament\Tables\Actions\EditAction` instead:

```php
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

public function table(Table $table): Table
{
    return $table
        ->actions([
            EditAction::make()
                ->form([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    // ...
                ]),
        ]);
}
```

## Customizing data before filling the form

You may wish to modify the data from a record before it is filled into the form. To do this, you may use the `mutateRecordDataUsing()` method to modify the `$data` array, and return the modified version before it is filled into the form:

```php
EditAction::make()
    ->mutateRecordDataUsing(function (array $data): array {
        $data['user_id'] = auth()->id();

        return $data;
    })
```

## Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may use the `mutateFormDataUsing()` method, which has access to the `$data` as an array, and returns the modified version:

```php
EditAction::make()
    ->mutateFormDataUsing(function (array $data): array {
        $data['last_edited_by_id'] = auth()->id();

        return $data;
    })
```

## Customizing the saving process

You can tweak how the record is updated with the `using()` method:

```php
use Illuminate\Database\Eloquent\Model;

EditAction::make()
    ->using(function (Model $record, array $data): Model {
        $record->update($data);

        return $record;
    })
```

## Redirecting after saving

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
EditAction::make()
    ->successRedirectUrl(route('posts.list'))
```

If you want to redirect using the created record, use the `$record` parameter:

```php
use Illuminate\Database\Eloquent\Model;

EditAction::make()
    ->successRedirectUrl(fn (Model $record): string => route('posts.view', [
        'post' => $record,
    ]))
```

## Customizing the save notification

When the record is successfully updated, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
EditAction::make()
    ->successNotificationTitle('User updated')
```

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Notifications\Notification;

EditAction::make()
    ->successNotification(
       Notification::make()
            ->success()
            ->title('User updated')
            ->body('The user has been saved successfully.'),
    )
```

To disable the notification altogether, use the `successNotification(null)` method:

```php
EditAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

Hooks may be used to execute code at various points within the action's lifecycle, like before a form is saved.

There are several available hooks:

```php
EditAction::make()
    ->beforeFormFilled(function () {
        // Runs before the form fields are populated from the database.
    })
    ->afterFormFilled(function () {
        // Runs after the form fields are populated from the database.
    })
    ->beforeFormValidated(function () {
        // Runs before the form fields are validated when the form is saved.
    })
    ->afterFormValidated(function () {
        // Runs after the form fields are validated when the form is saved.
    })
    ->before(function () {
        // Runs before the form fields are saved to the database.
    })
    ->after(function () {
        // Runs after the form fields are saved to the database.
    })
```

## Halting the saving process

At any time, you may call `$action->halt()` from inside a lifecycle hook or mutation method, which will halt the entire saving process:

```php
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\EditAction;

EditAction::make()
    ->before(function (EditAction $action) {
        if (! $this->record->team->subscribed()) {
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