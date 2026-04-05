<?php

use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with a name', function (): void {
    $hook = RenderHook::make('panels::body.start');

    expect($hook->getName())->toBe('panels::body.start');
});

it('can set `name()` with a `Closure`', function (): void {
    $hook = RenderHook::make('initial')
        ->name(static fn (): string => 'panels::head.end');

    expect($hook->getName())->toBe('panels::head.end');
});

describe('scopes', function (): void {
    it('defaults `getScopes()` to an empty array', function (): void {
        $hook = RenderHook::make('panels::body.start')
            ->container(Schema::make(Livewire::make()));

        expect($hook->getScopes())->toBe([]);
    });

    it('can set `scopes()` with a string', function (): void {
        $hook = RenderHook::make('panels::body.start', 'admin')
            ->container(Schema::make(Livewire::make()));

        expect($hook->getScopes())->toBe(['admin']);
    });

    it('can set `scopes()` with an array', function (): void {
        $hook = RenderHook::make('panels::body.start', ['admin', 'app'])
            ->container(Schema::make(Livewire::make()));

        expect($hook->getScopes())->toBe(['admin', 'app']);
    });

    it('can set `scopes()` with a `Closure`', function (): void {
        $hook = RenderHook::make('panels::body.start')
            ->scopes(static fn (): array => ['custom'])
            ->container(Schema::make(Livewire::make()));

        expect($hook->getScopes())->toBe(['custom']);
    });

    it('can clear `scopes()` with `null`', function (): void {
        $hook = RenderHook::make('panels::body.start', 'admin')
            ->scopes(null)
            ->container(Schema::make(Livewire::make()));

        expect($hook->getScopes())->toBe([]);
    });
});
