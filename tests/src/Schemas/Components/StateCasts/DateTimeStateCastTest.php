<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Schemas\Components\StateCasts\DateTimeStateCast;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('shifts wall time in `get()` and converts instants in `set()` for mutable, immutable, and string state', function (string $appTimezone, string $timezone, string $stored, string $internal, string $source): void {
    config(['app.timezone' => $appTimezone]);

    $cast = app(DateTimeStateCast::class, [
        'format' => 'd/m/Y H:i:s',
        'internalFormat' => 'Y-m-d H:i:s',
        'timezone' => $timezone,
    ]);

    $storedState = $source === 'string' ? $stored : $source::createFromFormat('d/m/Y H:i:s', $stored, $appTimezone);
    // The input object's zone must not replace the field's wall-time zone in `get()`.
    $internalState = $source === 'string' ? $internal : $source::parse($internal, 'Pacific/Honolulu');

    expect($cast->set($storedState))->toBe($internal)
        ->and($cast->get($internalState))->toBe($stored);
})->with([
    'before New York spring transition' => ['Europe/London', 'America/New_York', '09/03/2025 06:59:31', '2025-03-09 01:59:31'],
    'after New York spring transition' => ['Europe/London', 'America/New_York', '09/03/2025 07:01:47', '2025-03-09 03:01:47'],
    'before London fall transition' => ['Asia/Tokyo', 'Europe/London', '26/10/2025 08:30:17', '2025-10-26 00:30:17'],
    'after London fall transition' => ['Asia/Tokyo', 'Europe/London', '26/10/2025 11:30:43', '2025-10-26 02:30:43'],
    'positive fractional offset and next date' => ['America/Los_Angeles', 'Asia/Kathmandu', '14/07/2025 15:30:19', '2025-07-15 04:15:19'],
    'negative offset and previous date' => ['Asia/Tokyo', 'America/New_York', '15/07/2025 01:15:53', '2025-07-14 12:15:53'],
])->with(['string', Carbon::class, CarbonImmutable::class]);

it('preserves blank state in both directions and rejects invalid stored dates', function (): void {
    $cast = app(DateTimeStateCast::class, [
        'format' => 'd/m/Y H:i:s',
        'internalFormat' => 'Y-m-d H:i:s',
        'timezone' => 'Asia/Kathmandu',
    ]);

    expect($cast->get(null))->toBeNull()
        ->and($cast->get(''))->toBeNull()
        ->and($cast->set(null))->toBeNull()
        ->and($cast->set(''))->toBeNull()
        ->and($cast->set('not a date'))->toBeNull()
        ->and($cast->set('2025-07-14T22:30:19Z'))->toBe('2025-07-15 04:15:19');
});
