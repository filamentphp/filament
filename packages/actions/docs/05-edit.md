---
title: Edit action
---

## Introduction

Filament includes an action that is able to edit Eloquent records. When the trigger button is clicked, a modal will open with a form inside. The user fills the form, and that data is validated and saved into the database. You may use it like so:

```php
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;

EditAction::make()
    ->schema([
        TextInput::make('title')
            ->required()
            ->maxLength(255),
        // ...
    ])
```

<AutoScreenshot name="actions/edit-action/modal" alt="Edit action modal" />

## Customizing data before filling the form

You may wish to modify the data from a record before it is filled into the form. To do this, you may use the `mutateRecordDataUsing()` method to modify the `$data` array, and return the modified version before it is filled into the form:

```php
use Filament\Actions\EditAction;

EditAction::make()
    ->mutateRecordDataUsing(function (array $data): array {
        $data['user_id'] = auth()->id();

        return $data;
    })
```

<UtilityInjection set="actions">As well as `$data`, the `mutateRecordDataUsing()` function can inject various utilities as parameters.</UtilityInjection>

> [!WARNING]
> Filament fills the form using the record's array representation, which is sent to the browser as part of the Livewire request. If your model has a column containing binary data that is not valid UTF-8, such as a `geometry`, `point`, or `blob` column, it cannot be serialized to JSON and the modal will fail to open, often with no error in the Laravel log.
>
> To resolve this, add the column to [the `$hidden` array](https://laravel.com/docs/eloquent-serialization#hiding-attributes-from-json) on your model, which excludes it from the model's array and JSON representations:
>
> ```php
> protected $hidden = ['location'];
> ```
>
> This applies equally to the [View](view) and [Replicate](replicate) actions.

## Customizing data before saving

Sometimes, you may wish to modify form data before it is finally saved to the database. To do this, you may use the `mutateDataUsing()` method, which has access to the `$data` as an array, and returns the modified version:

```php
use Filament\Actions\EditAction;

EditAction::make()
    ->mutateDataUsing(function (array $data): array {
        $data['last_edited_by_id'] = auth()->id();

        return $data;
    })
```

<UtilityInjection set="actions">As well as `$data`, the `mutateDataUsing()` function can inject various utilities as parameters.</UtilityInjection>

## Customizing the saving process

You can tweak how the record is updated with the `using()` method:

```php
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;

EditAction::make()
    ->using(function (Model $record, array $data): Model {
        $record->update($data);

        return $record;
    })
```

<UtilityInjection set="actions">As well as `$record` and `$data`, the `using()` function can inject various utilities as parameters.</UtilityInjection>

## Redirecting after saving

You may set up a custom redirect when the form is submitted using the `successRedirectUrl()` method:

```php
use Filament\Actions\EditAction;

EditAction::make()
    ->successRedirectUrl(route('posts.list'))
```

If you want to redirect using the created record, use the `$record` parameter:

```php
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;

EditAction::make()
    ->successRedirectUrl(fn (Model $record): string => route('posts.view', [
        'post' => $record,
    ]))
```

<UtilityInjection set="actions">As well as `$record`, the `successRedirectUrl()` function can inject various utilities as parameters.</UtilityInjection>

## Customizing the save notification

When the record is successfully updated, a notification is dispatched to the user, which indicates the success of their action.

To customize the title of this notification, use the `successNotificationTitle()` method:

```php
use Filament\Actions\EditAction;

EditAction::make()
    ->successNotificationTitle('User updated')
```

<UtilityInjection set="actions">As well as allowing a static value, the `successNotificationTitle()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

You may customize the entire notification using the `successNotification()` method:

```php
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;

EditAction::make()
    ->successNotification(
       Notification::make()
            ->success()
            ->title('User updated')
            ->body('The user has been saved successfully.'),
    )
```

<UtilityInjection set="actions" extras="Notification;;Filament\Notifications\Notification;;$notification;;The default notification object, which could be a useful starting point for customization.">As well as allowing a static value, the `successNotification()` method also accepts a function to dynamically calculate it. You can inject various utilities into the function as parameters.</UtilityInjection>

To disable the notification altogether, use the `successNotification(null)` method:

```php
use Filament\Actions\EditAction;

EditAction::make()
    ->successNotification(null)
```

## Lifecycle hooks

Hooks may be used to execute code at various points within the action's lifecycle, like before a form is saved.

There are several available hooks:

```php
use Filament\Actions\EditAction;

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

<UtilityInjection set="actions">These hook functions can inject various utilities as parameters.</UtilityInjection>

## Halting the saving process

At any time, you may call `$action->halt()` from inside a lifecycle hook or mutation method, which will halt the entire saving process:

```php
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;

EditAction::make()
    ->before(function (EditAction $action, Post $record) {
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

## Resolving conflicting edits

You can opt in to concurrent-edit protection using `editConflictHandler()`:

```php
use Filament\Actions\EditAction;

EditAction::make()
    ->editConflictHandler()
```

Filament retains the original form values while the modal is open. Before validation, dehydration, upload storage, or save hooks, it locks the record on its database connection and compares the draft with current authorized values. Non-overlapping changes are combined; different changes to the same whole field open a recovery modal without closing the edit modal or saving its draft. Choosing a version updates the form only. Saving checks again atomically. Discarding the draft loads current values without saving. Ordinary Edit actions keep their existing behavior unless enabled.

> [!WARNING]
> Protection covers the form's changes to the edited record, not a universal transaction across related records, pivots, other connections, or external services. Table and resource-page actions retain their native query and relationship scope. Outside those integrations, provide an explicit model with `record($record)` or a [query-returning record resolver](#resolving-records-with-a-scoped-query). Model-returning custom resolvers, closures passed to `record()`, and arbitrary inherited schema record sources are not supported with conflict protection; Filament rejects these saves rather than replacing their scope with a generic query. Keep custom `using()` writes on the locked record's connection and implement protection for any independently edited related records yourself. Pending drafts are not retained across reloads or session expiry.

### Configuring comparison and previews

You can pass a configured `EditConflictHandler` or your own subclass:

```php
use Filament\Actions\EditAction;
use Filament\Forms\EditConflictHandler;

EditAction::make()
    ->editConflictHandler(
        EditConflictHandler::make()
            ->ignoredStatePaths(['search_text'])
            ->statePathGroups([
                'content' => ['title', 'body'],
            ]),
    )
```

Only configure `statePathGroups()` when replacing the default whole-field boundaries; include every field you want protected. Ignored paths can be overwritten without a warning. `editConflictHandler(false)` or `editConflictHandler(null)` disables protection. You can also pass a closure returning a handler. [Customizing conflict previews](../resources/editing-records#customizing-conflict-previews) describes the shared schema factories and retained comparison outcome.

### Loading current values

Current values reuse the action's configured `fillForm()` callback, including the built-in `mutateRecordDataUsing()` mapping, on an isolated form. Filament does not rerun `mountUsing()`, `beforeFormFilled()`, or `afterFormFilled()`. Keep data-loading and field hydration callbacks free of writes or external side effects.

If you customize how form values are loaded, configure that repeatable mapping using `fillForm()`. Conflict protection rejects saves when a custom `mountUsing()` callback has replaced the repeatable fill mapping, rather than replaying initialization or comparing the draft with itself.

Enabling `receiveRecordUpdates()` independently subscribes the action to record invalidations. Without conflict protection, these events retain the existing rerender-only behavior. Combining it with `editConflictHandler()` also updates untouched fields and advances only their accepted baseline:

```php
use Filament\Actions\EditAction;

EditAction::make()
    ->editConflictHandler()
    ->receiveRecordUpdates()
```

Locally changed fields and groups are retained, including edits made while a refresh is in flight. Current values reuse the same scoped record resolution, authorization and fill mapping as conflict recovery. Background updates do not save or accept conflict choices. Automatic field updates pause while a conflict comparison is open; choices and saving still check current values again. Custom `afterRecordUpdateReceived()` callbacks keep their normal behavior and may intentionally change form state, so do not refill the editable form indiscriminately in that callback. See [updating untouched values live](../resources/editing-records#updating-untouched-values-live) for the shared behavior and limits.

### Resolving records with a scoped query

When you resolve an action's record from a key, `resolveRecordUsing()` can return an Eloquent query builder instead of a fetched model:

```php
use App\Models\Post;
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;

EditAction::make()
    ->record((string) $postId)
    ->resolveRecordUsing(static fn (string $key): Builder => Post::query()
        ->whereKey($key)
        ->where('team_id', auth()->user()->team_id))
    ->editConflictHandler()
```

Return the query without calling `first()`, `find()`, or another method that fetches the record. Include its key and every application-specific scope. Filament fetches the record when needed, then reruns the resolver and executes a locking read inside the transaction for each protected save, recovery choice, or discard. Keep the resolver free of writes and external side effects. A record that no longer matches the query cannot be compared or saved.

Query-returning resolvers also work on action groups and mounted parent actions. Existing model-returning resolvers remain supported when conflict protection is disabled, but cannot provide a repeatable scoped locking read for protected saves. A resolver does not replace action authorization or protect independently edited related records.
