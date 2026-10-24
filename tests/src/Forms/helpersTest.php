<?php

use Filament\Tests\TestCase;

use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;

uses(TestCase::class);

beforeEach(function (): void {
    require_once __DIR__ . '/../../../packages/forms/src/helpers.php';
});

describe('`array_move_after()`', function (): void {
    it('swaps adjacent items without losing keys or values', function (): void {
        expect(array_move_after(['01' => 'padded', 1 => null, 'tail' => 'last'], '1'))
            ->toBe(['01' => 'padded', 'tail' => 'last', 1 => null])
            ->and(array_move_after([0 => null, 4 => ['value' => 7]], '0'))
            ->toBe([4 => ['value' => 7], 0 => null]);
    });

    it('leaves missing items and boundary items unchanged', function (array $items, string $key): void {
        expect(array_move_after($items, $key))->toBe($items);
    })->with([
        'empty' => [[], 'missing'],
        'single item' => [['only' => null], 'only'],
        'last item' => [['first' => 1, 'last' => 2], 'last'],
        'missing key' => [[1 => 'integer'], 'missing'],
        'decimal key' => [[1 => 'integer'], '1.0'],
        'exponent key' => [[1 => 'integer'], '1e0'],
        'blank neighbor' => [['first' => 1, '' => 2], 'first'],
    ]);
});

describe('`array_move_before()`', function (): void {
    it('swaps adjacent items without losing keys or values', function (): void {
        expect(array_move_before(['01' => 'padded', 1 => null, 'tail' => 'last'], '1'))
            ->toBe([1 => null, '01' => 'padded', 'tail' => 'last'])
            ->and(array_move_before([0 => null, 4 => ['value' => 7]], '4'))
            ->toBe([4 => ['value' => 7], 0 => null]);
    });

    it('leaves missing items and boundary items unchanged', function (array $items, string $key): void {
        expect(array_move_before($items, $key))->toBe($items);
    })->with([
        'empty' => [[], 'missing'],
        'single item' => [['only' => null], 'only'],
        'first item' => [['first' => 1, 'last' => 2], 'first'],
        'missing key' => [[1 => 'integer'], 'missing'],
        'decimal key' => [[1 => 'integer'], '1.0'],
        'exponent key' => [[1 => 'integer'], '1e0'],
        'blank neighbor' => [['' => 1, 'last' => 2], 'last'],
    ]);
});
