<?php

use Filament\Support\Components\Component;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('will run `setUp()` for new objects', function (): void {
    $component = new ChildComponent;
    $component->configure();

    expect($component->id)->toBe('child');
});

it('will run `configureUsing()` callbacks for new objects', function (): void {
    ChildComponent::configureUsing(function (ChildComponent $component): void {
        $component->id = 'child-configured';
    }, during: function (): void {
        $component = new ChildComponent;
        $component->configure();

        expect($component->id)->toBe('child-configured');
    });
});

it('will run `configureUsing()` callbacks registered for parents when a child object is instantiated', function (): void {
    ParentComponent::configureUsing(function (ParentComponent $component): void {
        $component->id = 'parent-configured';
        $component->configuredFrom = 'parent';
    }, during: function (): void {
        $component = new ChildComponent;
        $component->configure();

        expect($component->id)->toBe('child');
        expect($component->configuredFrom)->toBe('parent');
    });
});

it('will run `configureUsing()` callbacks registered for parents before their children', function (): void {
    GrandparentComponent::configureUsing(function (GrandparentComponent $component): void {
        $component->id = 'grandparent-configured';
        $component->configuredFrom = 'grandparent';
    }, during: function (): void {
        ParentComponent::configureUsing(function (ParentComponent $component): void {
            $component->id = 'parent-configured';
            $component->configuredFrom = 'parent';
        }, during: function (): void {
            $component = new GrandParentComponent;
            $component->configure();

            expect($component->id)->toBe('grandparent-configured');
            expect($component->configuredFrom)->toBe('grandparent');

            $component = new ParentComponent;
            $component->configure();

            expect($component->id)->toBe('parent-configured');
            expect($component->configuredFrom)->toBe('parent');

            $component = new ChildComponent;
            $component->configure();

            expect($component->id)->toBe('child');
            expect($component->configuredFrom)->toBe('parent');
        });
    });

    ParentComponent::configureUsing(function (ParentComponent $component): void {
        $component->id = 'parent-configured';
        $component->configuredFrom = 'parent';
    }, during: function (): void {
        GrandparentComponent::configureUsing(function (GrandparentComponent $component): void {
            $component->id = 'grandparent-configured';
            $component->configuredFrom = 'grandparent';
        }, during: function (): void {
            $component = new GrandParentComponent;
            $component->configure();

            expect($component->id)->toBe('grandparent-configured');
            expect($component->configuredFrom)->toBe('grandparent');

            $component = new ParentComponent;
            $component->configure();

            expect($component->id)->toBe('parent-configured');
            expect($component->configuredFrom)->toBe('parent');

            $component = new ChildComponent;
            $component->configure();

            expect($component->id)->toBe('child');
            expect($component->configuredFrom)->toBe('parent');
        });
    });
});

class GrandparentComponent extends Component
{
    public string $id = 'grandparent';

    public string $configuredFrom;
}

class ParentComponent extends GrandparentComponent
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->id = 'parent';
    }
}

class ChildComponent extends ParentComponent
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->id = 'child';
    }
}
