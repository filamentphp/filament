<?php

use Filament\Support\CliManager;
use Filament\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->manager = new CliManager;
});

describe('component locations', function (): void {
    it('returns empty array for `getComponentLocations()` by default', function (): void {
        expect($this->manager->getComponentLocations())->toBe([]);
    });

    it('can register a component location', function (): void {
        $this->manager->registerComponentLocation(
            '/app/Forms/Components',
            'App\\Forms\\Components',
            'app',
        );

        $locations = $this->manager->getComponentLocations();

        expect($locations)->toHaveKey('App\\Forms\\Components');
        expect($locations['App\\Forms\\Components'])->toBe([
            'path' => '/app/Forms/Components',
            'viewNamespace' => 'app',
        ]);
    });

    it('can register a component location with `null` view namespace', function (): void {
        $this->manager->registerComponentLocation(
            '/app/Components',
            'App\\Components',
            null,
        );

        $locations = $this->manager->getComponentLocations();

        expect($locations['App\\Components']['viewNamespace'])->toBeNull();
    });

    it('can register multiple component locations', function (): void {
        $this->manager->registerComponentLocation('/first', 'First\\Namespace', 'first');
        $this->manager->registerComponentLocation('/second', 'Second\\Namespace', 'second');

        expect($this->manager->getComponentLocations())->toHaveCount(2);
    });

    it('overwrites component location with same namespace', function (): void {
        $this->manager->registerComponentLocation('/old', 'App\\Components', 'old');
        $this->manager->registerComponentLocation('/new', 'App\\Components', 'new');

        $locations = $this->manager->getComponentLocations();

        expect($locations)->toHaveCount(1);
        expect($locations['App\\Components']['path'])->toBe('/new');
    });
});

describe('Livewire component locations', function (): void {
    it('returns empty array for `getLivewireComponentLocations()` by default', function (): void {
        expect($this->manager->getLivewireComponentLocations())->toBe([]);
    });

    it('can register a Livewire component location', function (): void {
        $this->manager->registerLivewireComponentLocation(
            '/app/Livewire',
            'App\\Livewire',
            'app',
        );

        $locations = $this->manager->getLivewireComponentLocations();

        expect($locations)->toHaveKey('App\\Livewire');
        expect($locations['App\\Livewire'])->toBe([
            'path' => '/app/Livewire',
            'viewNamespace' => 'app',
        ]);
    });

    it('can register a Livewire component location with `null` view namespace', function (): void {
        $this->manager->registerLivewireComponentLocation(
            '/app/Livewire',
            'App\\Livewire',
            null,
        );

        $locations = $this->manager->getLivewireComponentLocations();

        expect($locations['App\\Livewire']['viewNamespace'])->toBeNull();
    });

    it('keeps component and Livewire locations separate', function (): void {
        $this->manager->registerComponentLocation('/components', 'App\\Components', 'comp');
        $this->manager->registerLivewireComponentLocation('/livewire', 'App\\Livewire', 'lw');

        expect($this->manager->getComponentLocations())->toHaveCount(1);
        expect($this->manager->getLivewireComponentLocations())->toHaveCount(1);
    });
});
