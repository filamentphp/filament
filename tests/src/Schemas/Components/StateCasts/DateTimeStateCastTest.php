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

it('preserves calendar dates without timezone conversion or an implicit clock', function (string $source, string $appTimezone): void {
    config(['app.timezone' => $appTimezone]);

    $cast = app(DateTimeStateCast::class, [
        'format' => 'd/m/Y',
        'internalFormat' => 'Y-m-d H:i:s',
        'timezone' => null,
    ]);

    foreach (['00:15:23', '23:45:47'] as $time) {
        $this->travelTo(Carbon::parse("2025-07-20 {$time}", 'UTC'));
        $stored = $source === 'string' ? '15/07/2025' : $source::parse('2025-07-15 00:00:00', 'Asia/Tokyo');
        $internal = $source === 'string' ? '2025-07-15 00:00:00' : $source::parse('2025-07-15 00:00:00', 'America/Los_Angeles');

        expect($cast->set($stored))->toBe('2025-07-15 00:00:00')
            ->and($cast->get($internal))->toBe('15/07/2025');

        if ($source !== 'string') {
            expect($stored->format('Y-m-d H:i:s e'))->toBe('2025-07-15 00:00:00 Asia/Tokyo')
                ->and($internal->format('Y-m-d H:i:s e'))->toBe('2025-07-15 00:00:00 America/Los_Angeles');
        }
    }
})->with(['string', Carbon::class, CarbonImmutable::class])->with(['Europe/London', 'Pacific/Honolulu']);

it('preserves supplied times in custom formats without timezone conversion', function (string $format, string $stored, string $internal): void {
    $cast = app(DateTimeStateCast::class, [
        'format' => $format,
        'internalFormat' => 'Y-m-d H:i:s',
        'timezone' => null,
    ]);

    expect($cast->set($stored))->toBe($internal)
        ->and($cast->get($internal))->toBe($stored);
})->with([
    ['d/m/Y H:i:s', '15/07/2025 23:47:19', '2025-07-15 23:47:19'],
    ['d/m/Y A h:i', '15/07/2025 PM 01:30', '2025-07-15 13:30:00'],
    ['d/m/Y A h:i', '15/07/2025 AM 12:15', '2025-07-15 00:15:00'],
]);

it('preserves app-calendar defaults for partial formats without timezone conversion', function (string $format, string $stored, string $internal): void {
    config(['app.timezone' => 'Pacific/Honolulu']);
    $this->travelTo(Carbon::parse('2025-01-01 00:30:47', 'UTC'));

    $cast = app(DateTimeStateCast::class, [
        'format' => $format,
        'internalFormat' => 'Y-m-d H:i:s',
        'timezone' => null,
    ]);

    expect($cast->set($stored))->toBe($internal)
        ->and($cast->get($internal))->toBe($stored);
})->with([
    ['m-d', '02-29', '2024-02-29 00:00:00'],
    ['d', '15', '2024-12-15 00:00:00'],
]);

it('parses absolute calendar dates neutrally and relative dates in the app timezone', function (): void {
    config(['app.timezone' => 'Pacific/Honolulu']);
    $this->travelTo(Carbon::parse('2025-07-15 00:30:00', 'UTC'));

    $cast = app(DateTimeStateCast::class, [
        'format' => 'Y-m-d',
        'internalFormat' => 'Y-m-d H:i:s',
        'timezone' => null,
    ]);

    expect($cast->set('today'))->toBe('2025-07-14 00:00:00')
        ->and($cast->set('tomorrow'))->toBe('2025-07-15 00:00:00')
        ->and($cast->set('2025-07-15T00:15:23+09:00'))->toBe('2025-07-15 00:15:23')
        ->and($cast->set('2025-09-07 12:00:00 America/Santiago'))->toBe('2025-09-07 12:00:00')
        ->and($cast->set(null))->toBeNull()
        ->and($cast->set(''))->toBeNull()
        ->and($cast->set('not a date'))->toBeNull()
        ->and($cast->get(null))->toBeNull()
        ->and($cast->get(''))->toBeNull();

    config(['app.timezone' => 'Pacific/Apia']);

    expect($cast->set('2011-12-30'))->toBe('2011-12-30 00:00:00')
        ->and($cast->get('2011-12-30 00:00:00'))->toBe('2011-12-30');
});

