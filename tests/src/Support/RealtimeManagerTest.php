<?php

use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\ModelChanged;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Support\ModelSubscription;
use Filament\Tests\Fixtures\Enums\ObservablePostEvent;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Event;

uses(TestCase::class);

beforeEach(function (): void {
    $this->events = [];

    Event::listen(ModelChanged::class, function (ModelChanged $event): void {
        $this->events[] = $event;
    });
});

it('observes completion events without observing reads or pre-write events', function (): void {
    FilamentRealtime::subscribe(model: Post::class);

    $post = Post::factory()->create(['title' => 'Original']);
    $post->refresh();
    $post->update(['title' => 'Updated']);
    $post->delete();
    $post->restore();
    $post->forceDelete();

    expect(array_map(static fn (ModelChanged $event): BackedEnum | string | null => $event->getEvent(), $this->events))
        ->toBe([ModelEvent::Created, ModelEvent::Saved, ModelEvent::Updated, ModelEvent::Saved, ModelEvent::Trashed, ModelEvent::Deleted, ModelEvent::Updated, ModelEvent::Saved, ModelEvent::Restored, ModelEvent::Deleted, ModelEvent::ForceDeleted]);

    foreach ($this->events as $event) {
        expect($event->getRecord())->toBeSameModel($post);
    }

    expect($this->events[0]->getRecord()->getAttribute('title'))->toBe('Original');
});

it('does not observe models until subscribed or after unsubscribing', function (): void {
    Event::fake([ModelChanged::class]);

    $post = Post::factory()->create();

    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);
    User::factory()->create();
    $post->update(['title' => 'Observed']);

    FilamentRealtime::unsubscribe(Post::class);
    $post->update(['title' => 'Not observed']);

    Event::assertDispatchedTimes(ModelChanged::class, 1);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($post)
        && ($event->getRecord()->getAttribute('title') === 'Observed')
        && ($event->getEvent() === ModelEvent::Updated));

    expect(FilamentRealtime::getSubscriptions())->toBe([]);
});

it('keeps unconfigured subscriptions inactive until a model selector is chosen', function (): void {
    $subscription = FilamentRealtime::subscribe()->onlyEvents([ModelEvent::Updated]);
    $post = Post::factory()->create();

    expect(FilamentRealtime::getSubscriptions())->toBe([])
        ->and($this->events)->toBe([]);

    $subscription->model(Post::class);
    $post->update(['title' => 'Observed']);

    $subscription->model(null);
    $post->update(['title' => 'Inactive']);

    expect(FilamentRealtime::getSubscriptions())->toBe([])
        ->and($this->events)->toHaveCount(1)
        ->and($this->events[0]->getRecord()->title)->toBe('Observed')
        ->and($this->events[0]->getEvent())->toBe(ModelEvent::Updated);

    FilamentRealtime::unsubscribe($subscription);
    $subscription->allModels();
    $post->update(['title' => 'Unsubscribed']);

    expect($this->events)->toHaveCount(1);
});

it('replaces subscriptions without duplicating notifications or sharing mutable configuration', function (): void {
    $subscription = ModelSubscription::make(Post::class)->onlyEvents([ModelEvent::Created]);
    FilamentRealtime::subscribe($subscription);
    $registeredSubscription = FilamentRealtime::subscribe($subscription);
    $subscription->onlyEvents([ModelEvent::Deleted]);
    FilamentRealtime::getSubscriptions()[Post::class]->onlyEvents([]);
    FilamentRealtime::getSubscription(Post::class)->onlyEvents([]);

    $post = Post::factory()->create();

    $registeredSubscription->onlyEvents([ModelEvent::Updated]);
    $post->update(['title' => 'Updated']);

    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Deleted]);
    $post->update(['title' => 'Not observed']);
    $post->delete();

    expect(array_map(static fn (ModelChanged $event): BackedEnum | string | null => $event->getEvent(), $this->events))
        ->toBe([ModelEvent::Created, ModelEvent::Updated, ModelEvent::Deleted])
        ->and(FilamentRealtime::getSubscriptions())->toHaveCount(1);
});

it('uses current selectors when inspecting or removing live subscriptions', function (): void {
    $post = Post::factory()->create();
    $author = $post->author;
    Event::fake([ModelChanged::class]);

    $subscription = FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);
    $subscription->modelsInNamespace('Filament\\Tests\\Fixtures\\Models')->exceptModels([User::class]);

    expect(array_keys(FilamentRealtime::getSubscriptions()))->toBe(['Filament\\Tests\\Fixtures\\Models\\*']);

    FilamentRealtime::unsubscribe(Post::class);
    $post->update(['title' => 'Namespace']);
    $author->update(['name' => 'Excluded']);

    Event::assertDispatchedTimes(ModelChanged::class, 1);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($post)
        && ($event->getRecord()->title === 'Namespace')
        && ($event->getEvent() === ModelEvent::Updated));

    $subscription->allModels()->exceptModels([]);
    $author->update(['name' => 'All models']);

    expect(array_keys(FilamentRealtime::getSubscriptions()))->toBe(['*']);
    Event::assertDispatchedTimes(ModelChanged::class, 2);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($author)
        && ($event->getRecord()->name === 'All models')
        && ($event->getEvent() === ModelEvent::Updated));

    FilamentRealtime::unsubscribe(ModelSubscription::make()->allModels());
    $subscription->model(Post::class);
    $post->update(['title' => 'Unsubscribed']);

    expect(FilamentRealtime::getSubscriptions())->toBe([]);
    Event::assertDispatchedTimes(ModelChanged::class, 2);
});

