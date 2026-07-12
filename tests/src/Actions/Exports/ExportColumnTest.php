<?php

use Filament\Actions\Exports\ExportColumn;
use Filament\Tests\TestCase;

uses(TestCase::class);

// Overrides `getState()` so formula injection sanitization can be tested
// without a backing record (`getState()` returns `null` without one).
class FakeStateExportColumn extends ExportColumn
{
    protected mixed $fakeState = null;

    public function fakeState(mixed $state): static
    {
        $this->fakeState = $state;

        return $this;
    }

    public function getState(): mixed
    {
        return $this->fakeState;
    }
}

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

describe('formula injection protection', function (): void {
    it('does not sanitize formula triggers by default', function (string $value): void {
        $column = FakeStateExportColumn::make('title')
            ->fakeState($value);

        expect($column->shouldPreventFormulaInjection())->toBeFalse();
        expect($column->getFormattedState())->toBe($value);
    })->with([
        '-5',
        '+44 1234 567890',
        '=1+1',
        '@SUM(A1:A2)',
    ]);

    it('prefixes formula triggers with a single quote when `preventFormulaInjection()` is enabled', function (string $value, string $expected): void {
        $column = FakeStateExportColumn::make('title')
            ->preventFormulaInjection()
            ->fakeState($value);

        expect($column->getFormattedState())->toBe($expected);
    })->with([
        ['=1+1', "'=1+1"],
        ['+44 1234 567890', "'+44 1234 567890"],
        ['-5', "'-5"],
        ['@SUM(A1:A2)', "'@SUM(A1:A2)"],
        ["\tTabbed", "'\tTabbed"],
        ["\rReturn", "'\rReturn"],
    ]);

    it('leaves safe values untouched when enabled', function (): void {
        $column = FakeStateExportColumn::make('title')
            ->preventFormulaInjection()
            ->fakeState('Hello world');

        expect($column->getFormattedState())->toBe('Hello world');
    });

    it('passes through empty and `null` states when enabled', function (mixed $value, ?string $expected): void {
        $column = FakeStateExportColumn::make('title')
            ->preventFormulaInjection()
            ->fakeState($value);

        expect($column->getFormattedState())->toBe($expected);
    })->with([
        ['', ''],
        [null, null],
    ]);

    it('joins array state before sanitizing when enabled', function (): void {
        $column = FakeStateExportColumn::make('title')
            ->preventFormulaInjection()
            ->fakeState(['=danger', 'safe']);

        expect($column->getFormattedState())->toBe("'=danger, safe");
    });

    it('can set `preventFormulaInjection()` with a `Closure`', function (): void {
        $column = FakeStateExportColumn::make('title')
            ->preventFormulaInjection(static fn (): bool => true)
            ->fakeState('=1+1');

        expect($column->shouldPreventFormulaInjection())->toBeTrue();
        expect($column->getFormattedState())->toBe("'=1+1");
    });

    it('can opt back out with `preventFormulaInjection(false)`', function (): void {
        $column = FakeStateExportColumn::make('title')
            ->preventFormulaInjection()
            ->preventFormulaInjection(false)
            ->fakeState('=1+1');

        expect($column->shouldPreventFormulaInjection())->toBeFalse();
        expect($column->getFormattedState())->toBe('=1+1');
    });
});

describe('visibility', function (): void {
    test('is visible by default', function (): void {
        $column = ExportColumn::make('title');

        expect($column->isVisible())->toBeTrue();
        expect($column->isHidden())->toBeFalse();
    });

    it('can be hidden with `hidden()`', function (): void {
        $column = ExportColumn::make('title')
            ->hidden();

        expect($column->isHidden())->toBeTrue();
        expect($column->isVisible())->toBeFalse();
    });

    it('can undo `hidden()` by passing `false`', function (): void {
        $column = ExportColumn::make('title')
            ->hidden()
            ->hidden(false);

        expect($column->isHidden())->toBeFalse();
        expect($column->isVisible())->toBeTrue();
    });

    it('can set `hidden()` with a `Closure`', function (): void {
        $column = ExportColumn::make('title')
            ->hidden(static fn (): bool => true);

        expect($column->isHidden())->toBeTrue();
        expect($column->isVisible())->toBeFalse();
    });

    it('can be made not visible with `visible(false)`', function (): void {
        $column = ExportColumn::make('title')
            ->visible(false);

        expect($column->isVisible())->toBeFalse();
        expect($column->isHidden())->toBeTrue();
    });

    it('can undo `visible(false)` by passing `true`', function (): void {
        $column = ExportColumn::make('title')
            ->visible(false)
            ->visible();

        expect($column->isVisible())->toBeTrue();
        expect($column->isHidden())->toBeFalse();
    });

    it('can set `visible()` with a `Closure`', function (): void {
        $column = ExportColumn::make('title')
            ->visible(static fn (): bool => false);

        expect($column->isVisible())->toBeFalse();
        expect($column->isHidden())->toBeTrue();
    });

    test('`hidden()` takes priority over `visible()`', function (): void {
        $column = ExportColumn::make('title')
            ->hidden()
            ->visible();

        expect($column->isHidden())->toBeTrue();
        expect($column->isVisible())->toBeFalse();
    });
});
