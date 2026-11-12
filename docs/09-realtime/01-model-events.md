---
title: Model events
---

## Introduction

You can notify Filament when application data changes by subscribing to a model's completed lifecycle events in a service provider's `boot()` method:

```php
use App\Models\Post;
use Filament\Support\Facades\FilamentRealtime;

FilamentRealtime::subscribe(Post::class);
```

Filament dispatches a `RecordChanged` Laravel event for each matching model event. Notifications follow Laravel's after-commit event behavior: they wait until commit and are discarded on rollback. Outside a transaction, they are dispatched immediately.

With [websockets configured in your panel](../notifications/broadcast-notifications#setting-up-websockets-in-a-panel), these notifications also [keep resource record pages up to date](#refreshing-resource-pages).

## Selecting models

Pass a model class to `subscribe()` to observe that exact class, not its subclasses. It returns the registered `ModelSubscription`, which you can configure fluently:

```php
use App\Models\Post;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Facades\FilamentRealtime;

FilamentRealtime::subscribe(Post::class)
    ->exceptEvents([ModelEvent::Saved]);
```

### Observing models in a namespace

Use `modelsInNamespace()` to observe models in a namespace and its nested namespaces. Exclude specific model classes using `exceptModels()`:

```php
use App\Models\AuditLog;
use Filament\Support\Facades\FilamentRealtime;

FilamentRealtime::subscribe()
    ->modelsInNamespace('App\\Models')
    ->exceptModels([AuditLog::class]);
```

`App\Models` includes `App\Models\Shop\Order`, but not `App\ModelsExtra\Order`. Leading and trailing namespace separators are ignored; an empty namespace is rejected. Filament matches the class when a model event occurs, without scanning directories or loading every model class.

### Observing all models

Use `allModels()` to observe every model, including models provided by third-party packages:

```php
use App\Models\AuditLog;
use Filament\Support\Facades\FilamentRealtime;

FilamentRealtime::subscribe()
    ->allModels()
    ->exceptModels([AuditLog::class]);
```

All-model observation is always explicit. A subscription created using `subscribe()` without a model remains inactive until you select a model, namespace, or all models. Inactive subscriptions are not returned by `getSubscriptions()`.

The model selectors are mutually exclusive: selecting another model, namespace, or all models replaces the previous selection without changing event filters or model exclusions. Both `model(null)` and `modelsInNamespace(null)` clear the model selection entirely; `allModels(false)` disables all-model selection. Use `exceptModels([])` to clear model exclusions. Model exclusions apply to exact classes, not their subclasses.

### Combining subscriptions

When multiple subscriptions match a model, Filament selects the most-specific subscription, regardless of registration order:

1. The exact model class.
2. The most-specific matching namespace.
3. All models.

Only that subscription's event filters apply. Filters are not combined, and overlapping subscriptions do not produce duplicate notifications for the same model event. An exact-model subscription with `onlyEvents([])` silences that model even when broader subscriptions exist.

`exceptModels()` excludes models only from its own subscription. Another subscription can still observe those models. Removing an exact-model or namespace subscription allows a broader matching subscription to apply again.

## Selecting model events

By default, a subscription observes the completion events represented by the `Filament\Support\Enums\ModelEvent` enum: `Created`, `Updated`, `Saved`, `Deleted`, `Trashed`, `Restored`, and `ForceDeleted`. It does not observe reads or pre-write events such as `retrieved`, `creating`, or `updating` by default.

These names retain their Eloquent meanings. One operation can produce multiple notifications: creating a record emits both `created` and `saved`, for example. Eloquent also emits `saved` when saving an unchanged record. Soft-delete events only occur on models using `SoftDeletes`.

Use `onlyEvents()` to select a set of completion events:

```php
use App\Models\Post;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Facades\FilamentRealtime;

FilamentRealtime::subscribe(Post::class)
    ->onlyEvents([ModelEvent::Created, ModelEvent::Updated, ModelEvent::Deleted, ModelEvent::Restored]);
```

Alternatively, exclude events using `exceptEvents()`:

```php
use App\Models\Post;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Facades\FilamentRealtime;

FilamentRealtime::subscribe(Post::class)
    ->exceptEvents([ModelEvent::Saved]);
```

You can combine both methods with any model selector. Event exclusions take precedence. `onlyEvents([])` observes nothing, `onlyEvents(null)` removes the event allowlist, and `exceptEvents([])` clears event exclusions.

Quiet writes, mass updates or deletes through a query, raw SQL, and changes made by other applications do not emit Eloquent model events. Notify Filament manually for these operations.

> [!NOTE]
> Automatic observation requires Laravel's string Eloquent event to reach Filament's listener. Laravel can stop propagation, for example when an `updated` observer returns `false`, or skip the string event because a `$dispatchesEvents` listener returned a result. A write may succeed without Filament receiving that event. If your integration suppresses these events, dispatch `RecordChanged` manually from that integration instead.

### Observing custom model events

Both `onlyEvents()` and `exceptEvents()` also accept custom event names as strings or backed enums. For example, define an application enum for a custom Eloquent event:

```php
namespace App\Enums;

enum PostEvent: string
{
    case Published = 'published';
}
```

You can combine it with the built-in events:

```php
use App\Enums\PostEvent;
use App\Models\Post;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Facades\FilamentRealtime;

FilamentRealtime::subscribe(Post::class)
    ->onlyEvents([ModelEvent::Updated, PostEvent::Published]);
```

Enums and strings are matched by their underlying event name, so an enum exclusion also excludes the equivalent string. `getEvents()` preserves the selected enum cases or strings, and automatic notifications retain the matching subscription's enum case.

Custom events are not observed by default: select them explicitly using `onlyEvents()`. Your model must emit the corresponding Eloquent event after a successful write; subscribing does not define or fire the event. String event names remain unrestricted, including pre-write names if you explicitly select them. Prefer enum cases or application constants to avoid typos.

## Managing model subscriptions

`subscribe()` returns the live registered subscription. You can keep this object and change its event filters or model selector later. The registry uses its current selector for observation, inspection, and removal. When multiple subscriptions resolve to the same selector, the most recently registered one replaces the others without duplicating notifications. Exact-model selectors, namespaces, and the all-model selector each have their own registration.

`getSubscriptions()` returns copies of the active subscriptions, keyed by their current `getKey()`. `getSubscription(Post::class)` returns a copy of the effective subscription for that model, or `null` when none matches. Changing these copies does not change the registry; use the object returned by `subscribe()` or register a replacement instead.

You can inspect subscriptions or remove one:

```php
use App\Models\Post;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Support\ModelSubscription;

$subscriptions = FilamentRealtime::getSubscriptions();

FilamentRealtime::unsubscribe(Post::class);
FilamentRealtime::unsubscribe(ModelSubscription::make()->modelsInNamespace('App\\Models'));
FilamentRealtime::unsubscribe(ModelSubscription::make()->allModels());
```

You can also pass a subscription to `unsubscribe()`, including an exact-model subscription. Removal uses its current selector, not event filters. To remove an inactive subscription without a selector, pass the object returned by `subscribe()`. The registry contains subscription rules, not a discovered list of all matching model classes.

The registry is application-wide and persists for the lifetime of the application container, including across requests in long-running workers. Configure it in a service provider, not from tenant-specific or user-specific request state. It is not a registry of connected users and does not authorize access to records.

### Registering prepared subscriptions

You can also pass a `ModelSubscription` created using `make()`. Filament copies its configuration and returns the registered copy, so changes to the original object do not affect the registry:

```php
use App\Models\Post;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Support\ModelSubscription;

$subscription = ModelSubscription::make(Post::class)
    ->onlyEvents([ModelEvent::Updated]);

$registeredSubscription = FilamentRealtime::subscribe($subscription);
```

## Dispatching changes manually

Dispatch a change for a record without configuring automatic observation:

```php
use Filament\Support\Events\RecordChanged;

RecordChanged::make($post)->dispatch();
```

You may attach a built-in event using its enum case:

```php
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\RecordChanged;

RecordChanged::make($post)
    ->event(ModelEvent::Updated)
    ->dispatch();
```

Custom names, including application-backed enums, are also supported:

```php
use App\Enums\PostEvent;
use Filament\Support\Events\RecordChanged;

RecordChanged::make($post)
    ->event(PostEvent::Published)
    ->dispatch();
```

Manual notifications follow the same transaction rules as automatic notifications, but bypass model subscription filters. They do not save the record or verify that a write occurred. Avoid manually notifying for a write that already produces an automatic notification unless you intend both events.

## Refreshing resource pages

Resource record pages automatically listen for changes when their model has an active subscription and your panel has broadcasting enabled with `broadcasting.echo` configured in `config/filament.php`. There is no separate resource opt-in. Namespace and all-model subscriptions work too; the effective subscription's event filters and exclusions still apply.

Configure Laravel's broadcaster to use the same websocket application as Echo, and run a queue worker if your queue connection is asynchronous. There is no polling fallback; a successful reconnection refreshes the page to catch up with missed changes.

Quiet, bulk, raw SQL, and external writes need a [manual change notification](#dispatching-changes-manually), with the model subscription still configured to enable the page listener. Explicit `retrieved` notifications remain backend-only to avoid refresh loops.

### Preserving page state

Changes made in another session or a background job re-render the page, updating its heading, breadcrumbs, custom header and footer content, and widget selection. Your `getHeading()` and `getTitle()` overrides are respected, and the browser title is updated too. The page is not remounted. Edit forms retain unsaved input; view pages refresh their infolists and refill disabled forms using `mutateFormDataBeforeFill()` and normal field hydration, without calling `beforeFill()` or `afterFill()` again. Open actions are preserved. Existing child Livewire components, such as widgets and relation managers, retain their own state and do not automatically refresh.

Edit pages using [concurrent-edit protection](../resources/editing-records#updating-untouched-values-live) also update untouched fields, preserving local edits and advancing only accepted comparison baselines. Unprotected Edit pages retain the rerender-only behavior.

### Refreshing view action modals

Mounted `ViewAction` modals also listen for their record's changes when its effective model subscription and Echo configuration are active. Panel broadcasting must be enabled when using a panel. Infolists and record-backed modal content re-render, and disabled action forms refill using their configured `fillForm()` data, including `mutateRecordDataUsing()` and translatable content handling. Editing parent or child actions retain their drafts.

The action is not remounted, and `beforeFormFilled()` and `afterFormFilled()` do not run again. Put repeatable data mapping in `fillForm()` or `mutateRecordDataUsing()`, not those hooks. A custom `mountUsing()` callback is not replayed and its form state is not automatically refilled. Enabled action schemas are not refilled. Field hydration callbacks may run during refilling, so keep them safe to repeat.

Subscriptions close when the modal unmounts. Successful resubscription catches up missed changes. Refreshing and signing re-resolve the mounted action through its existing table, relationship, schema or page context and recheck action authorization. No model attributes are broadcast, and no polling is used. When the record can no longer be resolved or authorized, the action receives no fresh values; the normal action lifecycle may close its modal. Soft-deleted records remain available if the action's existing scope includes them.

### Refreshing other action modals

To listen for arbitrary broadcast events, including on actions without records, use [`webSocketListeners()`](../actions/modals#listening-for-websocket-events-while-a-modal-is-open). The record-update API below builds on the same modal transport, adding automatic model subscription selection, scoped authorization and reconnect catch-up.

Other mounted, record-backed actions can opt in using `receiveRecordUpdates()`. They use the same model subscription, Echo configuration and authorization checks as view actions:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;

Action::make('review')
    ->receiveRecordUpdates()
    ->schema([
        TextEntry::make('title'),
        TextInput::make('note'),
    ])
```

By default, the action's modal re-renders without refilling its form or changing drafts. Entries read from the current record, not the action's form data. Use `receiveRecordUpdates(false)` to disable updates, including the automatic updates on a `ViewAction`. The condition also accepts a closure.

An `EditAction` combining `receiveRecordUpdates()` with [`editConflictHandler()`](../actions/edit#loading-current-values) also updates untouched fields while preserving locally edited fields and groups. This does not enable autosave or accept conflict choices.

You can provide a repeatable PHP handler using `afterRecordUpdateReceived()`. It runs after the record has been resolved and authorized, before rendering. The handler receives normal action utility injections, including the current `$record`, `$action` and `$livewire`. The `$schema` and `$form` injections both refer to that action's own modal schema, even when a child action is open. For example, you can update a disabled status field without replacing editable fields:

```php
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

Action::make('review')
    ->receiveRecordUpdates()
    ->schema([
        TextInput::make('note'),
        TextInput::make('last_updated_at')->disabled(),
    ])
    ->afterRecordUpdateReceived(function (Post $record, Schema $schema): void {
        $schema->fillPartially([
            'last_updated_at' => $record->updated_at,
        ], ['last_updated_at']);
    })
```

Registering a handler does not itself enable listening; also call `receiveRecordUpdates()`. Passing `null` removes the handler. On a `ViewAction`, a custom handler replaces its default disabled-form refilling. Callback-requested partial or full rendering is preserved; the action's modal is the default render target, not a restriction on additional rendering.

> [!WARNING]
> The handler receives an invalidation, not a once-per-save event or an event payload. It also runs when the subscription connects or reconnects, and notifications may repeat or coalesce. Keep it safe to repeat. Listening does not enable edit-conflict protection: replacing editable state can overwrite input typed while a request is in flight, so draft reconciliation needs its own concurrency checks. Subscription authorization is not a substitute for authorization and conflict checks when saving.

### Authorizing subscriptions

Broadcasts contain no model attributes and use private record-specific channels. Filament authorizes subscriptions through the resource page or mounted action and rechecks record scope and access when refreshing. You do not need a channel authorization callback for these built-in subscriptions; your own channels still need their own authorization. Records that can no longer be resolved or authorized leave the current screen and unsaved input in place.

## Listening for changes

Register a Laravel listener for the shared event:

```php
use Filament\Support\Events\RecordChanged;
use Illuminate\Support\Facades\Event;

Event::listen(RecordChanged::class, function (RecordChanged $event): void {
    $record = $event->getRecord();
    $name = $event->getEvent(); // Null for an unnamed manual notification.

    // Handle the change in your backend integration.
});
```

`getEvent()` preserves the configured enum case, including application-backed enums, so you can compare directly with `ModelEvent::Updated` or `PostEvent::Published`. String names matching a built-in event are converted to the corresponding `ModelEvent` case. Custom string names remain strings, and unnamed manual notifications return `null`.

Notifications dispatched using `dispatch()` contain a shallow copy of the model at notification time, rather than a model reloaded after commit. Later attribute assignments to the original model do not replace that snapshot, and deleted records can still be identified. Related models and mutable objects are not deep-copied. Notifications are change signals, not an ordered change log: nested transactions can deliver them out of write order. Re-query data when your integration needs the current database state.

This event is backend-only: do not expose its model attributes to clients without appropriate authorization and payload filtering.

## Testing model changes

You can test your application's integration using Laravel's event fake and the public `RecordChanged` event. Keep your model subscriptions in your application's service provider, then exercise the application code that writes or manually reports a change:

> [!NOTE]
> Fake only `RecordChanged`, not all events: a bare `Event::fake()` also prevents the Eloquent events that trigger automatic notifications.

```php
use App\Models\Post;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\RecordChanged;
use Illuminate\Support\Facades\Event;

it('notifies Filament when a post is updated', function (): void {
    $post = Post::factory()->create();

    Event::fake([RecordChanged::class]);

    $post->update(['title' => 'Updated title']);

    Event::assertDispatched(RecordChanged::class, static fn (RecordChanged $event): bool =>
        $event->getRecord()->is($post)
        && ($event->getEvent() === ModelEvent::Updated));
});
```

This example assumes your application subscribes to the post's `Updated` event. In a feature test, replace the direct update with your application's action or request. You do not need to mock the realtime manager or call its model-event handler.

Use `Event::assertNotDispatched()` to check that a change was not reported, or `Event::assertDispatchedTimes()` when the notification count is part of your application's contract. Match the record and event rather than asserting a total count when other lifecycle events may also be reported.

Make assertions after your application's transaction completes. Laravel's event fake honors after-commit dispatch, so notifications from a transaction that rolls back are not recorded. Use Laravel's normal database-testing traits; do not manually commit a test's wrapping transaction to force event dispatch.

These assertions verify that your application emitted the change signal. Faked listeners do not run, and the assertions do not verify downstream processing or delivery to a browser.
