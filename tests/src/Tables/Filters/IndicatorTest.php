<?php

use Filament\Tables\Filters\Indicator;
use Filament\Tests\Tables\TestCase;

uses(TestCase::class);

describe('label', function (): void {
    it('can be constructed with a string label', function (): void {
        $indicator = Indicator::make('Active');

        expect($indicator->getLabel())->toBe('Active');
    });

    it('can be constructed with a `Closure` label', function (): void {
        $indicator = Indicator::make(static fn (): string => 'Dynamic');

        expect($indicator->getLabel())->toBe('Dynamic');
    });

    it('can change label after construction', function (): void {
        $indicator = Indicator::make('Old')
            ->label('New');

        expect($indicator->getLabel())->toBe('New');
    });
});

describe('removable', function (): void {
    it('defaults `isRemovable()` to `true`', function (): void {
        $indicator = Indicator::make('Test');

        expect($indicator->isRemovable())->toBeTrue();
    });

    it('can set `removable()` to `false`', function (): void {
        $indicator = Indicator::make('Test')
            ->removable(false);

        expect($indicator->isRemovable())->toBeFalse();
    });

    it('can set `removable()` with a `Closure`', function (): void {
        $indicator = Indicator::make('Test')
            ->removable(static fn (): bool => false);

        expect($indicator->isRemovable())->toBeFalse();
    });
});

describe('remove field', function (): void {
    it('returns `null` for `getRemoveField()` by default', function (): void {
        $indicator = Indicator::make('Test');

        expect($indicator->getRemoveField())->toBeNull();
    });

    it('can set `removeField()`', function (): void {
        $indicator = Indicator::make('Test')
            ->removeField('isActive');

        expect($indicator->getRemoveField())->toBe('isActive');
    });

    it('can set `removeField()` with a `Closure`', function (): void {
        $indicator = Indicator::make('Test')
            ->removeField(static fn (): string => 'value');

        expect($indicator->getRemoveField())->toBe('value');
    });
});

describe('remove Livewire click handler', function (): void {
    it('returns `null` for `getRemoveLivewireClickHandler()` by default', function (): void {
        $indicator = Indicator::make('Test');

        expect($indicator->getRemoveLivewireClickHandler())->toBeNull();
    });

    it('can set `removeLivewireClickHandler()`', function (): void {
        $indicator = Indicator::make('Test')
            ->removeLivewireClickHandler('removeFilter()');

        expect($indicator->getRemoveLivewireClickHandler())->toBe('removeFilter()');
    });

    it('can set `removeLivewireClickHandler()` with a `Closure`', function (): void {
        $indicator = Indicator::make('Test')
            ->removeLivewireClickHandler(static fn (): string => 'dynamic()');

        expect($indicator->getRemoveLivewireClickHandler())->toBe('dynamic()');
    });
});