it('preserves valid field wall times across gaps in the app timezone in repeated `get()` and `set()` calls', function (string $appTimezone, string $timezone, string $internal, string $stored, string $source): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => $appTimezone]);
    date_default_timezone_set($appTimezone);

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'd/m/Y H:i:s',
            'internalFormat' => 'Y-m-d H:i:s',
            'timezone' => $timezone,
        ]);

        $currentInternal = $internal;

        for ($cycle = 0; $cycle < 3; $cycle++) {
            $state = $source === 'string' ? $currentInternal : $source::parse($currentInternal, 'UTC');
            $currentStored = $cast->get($state);
            $currentInternal = $cast->set($currentStored);

            expect($currentStored)->toBe($stored)
                ->and($currentInternal)->toBe($internal);
        }
    } finally {
        date_default_timezone_set($originalTimezone);
    }
})->with([
    'London missing hour, valid New York time' => ['Europe/London', 'America/New_York', '2025-03-30 01:30:17', '30/03/2025 06:30:17'],
    'New York missing hour, valid Tokyo time' => ['America/New_York', 'Asia/Tokyo', '2025-03-09 02:15:47', '08/03/2025 12:15:47'],
    'Lord Howe missing half hour, valid UTC time' => ['Australia/Lord_Howe', 'UTC', '2025-10-05 02:15:23', '05/10/2025 13:15:23'],
    'Apia missing date, valid UTC date' => ['Pacific/Apia', 'UTC', '2011-12-30 12:15:53', '31/12/2011 02:15:53'],
    'before London missing hour' => ['Europe/London', 'America/New_York', '2025-03-30 00:59:31', '30/03/2025 05:59:31'],
    'after London missing hour' => ['Europe/London', 'America/New_York', '2025-03-30 02:01:43', '30/03/2025 07:01:43'],
])->with(['string', Carbon::class, CarbonImmutable::class]);

it('keeps stored strings in the app timezone when loading and repeatedly saving across field DST transitions', function (string $stored, string $internal, string $source): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => 'UTC']);
    date_default_timezone_set('UTC');

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'Y-m-d H:i:s',
            'internalFormat' => 'Y-m-d H:i:s',
            'timezone' => 'America/New_York',
        ]);

        $currentStored = $stored;

        for ($cycle = 0; $cycle < 3; $cycle++) {
            $state = $source === 'string' ? $currentStored : $source::parse($currentStored, 'UTC');
            $currentInternal = $cast->set($state);
            $currentStored = $cast->get($currentInternal);

            expect($currentInternal)->toBe($internal)
                ->and($currentStored)->toBe($stored);
        }
    } finally {
        date_default_timezone_set($originalTimezone);
    }
})->with([
    'before spring jump' => ['2025-03-09 06:59:31', '2025-03-09 01:59:31'],
    'after spring jump' => ['2025-03-09 07:01:47', '2025-03-09 03:01:47'],
    'before fall repeated hour' => ['2025-11-02 04:30:17', '2025-11-02 00:30:17'],
    'after fall repeated hour' => ['2025-11-02 07:30:43', '2025-11-02 02:30:43'],
])->with(['string', Carbon::class, CarbonImmutable::class]);

it('keeps the app calendar date for time-only state when field and app dates differ', function (string $appTimezone, string $timezone, string $now, string $internalFormat, string $internal, string $stored): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => $appTimezone]);
    date_default_timezone_set($appTimezone);
    $this->travelTo(Carbon::parse($now, 'UTC'));

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'Y-m-d H:i:s',
            'internalFormat' => $internalFormat,
            'timezone' => $timezone,
        ]);

        expect($cast->get($internal))->toBe($stored)
            ->and($cast->set($stored))->toBe($internal);
    } finally {
        date_default_timezone_set($originalTimezone);
    }
})->with([
    'field is tomorrow before app spring jump' => ['America/New_York', 'Asia/Tokyo', '2025-03-09 04:30:00', 'H:i:s', '20:15:47', '2025-03-08 06:15:47'],
    'field is tomorrow without seconds' => ['America/New_York', 'Asia/Tokyo', '2025-03-09 04:30:00', 'H:i', '20:15', '2025-03-08 06:15:00'],
    'field is yesterday before field spring jump' => ['Asia/Tokyo', 'America/New_York', '2025-03-08 16:30:00', 'H:i:s', '08:15:53', '2025-03-09 21:15:53'],
    'time-only input in app spring gap' => ['Europe/London', 'America/New_York', '2025-03-30 12:00:00', 'H:i:s', '01:30:17', '2025-03-30 06:30:17'],
]);

it('preserves explicit-offset wall-time handling and ambiguous field-time handling in `get()`', function (string $appTimezone, string $timezone, string $internal, string $stored): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => $appTimezone]);
    date_default_timezone_set($appTimezone);

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'Y-m-d H:i:s',
            'internalFormat' => 'Y-m-d H:i:s',
            'timezone' => $timezone,
        ]);

        expect($cast->get($internal))->toBe($stored);
    } finally {
        date_default_timezone_set($originalTimezone);
    }
})->with([
    'explicit positive offset' => ['Europe/London', 'America/New_York', '2025-07-15T09:30:17+09:00', '2025-07-15 14:30:17'],
    'explicit negative offset' => ['Europe/London', 'Asia/Kathmandu', '2025-07-15T09:30:47-04:00', '2025-07-15 04:45:47'],
    'field spring gap keeps PHP normalization' => ['UTC', 'America/New_York', '2025-03-09 02:15:23', '2025-03-09 07:15:23'],
    'field fall overlap keeps PHP choice' => ['UTC', 'America/New_York', '2025-11-02 01:30:43', '2025-11-02 05:30:43'],
]);

