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

Filament dispatches a `ModelChanged` Laravel event for each matching model event. Notifications follow Laravel's after-commit event behavior: they wait until commit and are discarded on rollback. Outside a transaction, they are dispatched immediately.

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
> Automatic observation requires Laravel's string Eloquent event to reach Filament's listener. Laravel can stop propagation, for example when an `updated` observer returns `false`, or skip the string event because a `$dispatchesEvents` listener returned a result. A write may succeed without Filament receiving that event. If your integration suppresses these events, dispatch `ModelChanged` manually from that integration instead.

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
use Filament\Support\Events\ModelChanged;

ModelChanged::make($post)->dispatch();
```

You may attach a built-in event using its enum case:

```php
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\ModelChanged;

ModelChanged::make($post)
    ->event(ModelEvent::Updated)
    ->dispatch();
```

Custom names, including application-backed enums, are also supported:

```php
use App\Enums\PostEvent;
use Filament\Support\Events\ModelChanged;

ModelChanged::make($post)
    ->event(PostEvent::Published)
    ->dispatch();
```

Manual notifications follow the same transaction rules as automatic notifications, but bypass model subscription filters. They do not save the record or verify that a write occurred. Avoid manually notifying for a write that already produces an automatic notification unless you intend both events.

## Refreshing resource pages

Resource record pages automatically listen for changes when their model has an active subscription and your panel has broadcasting enabled with `broadcasting.echo` configured in `config/filament.php`. There is no separate resource opt-in. Namespace and all-model subscriptions work too; the effective subscription's event filters and exclusions still apply.

Configure Laravel's broadcaster to use the same websocket application as Echo, and run a queue worker if your queue connection is asynchronous. There is no polling fallback; a successful reconnection refreshes the page to catch up with missed changes.

Quiet, bulk, raw SQL, and external writes need a [manual change notification](#dispatching-changes-manually), with the model subscription still configured to enable the page listener. Explicit `retrieved` notifications remain backend-only to avoid refresh loops.

### Preserving page state

Changes made in another session or a background job re-render the page, updating its heading, breadcrumbs, custom header and footer content, and widget selection. Your `getHeading()` and `getTitle()` overrides are respected, and the browser title is updated too. The page is not remounted or its forms refilled, so unsaved input and open actions are preserved. Existing child Livewire components, such as widgets and relation managers, retain their own state and do not automatically refresh.

### Authorizing subscriptions

Broadcasts contain no model attributes and use private record-specific channels. Filament authorizes subscriptions through the resource page and rechecks record scope and page access when refreshing. You do not need a channel authorization callback for these built-in subscriptions; your own channels still need their own authorization. Deleted or inaccessible records leave the current screen and unsaved input in place.

## Listening for changes

Register a Laravel listener for the shared event:

```php
use Filament\Support\Events\ModelChanged;
use Illuminate\Support\Facades\Event;

Event::listen(ModelChanged::class, function (ModelChanged $event): void {
    $record = $event->getRecord();
    $name = $event->getEvent(); // Null for an unnamed manual notification.

    // Handle the change in your backend integration.
});
```

`getEvent()` preserves the configured enum case, including application-backed enums, so you can compare directly with `ModelEvent::Updated` or `PostEvent::Published`. String names matching a built-in event are converted to the corresponding `ModelEvent` case. Custom string names remain strings, and unnamed manual notifications return `null`.

Notifications dispatched using `dispatch()` contain a shallow copy of the model at notification time, rather than a model reloaded after commit. Later attribute assignments to the original model do not replace that snapshot, and deleted records can still be identified. Related models and mutable objects are not deep-copied. Notifications are change signals, not an ordered change log: nested transactions can deliver them out of write order. Re-query data when your integration needs the current database state.

This event is backend-only: do not expose its model attributes to clients without appropriate authorization and payload filtering.

## Testing model changes

You can test your application's integration using Laravel's event fake and the public `ModelChanged` event. Keep your model subscriptions in your application's service provider, then exercise the application code that writes or manually reports a change:

> [!NOTE]
> Fake only `ModelChanged`, not all events: a bare `Event::fake()` also prevents the Eloquent events that trigger automatic notifications.

```php
use App\Models\Post;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\ModelChanged;
use Illuminate\Support\Facades\Event;

it('notifies Filament when a post is updated', function (): void {
    $post = Post::factory()->create();

    Event::fake([ModelChanged::class]);

    $post->update(['title' => 'Updated title']);

    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool =>
        $event->getRecord()->is($post)
        && ($event->getEvent() === ModelEvent::Updated));
});
```

This example assumes your application subscribes to the post's `Updated` event. In a feature test, replace the direct update with your application's action or request. You do not need to mock the realtime manager or call its model-event handler.

Use `Event::assertNotDispatched()` to check that a change was not reported, or `Event::assertDispatchedTimes()` when the notification count is part of your application's contract. Match the record and event rather than asserting a total count when other lifecycle events may also be reported.

Make assertions after your application's transaction completes. Laravel's event fake honors after-commit dispatch, so notifications from a transaction that rolls back are not recorded. Use Laravel's normal database-testing traits; do not manually commit a test's wrapping transaction to force event dispatch.

These assertions verify that your application emitted the change signal. Faked listeners do not run, and the assertions do not verify downstream processing or delivery to a browser.
