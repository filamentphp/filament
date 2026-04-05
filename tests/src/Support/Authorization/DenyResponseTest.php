<?php

use Filament\Support\Authorization\DenyResponse;
use Filament\Tests\TestCase;
use Illuminate\Auth\Access\Response;

uses(TestCase::class);

it('can be created with `make()` using a string message', function (): void {
    $response = DenyResponse::make('delete', 'Cannot delete this record.');

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->message())->toBe('Cannot delete this record.');
});

it('can be created with `make()` using a `Closure` message', function (): void {
    $response = DenyResponse::make('delete', static fn (): string => 'Dynamic message');

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->message())->toBe('Dynamic message');
});

it('is denied by default', function (): void {
    $response = DenyResponse::make('delete', 'Denied.');

    expect($response->allowed())->toBeFalse();
});

describe('key', function (): void {
    it('returns the key set by `make()`', function (): void {
        $response = DenyResponse::make('delete', 'Denied.');

        if ($response instanceof DenyResponse) {
            expect($response->getKey())->toBe('delete');
        }
    });

    it('can set `key()` directly', function (): void {
        $response = DenyResponse::deny();
        $response->key('custom-key');

        expect($response->getKey())->toBe('custom-key');
    });

    it('returns `null` for `getKey()` by default', function (): void {
        $response = DenyResponse::deny();

        expect($response->getKey())->toBeNull();
    });

    it('can clear `key()` with `null`', function (): void {
        $response = DenyResponse::deny();
        $response->key('some-key');
        $response->key(null);

        expect($response->getKey())->toBeNull();
    });
});

describe('message with `Closure`', function (): void {
    it('passes `$count` and `$total` to the `Closure`', function (): void {
        $response = DenyResponse::make('bulk-delete', static fn (int $count, int $total): string => "Cannot delete {$count} of {$total} records.");

        expect($response->message(3, 10))->toBe('Cannot delete 3 of 10 records.');
    });

    it('passes `$failureCount` to the `Closure`', function (): void {
        $response = DenyResponse::make('bulk', static fn (int $failureCount): string => "{$failureCount} failed");

        expect($response->message(5, 10))->toBe('5 failed');
    });

    it('passes `$isAll` boolean to the `Closure`', function (): void {
        $response = DenyResponse::make('bulk', static fn (bool $isAll): string => $isAll ? 'All failed' : 'Some failed');

        expect($response->message(10, 10))->toBe('All failed');
        expect($response->message(5, 10))->toBe('Some failed');
    });

    it('passes `$totalCount` to the `Closure`', function (): void {
        $response = DenyResponse::make('bulk', static fn (int $totalCount): string => "Out of {$totalCount}");

        expect($response->message(3, 20))->toBe('Out of 20');
    });

    it('defaults `$count` and `$total` to `1`', function (): void {
        $response = DenyResponse::make('action', static fn (int $count, int $total): string => "{$count}/{$total}");

        expect($response->message())->toBe('1/1');
    });

    it('can return `null` from the `Closure` to fall back to parent message', function (): void {
        $response = DenyResponse::make('action', 'Fallback message');

        if ($response instanceof DenyResponse) {
            $response->getMessageUsing(static fn (): ?string => null);
        }

        // When Closure returns null, falls back to parent message
        expect($response->message())->toBe('Fallback message');
    });
});

describe('`getMessageUsing()`', function (): void {
    it('can set `getMessageUsing()` callback', function (): void {
        $response = DenyResponse::deny();
        $result = $response->getMessageUsing(static fn (): string => 'callback');

        expect($result)->toBe($response);
        expect($response->message())->toBe('callback');
    });

    it('can clear `getMessageUsing()` with `null`', function (): void {
        $response = DenyResponse::deny();
        $response->getMessageUsing(static fn (): string => 'callback');
        $response->getMessageUsing(null);

        // With no callback, falls back to parent::message()
        expect($response->message())->toBeNull();
    });
});
