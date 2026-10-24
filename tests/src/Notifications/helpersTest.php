<?php

use Filament\Notifications\Notification;
use Filament\Tests\TestCase;
use PHPUnit\Framework\AssertionFailedError;

use function Filament\Notifications\Testing\assertNotified;

uses(TestCase::class);

beforeEach(function (): void {
    require_once __DIR__ . '/../../../packages/notifications/src/Testing/helpers.php';
});

describe('`assertNotified()`', function (): void {
    it('accepts any notification, a title or an instance and returns the current test', function (Notification | string | null $expected): void {
        Notification::make()->title('First')->send();
        Notification::make()->title('Second')->body('Details')->send();

        expect(assertNotified($expected)->name())->toBe($this->name());
    })->with([
        'any notification' => [null],
        'title' => ['Second'],
        'instance' => [fn (): Notification => Notification::make()->title('Second')->body('Details')],
    ]);

    it('fails when no notification was sent', function (): void {
        expect(fn () => assertNotified())->toThrow(AssertionFailedError::class);
    });

    it('fails when sent notifications do not match', function (Notification | string $expected): void {
        Notification::make()->title('Second')->body('Details')->send();

        expect(fn () => assertNotified($expected))->toThrow(AssertionFailedError::class);
    })->with([
        'different title' => ['Missing'],
        'different body' => [fn (): Notification => Notification::make()->title('Second')->body('Different')],
    ]);
});