it('keeps the latest registration when live selectors collide without reviving replaced subscriptions', function (bool $inspectBetweenChanges): void {
    $post = Post::factory()->create();
    Event::fake([ModelChanged::class]);

    $olderSubscription = FilamentRealtime::subscribe()
        ->modelsInNamespace('Filament\\Tests\\Fixtures\\Models')
        ->onlyEvents([ModelEvent::Created]);
    $newerSubscription = FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);

    $olderSubscription->model(Post::class);

    if ($inspectBetweenChanges) {
        expect(array_keys(FilamentRealtime::getSubscriptions()))->toBe([Post::class]);
    }

    $olderSubscription->modelsInNamespace('Filament\\Tests\\Fixtures\\Models');
    $newerSubscription->allModels()->onlyEvents([ModelEvent::Saved]);
    $createdPost = Post::factory()->create(['author_id' => $post->author_id]);

    expect(array_keys(FilamentRealtime::getSubscriptions()))->toBe(['*']);
    Event::assertDispatchedTimes(ModelChanged::class, 1);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($createdPost)
        && ($event->getEvent() === ModelEvent::Saved));
})->with(['without inspection' => false, 'with inspection' => true]);

it('replaces subscriptions consistently through direct and fluent registration before any observation', function (bool $registerFluently, bool $inspectBetweenChanges): void {
    $post = Post::factory()->create();
    $author = $post->author;
    Event::fake([ModelChanged::class]);

    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);
    $subscription = $registerFluently
        ? FilamentRealtime::subscribe()->model(Post::class)
        : FilamentRealtime::subscribe(Post::class);

    if ($inspectBetweenChanges) {
        expect(array_keys(FilamentRealtime::getSubscriptions()))->toBe([Post::class]);
    }

    $subscription->model(User::class)->onlyEvents([ModelEvent::Updated]);
    $post->update(['title' => 'Replaced subscription']);
    $author->update(['name' => 'Current subscription']);

    expect(array_keys(FilamentRealtime::getSubscriptions()))->toBe([User::class]);
    Event::assertDispatchedTimes(ModelChanged::class, 1);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($author)
        && ($event->getEvent() === ModelEvent::Updated));
})->with(['direct' => false, 'fluent' => true])->with(['without inspection' => false, 'with inspection' => true]);

it('observes custom Eloquent events using backed enums and strings', function (): void {
    $post = Post::factory()->create();
    $record = ObservablePost::query()->findOrFail($post->getKey());

    FilamentRealtime::subscribe(
        ModelSubscription::make(ObservablePost::class)
            ->onlyEvents([ModelEvent::Updated, ObservablePostEvent::Published])
            ->exceptEvents(['updated']),
    );

    $record->publish();

    expect($this->events)->toHaveCount(1)
        ->and($this->events[0]->getEvent())->toBe(ObservablePostEvent::Published)
        ->and($this->events[0]->getRecord())->toBeSameModel($record)
        ->and($record->fresh()->is_published)->toBeTrue()
        ->and(FilamentRealtime::getSubscriptions()[ObservablePost::class]->getEvents())->toBe([ObservablePostEvent::Published]);

    FilamentRealtime::subscribe(ModelSubscription::make(ObservablePost::class)->onlyEvents(['published']));
    $record->publish();

    expect($this->events)->toHaveCount(2)
        ->and($this->events[1]->getEvent())->toBe('published');

    FilamentRealtime::subscribe(
        ModelSubscription::make(ObservablePost::class)
            ->onlyEvents(['published'])
            ->exceptEvents([ObservablePostEvent::Published]),
    );

    $record->publish();

    expect($this->events)->toHaveCount(2);

    FilamentRealtime::subscribe(ModelSubscription::make(ObservablePost::class)->exceptEvents(ModelEvent::cases()));
    $record->publish();

    expect($this->events)->toHaveCount(2);
});

it('does not notify for cancelled writes, quiet writes, or bulk queries', function (): void {
    $post = Post::factory()->create(['title' => 'Original']);
    FilamentRealtime::subscribe(ModelSubscription::make(Post::class));

    Post::updating(static fn (): bool => false);

    expect($post->update(['title' => 'Cancelled']))->toBeFalse();
    expect($post->fresh()->title)->toBe('Original');

    $post->updateQuietly(['title' => 'Quiet']);
    Post::query()->whereKey($post)->update(['title' => 'Bulk']);

    expect($post->fresh()->title)->toBe('Bulk')
        ->and($this->events)->toBe([]);
});

