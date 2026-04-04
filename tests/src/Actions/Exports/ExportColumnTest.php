<?php

use Filament\Actions\Exports\ExportColumn;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('construction', function (): void {
    it('can be constructed with a name', function (): void {
        $column = ExportColumn::make('title');

        expect($column->getName())->toBe('title');
    });

    it('throws `InvalidArgumentException` when name is blank', function (): void {
        ExportColumn::make('');
    })->throws(InvalidArgumentException::class);
});

describe('`getLabel()` logic', function (): void {
    it('auto-generates label from simple name', function (): void {
        $column = ExportColumn::make('first_name');

        expect($column->getLabel())->toBe('First name');
    });

    it('auto-generates label from dotted name using segment before last dot', function (): void {
        $column = ExportColumn::make('author.name');

        expect($column->getLabel())->toBe('Author');
    });

    it('uses custom label over auto-generated', function (): void {
        $column = ExportColumn::make('title')
            ->label('Post Title');

        expect($column->getLabel())->toBe('Post Title');
    });

    it('can set label with a `Closure`', function (): void {
        $column = ExportColumn::make('title')
            ->label(static fn (): string => 'Dynamic');

        expect($column->getLabel())->toBe('Dynamic');
    });
});

describe('enabled by default', function (): void {
    it('defaults `isEnabledByDefault()` to `true`', function (): void {
        $column = ExportColumn::make('title');

        expect($column->isEnabledByDefault())->toBeTrue();
    });

    it('can set `enabledByDefault()` to `false`', function (): void {
        $column = ExportColumn::make('title')
            ->enabledByDefault(false);

        expect($column->isEnabledByDefault())->toBeFalse();
    });

    it('can set `enabledByDefault()` with a `Closure`', function (): void {
        $column = ExportColumn::make('title')
            ->enabledByDefault(static fn (): bool => false);

        expect($column->isEnabledByDefault())->toBeFalse();
    });
});

it('returns `null` from `getDefaultName()`', function (): void {
    expect(ExportColumn::getDefaultName())->toBeNull();
});

it('returns `null` from `getExporter()` by default', function (): void {
    $column = ExportColumn::make('title');

    expect($column->getExporter())->toBeNull();
});

it('returns `null` from `getRecord()` when no exporter set', function (): void {
    $column = ExportColumn::make('title');

    expect($column->getRecord())->toBeNull();
});