it('preserves the London repeated-hour choice for strings and `Carbon` state when the app is in Tokyo', function (string $source): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => 'Asia/Tokyo']);
    date_default_timezone_set('Asia/Tokyo');

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'Y-m-d H:i:s',
            'internalFormat' => 'Y-m-d H:i:s',
            'timezone' => 'Europe/London',
        ]);
        $internal = $cast->set('2025-10-26 09:30:43');

        expect($internal)->toBe('2025-10-26 01:30:43')
            ->and($cast->get($source === 'string' ? $internal : $source::parse($internal, 'Asia/Tokyo')))->toBe('2025-10-26 09:30:43');
    } finally {
        date_default_timezone_set($originalTimezone);
    }
})->with(['string', Carbon::class, CarbonImmutable::class]);

it('preserves the app calendar date for fractional time-only strings', function (): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => 'America/New_York']);
    date_default_timezone_set('America/New_York');
    $this->travelTo(Carbon::parse('2025-03-09 04:30:00', 'UTC'));

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'Y-m-d H:i:s',
            'internalFormat' => 'H:i:s',
            'timezone' => 'Asia/Tokyo',
        ]);

        expect($cast->get('20:15:47.123'))->toBe('2025-03-08 06:15:47')
            ->and($cast->set('2025-03-08 06:15:47'))->toBe('20:15:47');
    } finally {
        date_default_timezone_set($originalTimezone);
    }
});

it('preserves fractional seconds when recovering a native string from an app DST gap', function (string $internal): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => 'Europe/London']);
    date_default_timezone_set('Europe/London');
    $this->travelTo(Carbon::parse('2025-03-30 12:00:00', 'UTC'));

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'Y-m-d H:i:s.u',
            'internalFormat' => 'Y-m-d H:i:s',
            'timezone' => 'America/New_York',
        ]);

        expect($cast->get($internal))->toBe('2025-03-30 06:30:17.123000');
    } finally {
        date_default_timezone_set($originalTimezone);
    }
})->with(['2025-03-30T01:30:17.123', '01:30:17.123']);

it('keeps non-canonical string parsing compatible in `get()`', function (string $internal, string $stored): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => 'Europe/London']);
    date_default_timezone_set('Europe/London');
    $this->travelTo(Carbon::parse('2025-03-29 12:34:56', 'UTC'));

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'Y-m-d H:i:s.u',
            'internalFormat' => 'Y-m-d H:i:s',
            'timezone' => 'America/New_York',
        ]);

        expect($cast->get($internal))->toBe($stored);
    } finally {
        date_default_timezone_set($originalTimezone);
    }
})->with([
    'relative date' => ['tomorrow', '2025-03-30 05:00:00.000000'],
    'explicit positive offset in app gap' => ['2025-03-30T01:30:17.123456+09:00', '2025-03-30 06:30:17.123456'],
    'explicit negative offset in app gap' => ['2025-03-30T01:30:17-04:00', '2025-03-30 06:30:17.000000'],
    'explicit named zone in app gap' => ['2025-03-30 01:30:17 America/New_York', '2025-03-30 06:30:17.000000'],
    'overflow date still follows PHP normalization' => ['2025-02-30 01:30:17', '2025-03-02 06:30:17.000000'],
    'midnight overflow still follows PHP normalization' => ['2025-03-29 24:00:00', '2025-03-30 05:00:00.000000'],
]);

it('preserves ordinary PHP date objects in `get()` without mutating them', function (string $source): void {
    $originalTimezone = date_default_timezone_get();
    config(['app.timezone' => 'Europe/London']);
    date_default_timezone_set('Europe/London');

    try {
        $cast = app(DateTimeStateCast::class, [
            'format' => 'Y-m-d H:i:s.u',
            'internalFormat' => 'Y-m-d H:i:s.u',
            'timezone' => 'America/New_York',
        ]);
        $internal = new $source('2025-03-30 01:30:17.123456', new DateTimeZone('UTC'));

        expect($cast->get($internal))->toBe('2025-03-30 06:30:17.123456')
            ->and($internal->format('Y-m-d H:i:s.u e'))->toBe('2025-03-30 01:30:17.123456 UTC');
    } finally {
        date_default_timezone_set($originalTimezone);
    }
})->with([DateTime::class, DateTimeImmutable::class]);