it('uses the most-specific model subscription regardless of registration order', function (bool $reverse): void {
    $post = Post::factory()->create();
    Event::fake([ModelChanged::class]);

    $subscriptions = [
        ModelSubscription::make()->allModels()->onlyEvents([ModelEvent::Created]),
        ModelSubscription::make()->modelsInNamespace('Filament\\Tests')->onlyEvents([ModelEvent::Deleted]),
        ModelSubscription::make()->modelsInNamespace('Filament\\Tests\\Fixtures\\Models')->onlyEvents([ModelEvent::Saved]),
        ModelSubscription::make(Post::class)->onlyEvents([ModelEvent::Updated]),
    ];

    foreach ($reverse ? array_reverse($subscriptions) : $subscriptions as $subscription) {
        FilamentRealtime::subscribe($subscription);
    }

    $post->update(['title' => 'Exact']);

    FilamentRealtime::subscribe(ModelSubscription::make(Post::class)->onlyEvents([]));
    $post->update(['title' => 'Muted']);

    Event::assertDispatchedTimes(ModelChanged::class, 1);

    FilamentRealtime::unsubscribe(Post::class);
    $post->update(['title' => 'Namespace']);

    FilamentRealtime::unsubscribe(ModelSubscription::make()->modelsInNamespace('Filament\\Tests\\Fixtures\\Models'));
    $post->delete();

    FilamentRealtime::unsubscribe(ModelSubscription::make()->modelsInNamespace('Filament\\Tests'));
    $createdPost = Post::factory()->create(['author_id' => $post->author_id]);

    Event::assertDispatchedTimes(ModelChanged::class, 4);

    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($post)
        && ($event->getRecord()->title === 'Exact')
        && ($event->getEvent() === ModelEvent::Updated));
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($post)
        && ($event->getRecord()->title === 'Namespace')
        && ($event->getEvent() === ModelEvent::Saved));
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($post)
        && ($event->getEvent() === ModelEvent::Deleted));
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($createdPost)
        && ($event->getEvent() === ModelEvent::Created));
})->with(['broad first' => false, 'exact first' => true]);

it('applies model exclusions only to their owning subscription', function (): void {
    $author = User::factory()->create();
    Event::fake([ModelChanged::class]);

    FilamentRealtime::subscribe(
        ModelSubscription::make()->allModels()->exceptModels([User::class])->onlyEvents([ModelEvent::Created]),
    );
    FilamentRealtime::subscribe(
        ModelSubscription::make()->modelsInNamespace('Filament\\Tests\\Fixtures\\Models')->exceptModels([Post::class])->onlyEvents([ModelEvent::Saved]),
    );

    $post = Post::factory()->create(['author_id' => $author->getKey()]);
    $user = User::factory()->create();

    Event::assertDispatchedTimes(ModelChanged::class, 2);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($post)
        && ($event->getEvent() === ModelEvent::Created));
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($user)
        && ($event->getEvent() === ModelEvent::Saved));
});

it('can replace and remove broad subscriptions without sharing mutable configuration', function (): void {
    $author = User::factory()->create();
    Event::fake([ModelChanged::class]);

    $subscription = ModelSubscription::make()->modelsInNamespace('Filament\\Tests\\Fixtures\\Models')->onlyEvents([ModelEvent::Created]);
    FilamentRealtime::subscribe($subscription);
    FilamentRealtime::subscribe($subscription);
    $subscription->allModels()->onlyEvents([ModelEvent::Deleted]);

    foreach (FilamentRealtime::getSubscriptions() as $registeredSubscription) {
        $registeredSubscription->onlyEvents([]);
    }

    $post = Post::factory()->create(['author_id' => $author->getKey()]);

    FilamentRealtime::unsubscribe(ModelSubscription::make()->modelsInNamespace('\\Filament\\Tests\\Fixtures\\Models\\'));
    Post::factory()->create(['author_id' => $author->getKey()]);

    FilamentRealtime::subscribe()->allModels()->onlyEvents([ModelEvent::Created]);
    FilamentRealtime::subscribe()->allModels()->onlyEvents([ModelEvent::Saved]);
    $user = User::factory()->create();

    FilamentRealtime::unsubscribe(ModelSubscription::make()->allModels());
    User::factory()->create();

    Event::assertDispatchedTimes(ModelChanged::class, 2);
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($post)
        && ($event->getEvent() === ModelEvent::Created));
    Event::assertDispatched(ModelChanged::class, static fn (ModelChanged $event): bool => $event->getRecord()->is($user)
        && ($event->getEvent() === ModelEvent::Saved));
    expect(FilamentRealtime::getSubscriptions())->toBe([]);
});

class ObservablePost extends Post
{
    protected $table = 'posts';

    protected $observables = ['published'];

    public function publish(): void
    {
        $this->update(['is_published' => true]);

        $this->fireModelEvent(ObservablePostEvent::Published->value, halt: false);
    }
}
