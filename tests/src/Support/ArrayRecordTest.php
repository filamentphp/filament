<?php

use Filament\Support\ArrayRecord;
use Filament\Tests\TestCase;

uses(TestCase::class);

afterEach(function (): void {
    // Reset static state between tests
    ArrayRecord::keyName('__key');
});

it('defaults `getKeyName()` to `__key`', function (): void {
    expect(ArrayRecord::getKeyName())->toBe('__key');
});

it('can set `keyName()`', function (): void {
    ArrayRecord::keyName('id');

    expect(ArrayRecord::getKeyName())->toBe('id');
});

it('can change `keyName()` multiple times', function (): void {
    ArrayRecord::keyName('first');
    expect(ArrayRecord::getKeyName())->toBe('first');

    ArrayRecord::keyName('second');
    expect(ArrayRecord::getKeyName())->toBe('second');
});
