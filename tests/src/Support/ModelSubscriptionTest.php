<?php

use Filament\Support\Enums\ModelEvent;
use Filament\Support\ModelSubscription;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Shop\Order;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('combines `onlyEvents()` and `exceptEvents()` with exclusions taking precedence', function (): void {
    $subscription = ModelSubscription::make(Post::class)
        ->onlyEvents([ModelEvent::Created, ModelEvent::Updated, ModelEvent::Deleted])
        ->exceptEvents(['created', 'deleted']);

    expect($subscription->getEvents())->toBe([ModelEvent::Updated]);
});

it('can exclude events without an allowlist and reset both filters', function (): void {
    $subscription = ModelSubscription::make(Post::class)->exceptEvents([ModelEvent::Created, ModelEvent::Saved]);

    expect($subscription->getEvents())->toEqualCanonicalizing([
        ModelEvent::Updated,
        ModelEvent::Deleted,
        ModelEvent::Trashed,
        ModelEvent::Restored,
        ModelEvent::ForceDeleted,
    ]);

    $subscription->onlyEvents([]);

    expect($subscription->getEvents())->toBe([]);

    $subscription->onlyEvents(null)->exceptEvents([]);

    expect($subscription->getEvents())->toEqualCanonicalizing([
        ModelEvent::Created,
        ModelEvent::Updated,
        ModelEvent::Saved,
        ModelEvent::Deleted,
        ModelEvent::Trashed,
        ModelEvent::Restored,
        ModelEvent::ForceDeleted,
    ]);
});

it('can select all models and exclude exact model classes', function (): void {
    $subscription = ModelSubscription::make()->allModels()->exceptModels([Post::class]);

    expect($subscription->matchesModel(Post::class))->toBeFalse()
        ->and($subscription->matchesModel(User::class))->toBeTrue()
        ->and($subscription->matchesModel(Order::class))->toBeTrue();

    $subscription->exceptModels([]);

    expect($subscription->matchesModel(Post::class))->toBeTrue();

    $subscription->allModels(false);

    expect($subscription->matchesModel(Post::class))->toBeFalse();
});

it('matches nested namespaces without matching similar namespace prefixes', function (): void {
    $subscription = ModelSubscription::make()->modelsInNamespace('\\Filament\\Tests\\Fixtures\\Models\\');

    expect($subscription->matchesModel(Post::class))->toBeTrue()
        ->and($subscription->matchesModel(Order::class))->toBeTrue();

    $subscription->modelsInNamespace('Filament\\Tests\\Fixtures\\Model');

    expect($subscription->matchesModel(Post::class))->toBeFalse();

    $subscription->modelsInNamespace('Filament\\Tests\\Fixtures\\Models\\Shop');

    expect($subscription->matchesModel(Post::class))->toBeFalse()
        ->and($subscription->matchesModel(Order::class))->toBeTrue();
});

it('replaces model selectors without changing event filters', function (): void {
    $subscription = ModelSubscription::make(Post::class)->onlyEvents([ModelEvent::Updated]);
    $subscription->modelsInNamespace('Filament\\Tests\\Fixtures\\Models\\Shop');

    expect($subscription->matchesModel(Post::class))->toBeFalse()
        ->and($subscription->matchesModel(Order::class))->toBeTrue();

    $subscription->allModels()->model(User::class);

    expect($subscription->matchesModel(Post::class))->toBeFalse()
        ->and($subscription->matchesModel(Order::class))->toBeFalse()
        ->and($subscription->matchesModel(User::class))->toBeTrue()
        ->and($subscription->getEvents())->toBe([ModelEvent::Updated]);

    $subscription->model(null);

    expect($subscription->matchesModel(User::class))->toBeFalse();

    $subscription->allModels()->modelsInNamespace(null);

    expect($subscription->matchesModel(User::class))->toBeFalse();
});

it('rejects an empty namespace rather than subscribing to every model', function (): void {
    expect(static fn () => ModelSubscription::make()->modelsInNamespace('\\'))
        ->toThrow(InvalidArgumentException::class);
});
