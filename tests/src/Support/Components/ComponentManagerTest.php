<?php

use Filament\Support\Components\ComponentManager;
use Filament\Support\Components\Contracts\ScopedComponentManager;
use Filament\Support\Components\ViewComponent;
use Filament\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->manager = new ComponentManager;
});

it('implements `ScopedComponentManager`', function (): void {
    expect($this->manager)->toBeInstanceOf(ScopedComponentManager::class);
});

it('can be cloned', function (): void {
    $clone = $this->manager->clone();

    expect($clone)->toBeInstanceOf(ComponentManager::class);
    expect($clone)->not->toBe($this->manager);
});

describe('`configureUsing()`', function (): void {
    it('stores a configuration callback and returns an unregister closure', function (): void {
        $called = false;

        $unregister = $this->manager->configureUsing(
            TestConfigurableComponent::class,
            static function () use (&$called): void {
                $called = true;
            },
        );

        expect($unregister)->toBeInstanceOf(Closure::class);
    });

    it('returns the result of `$during` when provided', function (): void {
        $result = $this->manager->configureUsing(
            TestConfigurableComponent::class,
            static function (): void {},
            during: static fn (): string => 'during-result',
        );

        expect($result)->toBe('during-result');
    });

    it('removes configuration after `$during` completes', function (): void {
        $callCount = 0;

        $this->manager->configureUsing(
            TestConfigurableComponent::class,
            static function () use (&$callCount): void {
                $callCount++;
            },
            during: static fn (): string => 'done',
        );

        // After $during completes, configure a component - the callback should NOT be called
        $component = new TestConfigurableComponent;
        $this->manager->configure($component, static function (): void {});

        expect($callCount)->toBe(0);
    });

    it('can unregister a configuration using the returned closure', function (): void {
        $callCount = 0;

        $unregister = $this->manager->configureUsing(
            TestConfigurableComponent::class,
            static function () use (&$callCount): void {
                $callCount++;
            },
        );

        $unregister();

        $component = new TestConfigurableComponent;
        $this->manager->configure($component, static function (): void {});

        expect($callCount)->toBe(0);
    });

    it('stores important configurations separately', function (): void {
        $order = [];

        $this->manager->configureUsing(
            TestConfigurableComponent::class,
            static function () use (&$order): void {
                $order[] = 'normal';
            },
        );

        $this->manager->configureUsing(
            TestConfigurableComponent::class,
            static function () use (&$order): void {
                $order[] = 'important';
            },
            isImportant: true,
        );

        $component = new TestConfigurableComponent;
        $this->manager->configure($component, static function () use (&$order): void {
            $order[] = 'setUp';
        });

        // Normal configs run during class hierarchy, important after all classes
        expect($order)->toContain('normal');
        expect($order)->toContain('important');
        expect($order)->toContain('setUp');

        // Important should come last
        $importantIndex = array_search('important', $order);
        $normalIndex = array_search('normal', $order);
        expect($importantIndex)->toBeGreaterThan($normalIndex);
    });
});

describe('`configure()`', function (): void {
    it('calls `setUp` callback during configuration', function (): void {
        $setUpCalled = false;

        $component = new TestConfigurableComponent;
        $this->manager->configure($component, static function () use (&$setUpCalled): void {
            $setUpCalled = true;
        });

        expect($setUpCalled)->toBeTrue();
    });

    it('calls registered configuration callbacks', function (): void {
        $configuredValue = null;

        $this->manager->configureUsing(
            TestConfigurableComponent::class,
            static function ($component) use (&$configuredValue): void {
                $configuredValue = $component::class;
            },
        );

        $component = new TestConfigurableComponent;
        $this->manager->configure($component, static function (): void {});

        expect($configuredValue)->toBe(TestConfigurableComponent::class);
    });

    it('runs configurations for parent classes too', function (): void {
        $parentConfigured = false;

        $this->manager->configureUsing(
            TestConfigurableComponent::class,
            static function () use (&$parentConfigured): void {
                $parentConfigured = true;
            },
        );

        $component = new TestChildConfigurableComponent;
        $this->manager->configure($component, static function (): void {});

        expect($parentConfigured)->toBeTrue();
    });
});

describe('`extractPublicMethods()`', function (): void {
    it('returns public methods as closures', function (): void {
        $component = new TestConfigurableComponent;
        $methods = $this->manager->extractPublicMethods($component);

        expect($methods)->toBeArray();
        expect($methods)->not->toBeEmpty();
    });

    it('caches method reflection between calls', function (): void {
        $component1 = new TestConfigurableComponent;
        $component2 = new TestConfigurableComponent;

        $methods1 = $this->manager->extractPublicMethods($component1);
        $methods2 = $this->manager->extractPublicMethods($component2);

        // Same keys since same class
        expect(array_keys($methods1))->toBe(array_keys($methods2));
    });
});

describe('`resolve()`', function (): void {
    it('returns a `ScopedComponentManager` instance', function (): void {
        $resolved = ComponentManager::resolve();

        expect($resolved)->toBeInstanceOf(ScopedComponentManager::class);
    });
});

class TestConfigurableComponent extends ViewComponent
{
    protected string $view = 'livewire.form';

    public function testMethod(): string
    {
        return 'test';
    }
}

class TestChildConfigurableComponent extends TestConfigurableComponent {}
