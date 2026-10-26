<?php

use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire as LivewireFixture;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with a component name', function (): void {
    $livewire = Livewire::make('counter');

    expect($livewire->getComponent())->toBe('counter');
});

it('can set `component()` with a `Closure`', function (): void {
    $livewire = Livewire::make('initial')
        ->component(static fn (): string => 'dynamic-component');

    expect($livewire->getComponent())->toBe('dynamic-component');
});

it('can be constructed with data', function (): void {
    $livewire = Livewire::make('counter', ['count' => 5]);

    expect($livewire->getData())->toBe(['count' => 5]);
});

it('defaults `getData()` to an empty array', function (): void {
    $livewire = Livewire::make('counter');

    expect($livewire->getData())->toBe([]);
});

it('can set `data()` with a `Closure`', function (): void {
    $livewire = Livewire::make('counter')
        ->data(static fn (): array => ['count' => 10]);

    expect($livewire->getData())->toBe(['count' => 10]);
});

describe('lazy loading', function (): void {
    it('defaults `isLazyLoaded()` to `false`', function (): void {
        $livewire = Livewire::make('counter');

        expect($livewire->isLazyLoaded())->toBeFalse();
    });

    it('can enable and disable lazy loading', function (): void {
        $livewire = Livewire::make('counter')->lazyLoaded();

        expect($livewire->isLazyLoaded())->toBeTrue()
            ->and($livewire->lazyLoaded(false)->isLazyLoaded())->toBeFalse();
    });

    it('can configure lazy loading with a `Closure`', function (): void {
        $livewire = Livewire::make('counter')
            ->lazyLoaded(static fn (): bool => true);

        expect($livewire->isLazyLoaded())->toBeTrue();
    });
});

describe('component properties', function (): void {
    it('includes data in `getComponentProperties()`', function (): void {
        $livewire = Livewire::make('counter', ['count' => 5])
            ->container(Schema::make(LivewireFixture::make()));

        $properties = $livewire->getComponentProperties();

        expect($properties)->toHaveKey('count', 5);
    });

    it('includes the Livewire `lazy` property with `lazyLoaded()`', function (): void {
        $livewire = Livewire::make('counter')
            ->lazyLoaded()
            ->container(Schema::make(LivewireFixture::make()));

        $properties = $livewire->getComponentProperties();

        expect($properties)->toHaveKey('lazy', true);
    });

    it('excludes `lazy` from `getComponentProperties()` when not lazy', function (): void {
        $livewire = Livewire::make('counter')
            ->container(Schema::make(LivewireFixture::make()));

        $properties = $livewire->getComponentProperties();

        expect($properties)->not->toHaveKey('lazy');
    });

    it('includes `record` in `getComponentProperties()`', function (): void {
        $livewire = Livewire::make('counter')
            ->container(Schema::make(LivewireFixture::make()));

        $properties = $livewire->getComponentProperties();

        expect($properties)->toHaveKey('record');
    });
});

describe('ID', function (): void {
    it('returns `null` for `getId()` by default', function (): void {
        $livewire = Livewire::make('counter');

        expect($livewire->getId())->toBeNull();
    });

    it('can set `id()` and retrieve it via `getId()`', function (): void {
        $livewire = Livewire::make('counter')->id('my-counter');

        expect($livewire->getId())->toBe('my-counter');
    });
});
