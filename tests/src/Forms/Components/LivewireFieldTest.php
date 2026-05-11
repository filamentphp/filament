<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\LivewireField;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be instantiated', function (): void {
    $field = LivewireField::make('custom');

    expect($field->getName())->toBe('custom');
});

it('can set and get a `component()` class', function (): void {
    $field = LivewireField::make('custom')
        ->component('App\\Livewire\\MyComponent');
    expect($field->getComponent())->toBe('App\\Livewire\\MyComponent');
});

it('can set `lazy()` mode', function (): void {
    $field = LivewireField::make('custom');
    expect($field->isLazy())->toBeFalse();
    $field->lazy();
    expect($field->isLazy())->toBeTrue();
});

it('can set and get `data()`', function (): void {
    $field = LivewireField::make('custom')
        ->data(['key' => 'value', 'foo' => 'bar']);
    expect($field->getData())->toBe(['key' => 'value', 'foo' => 'bar']);
});

it('returns empty array for `getData()` by default', function (): void {
    $field = LivewireField::make('custom');
    expect($field->getData())->toBe([]);
});

describe('Closure support', function (): void {
    it('can set `component()` with a `Closure`', function (): void {
        $field = LivewireField::make('custom')
            ->component(static fn (): string => 'App\\Livewire\\DynamicComponent');

        expect($field->getComponent())->toBe('App\\Livewire\\DynamicComponent');
    });

    it('can set `lazy()` with a `Closure`', function (): void {
        $field = LivewireField::make('custom')
            ->lazy(static fn (): bool => true);

        expect($field->isLazy())->toBeTrue();
    });

    it('can set `data()` with a `Closure`', function (): void {
        $field = LivewireField::make('custom')
            ->data(static fn (): array => ['dynamic' => 'value']);

        expect($field->getData())->toBe(['dynamic' => 'value']);
    });
});

describe('`getComponentProperties()` logic', function (): void {
    it('includes `wire:model` with state path', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = LivewireField::make('custom')
                    ->component('App\\Livewire\\MyComponent'),
            ])
            ->fill();

        $properties = $field->getComponentProperties();

        expect($properties)->toHaveKey('wire:model');
        expect($properties['wire:model'])->toBe('data.custom');
    });

    it('includes `lazy` property when `lazy()` is enabled', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = LivewireField::make('custom')
                    ->component('App\\Livewire\\MyComponent')
                    ->lazy(),
            ])
            ->fill();

        $properties = $field->getComponentProperties();

        expect($properties)->toHaveKey('lazy');
        expect($properties['lazy'])->toBeTrue();
    });

    it('does not include `lazy` property when `lazy()` is not set', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = LivewireField::make('custom')
                    ->component('App\\Livewire\\MyComponent'),
            ])
            ->fill();

        $properties = $field->getComponentProperties();

        expect($properties)->not->toHaveKey('lazy');
    });

    it('merges `data()` into component properties', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = LivewireField::make('custom')
                    ->component('App\\Livewire\\MyComponent')
                    ->data(['color' => 'blue', 'size' => 'lg']),
            ])
            ->fill();

        $properties = $field->getComponentProperties();

        expect($properties)->toHaveKey('color', 'blue');
        expect($properties)->toHaveKey('size', 'lg');
    });

    it('includes `record` key', function (): void {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = LivewireField::make('custom')
                    ->component('App\\Livewire\\MyComponent'),
            ])
            ->fill();

        $properties = $field->getComponentProperties();

        expect($properties)->toHaveKey('record');
    });
});
