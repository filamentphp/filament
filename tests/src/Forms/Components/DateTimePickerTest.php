<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('configuration', function (): void {
    it('returns the full date-time `getInternalFormat()` by default', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getInternalFormat())->toBe('Y-m-d H:i:s');
    });

    it('returns the date-only `getInternalFormat()` with `time(false)`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->time(false);

        expect($picker->getInternalFormat())->toBe('Y-m-d');
    });

    it('returns the time-only `getInternalFormat()` with `date(false)` and `seconds(false)`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->date(false)
            ->seconds(false);

        expect($picker->getInternalFormat())->toBe('H:i');
    });

    it('returns the time-only `getInternalFormat()` with `date(false)`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->date(false); // seconds enabled by default

        expect($picker->getInternalFormat())->toBe('H:i:s');
    });

    it('returns the date-time `getInternalFormat()` with `seconds(false)`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->seconds(false);

        expect($picker->getInternalFormat())->toBe('Y-m-d H:i');
    });

    it('returns the full date-time `getInternalFormat()` with `native(false)` regardless of other flags', function (): void {
        $picker = DateTimePicker::make('dt')
            ->time(false)
            ->date(false)
            ->seconds(false)
            ->native(false);

        expect($picker->getInternalFormat())->toBe('Y-m-d H:i:s');
    });

    it('can set `maxDate()`', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getMaxDate())->toBeNull();

        $picker->maxDate('2025-12-31');

        expect($picker->getMaxDate())->toBe('2025-12-31');
    });

    it('can set `minDate()`', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getMinDate())->toBeNull();

        $picker->minDate('2020-01-01');

        expect($picker->getMinDate())->toBe('2020-01-01');
    });

    it('returns effective bounds from `getMinDate()` and `getMaxDate()` without timezone conversion or mutating `Carbon` values', function (string $pickerClass, bool $hasTime, string $expectedMinimumTime, string $expectedMaximumTime, string $source, bool $useClosure): void {
        config(['app.timezone' => 'Europe/London']);

        $minimum = $source === 'string' ? '2025-07-15 09:30:23' : $source::parse('2025-07-15 09:30:23', 'Asia/Tokyo');
        $maximum = $source === 'string' ? '2025-07-17 17:45:47' : $source::parse('2025-07-17 17:45:47', 'America/New_York');
        $picker = $pickerClass::make('appointment')
            ->time($hasTime)
            ->timezone('America/Los_Angeles')
            ->format('d/m/Y H:i')
            ->minDate($useClosure ? static fn () => $minimum : $minimum)
            ->maxDate($useClosure ? static fn () => $maximum : $maximum);

        expect($picker->getMinDate())->toBe("2025-07-15 {$expectedMinimumTime}")
            ->and($picker->getMaxDate())->toBe("2025-07-17 {$expectedMaximumTime}");

        if ($minimum instanceof CarbonInterface) {
            expect($minimum->format('Y-m-d H:i:s e'))->toBe('2025-07-15 09:30:23 Asia/Tokyo')
                ->and($maximum->format('Y-m-d H:i:s e'))->toBe('2025-07-17 17:45:47 America/New_York');
        }

        $picker->minDate($useClosure ? static fn () => null : null)
            ->maxDate($useClosure ? static fn () => null : null);

        expect($picker->getMinDate())->toBeNull()
            ->and($picker->getMaxDate())->toBeNull();
    })->with([
        'date picker' => [DatePicker::class, false, '00:00:00', '23:59:59'],
        'date-time picker without time' => [DateTimePicker::class, false, '00:00:00', '23:59:59'],
        'date-time picker' => [DateTimePicker::class, true, '09:30:23', '17:45:47'],
        'time picker' => [TimePicker::class, true, '09:30:23', '17:45:47'],
    ])->with(['string', Carbon::class, CarbonImmutable::class])
        ->with([false, true]);

    it('can set `firstDayOfWeek()`', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getFirstDayOfWeek())->toBe(1);

        $picker->firstDayOfWeek(0);

        expect($picker->getFirstDayOfWeek())->toBe(0);
    });

    it('can set `weekStartsOnMonday()`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->weekStartsOnMonday();

        expect($picker->getFirstDayOfWeek())->toBe(1);
    });

    it('can set `weekStartsOnSunday()`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->weekStartsOnSunday();

        expect($picker->getFirstDayOfWeek())->toBe(7);
    });

    it('can set `hoursStep()`', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getHoursStep())->toBe(1);

        $picker->hoursStep(2);

        expect($picker->getHoursStep())->toBe(2);
    });

    it('can set `minutesStep()`', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getMinutesStep())->toBe(1);

        $picker->minutesStep(15);

        expect($picker->getMinutesStep())->toBe(15);
    });

    it('can set `secondsStep()`', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getSecondsStep())->toBe(1);

        $picker->secondsStep(30);

        expect($picker->getSecondsStep())->toBe(30);
    });

    it('can set `closeOnDateSelection()`', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->shouldCloseOnDateSelection())->toBeFalse();

        $picker->closeOnDateSelection();

        expect($picker->shouldCloseOnDateSelection())->toBeTrue();
    });

    it('can set `timezone()`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->timezone('America/New_York');

        expect($picker->getTimezone())->toBe('America/New_York');
    });

    it('can set `locale()`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->locale('fr');

        expect($picker->getLocale())->toBe('fr');
    });

    it('can set `disabledDates()`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->disabledDates(['2025-01-01', '2025-12-25']);

        expect($picker->getDisabledDates())->toBe(['2025-01-01', '2025-12-25']);
    });

    it('returns `datetime-local` for `getType()` by default', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getType())->toBe('datetime-local');
    });

    it('returns `date` for `getType()` when time is disabled', function (): void {
        $picker = DateTimePicker::make('dt')
            ->time(false);

        expect($picker->getType())->toBe('date');
    });

    it('returns `time` for `getType()` when date is disabled', function (): void {
        $picker = DateTimePicker::make('dt')
            ->date(false);

        expect($picker->getType())->toBe('time');
    });

    it('can set `format()`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->format('d/m/Y');

        expect($picker->getFormat())->toBe('d/m/Y');
    });

    it('can set `displayFormat()`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->displayFormat('F j, Y g:i A');

        expect($picker->getDisplayFormat())->toBe('F j, Y g:i A');
    });

    describe('`getFormat()` computed defaults', function (): void {
        it('returns `Y-m-d H:i:s` for `getFormat()` with date, time, and seconds', function (): void {
            $picker = DateTimePicker::make('dt');

            expect($picker->getFormat())->toBe('Y-m-d H:i:s');
        });

        it('returns `Y-m-d` for `getFormat()` with date only', function (): void {
            $picker = DateTimePicker::make('dt')
                ->time(false);

            expect($picker->getFormat())->toBe('Y-m-d');
        });

        it('returns `H:i:s` for `getFormat()` with time and seconds only', function (): void {
            $picker = DateTimePicker::make('dt')
                ->date(false);

            expect($picker->getFormat())->toBe('H:i:s');
        });

        it('returns `H:i` for `getFormat()` with time only (no seconds)', function (): void {
            $picker = DateTimePicker::make('dt')
                ->date(false)
                ->seconds(false);

            expect($picker->getFormat())->toBe('H:i');
        });

        it('returns `Y-m-d H:i` for `getFormat()` with date and time (no seconds)', function (): void {
            $picker = DateTimePicker::make('dt')
                ->seconds(false);

            expect($picker->getFormat())->toBe('Y-m-d H:i');
        });
    });

    describe('`getStep()` computed logic', function (): void {
        it('returns `1` for `getStep()` by default (has seconds)', function (): void {
            $picker = DateTimePicker::make('dt');

            expect($picker->getStep())->toBe(1);
        });

        it('returns `null` for `getStep()` when time is disabled', function (): void {
            $picker = DateTimePicker::make('dt')
                ->time(false);

            expect($picker->getStep())->toBeNull();
        });

        it('returns `null` for `getStep()` when seconds disabled and no custom steps', function (): void {
            $picker = DateTimePicker::make('dt')
                ->seconds(false);

            expect($picker->getStep())->toBeNull();
        });

        it('returns seconds step value for `getStep()` when `secondsStep()` > 1', function (): void {
            $picker = DateTimePicker::make('dt')
                ->secondsStep(15);

            expect($picker->getStep())->toBe(15);
        });

        it('returns minutes * 60 for `getStep()` when `minutesStep()` > 1', function (): void {
            $picker = DateTimePicker::make('dt')
                ->minutesStep(5);

            expect($picker->getStep())->toBe(300);
        });

        it('returns hours * 3600 for `getStep()` when `hoursStep()` > 1', function (): void {
            $picker = DateTimePicker::make('dt')
                ->hoursStep(2);

            expect($picker->getStep())->toBe(7200);
        });
    });

    it('clamps out-of-range `firstDayOfWeek()` to `null` (falls back to default 1)', function (): void {
        $picker = DateTimePicker::make('dt')
            ->firstDayOfWeek(10);

        expect($picker->getFirstDayOfWeek())->toBe(1);

        $negativePicker = DateTimePicker::make('dt')
            ->firstDayOfWeek(-1);

        expect($negativePicker->getFirstDayOfWeek())->toBe(1);
    });

    it('can set `defaultFocusedDate()`', function (): void {
        $picker = DateTimePicker::make('dt');

        expect($picker->getDefaultFocusedDate())->toBeNull();

        $picker->defaultFocusedDate('2025-06-15');

        expect($picker->getDefaultFocusedDate())->not->toBeNull();
    });

    it('does not mutate a shared `CarbonInterface` from `defaultFocusedDate()`', function (string $dateClass): void {
        $date = $dateClass::parse('2025-07-15 23:45:19.123456', 'Asia/Tokyo');
        $picker = DateTimePicker::make('appointment')
            ->timezone('America/New_York')
            ->defaultFocusedDate(static fn (): CarbonInterface => $date);
        $otherPicker = DateTimePicker::make('other_appointment')
            ->timezone('UTC')
            ->defaultFocusedDate($date);

        for ($cycle = 0; $cycle < 3; $cycle++) {
            expect($picker->getDefaultFocusedDate())->toBe('2025-07-15 10:45:19')
                ->and($otherPicker->getDefaultFocusedDate())->toBe('2025-07-15 14:45:19')
                ->and($date->format('Y-m-d H:i:s.u e'))->toBe('2025-07-15 23:45:19.123456 Asia/Tokyo');
        }
    })->with(['mutable' => [Carbon::class], 'immutable' => [CarbonImmutable::class]]);

    it('can set `maxDate()` with a `Closure`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->maxDate(static fn (): string => '2030-01-01');

        expect($picker->getMaxDate())->toBe('2030-01-01');
    });

    it('can set `minDate()` with a `Closure`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->minDate(static fn (): string => '2000-01-01');

        expect($picker->getMinDate())->toBe('2000-01-01');
    });

    it('can set `hoursStep()` with a `Closure`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->hoursStep(static fn (): int => 3);

        expect($picker->getHoursStep())->toBe(3);
    });

    it('can set `disabledDates()` with a `Closure`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->disabledDates(static fn (): array => ['2025-12-25']);

        expect($picker->getDisabledDates())->toBe(['2025-12-25']);
    });

    it('returns fluent `$this` from `resetFirstDayOfWeek()`', function (): void {
        $picker = DateTimePicker::make('dt')
            ->firstDayOfWeek(7);

        $result = $picker->resetFirstDayOfWeek();

        expect($result)->toBe($picker);
        expect($picker->getFirstDayOfWeek())->toBe(1);
    });

    describe('`getDisplayFormat()` computed defaults', function (): void {
        it('returns date-time with seconds format by default', function (): void {
            $picker = DateTimePicker::make('dt');

            expect($picker->getDisplayFormat())->toBe('M j, Y H:i:s');
        });

        it('returns date-only format when time is disabled', function (): void {
            $picker = DateTimePicker::make('dt')
                ->time(false);

            expect($picker->getDisplayFormat())->toBe('M j, Y');
        });

        it('returns time-only format without seconds when date is disabled and seconds disabled', function (): void {
            $picker = DateTimePicker::make('dt')
                ->date(false)
                ->seconds(false);

            expect($picker->getDisplayFormat())->toBe('H:i');
        });

        it('returns time-only format with seconds when date is disabled', function (): void {
            $picker = DateTimePicker::make('dt')
                ->date(false);

            expect($picker->getDisplayFormat())->toBe('H:i:s');
        });

        it('returns date-time without seconds format when seconds disabled', function (): void {
            $picker = DateTimePicker::make('dt')
                ->seconds(false);

            expect($picker->getDisplayFormat())->toBe('M j, Y H:i');
        });

        it('uses custom `displayFormat()` over computed default', function (): void {
            $picker = DateTimePicker::make('dt')
                ->time(false)
                ->displayFormat('d/m/Y');

            expect($picker->getDisplayFormat())->toBe('d/m/Y');
        });
    });

    describe('custom default display formats', function (): void {
        it('can override `defaultDateDisplayFormat()`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->time(false)
                ->defaultDateDisplayFormat('d/m/Y');

            expect($picker->getDisplayFormat())->toBe('d/m/Y');
        });

        it('can override `defaultDateTimeDisplayFormat()`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->seconds(false)
                ->defaultDateTimeDisplayFormat('d/m/Y H:i');

            expect($picker->getDisplayFormat())->toBe('d/m/Y H:i');
        });

        it('can override `defaultDateTimeWithSecondsDisplayFormat()`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->defaultDateTimeWithSecondsDisplayFormat('d/m/Y H:i:s');

            expect($picker->getDisplayFormat())->toBe('d/m/Y H:i:s');
        });

        it('can override `defaultTimeDisplayFormat()`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->date(false)
                ->seconds(false)
                ->defaultTimeDisplayFormat('g:i A');

            expect($picker->getDisplayFormat())->toBe('g:i A');
        });

        it('can override `defaultTimeWithSecondsDisplayFormat()`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->date(false)
                ->defaultTimeWithSecondsDisplayFormat('g:i:s A');

            expect($picker->getDisplayFormat())->toBe('g:i:s A');
        });
    });

    describe('boolean flags with `Closure`', function (): void {
        it('can set `date()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->date(static fn (): bool => false);

            expect($picker->hasDate())->toBeFalse();
        });

        it('can set `time()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->time(static fn (): bool => false);

            expect($picker->hasTime())->toBeFalse();
        });

        it('can set `seconds()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->seconds(static fn (): bool => false);

            expect($picker->hasSeconds())->toBeFalse();
        });

        it('can set `closeOnDateSelection()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->closeOnDateSelection(static fn (): bool => true);

            expect($picker->shouldCloseOnDateSelection())->toBeTrue();
        });
    });

    describe('`Closure` support for other setters', function (): void {
        it('can set `format()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->format(static fn (): string => 'U');

            expect($picker->getFormat())->toBe('U');
        });

        it('can set `displayFormat()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->displayFormat(static fn (): string => 'l, F j');

            expect($picker->getDisplayFormat())->toBe('l, F j');
        });

        it('can set `minutesStep()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->minutesStep(static fn (): int => 15);

            expect($picker->getMinutesStep())->toBe(15);
        });

        it('can set `secondsStep()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->secondsStep(static fn (): int => 30);

            expect($picker->getSecondsStep())->toBe(30);
        });

        it('can set `timezone()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->timezone(static fn (): string => 'Europe/London');

            expect($picker->getTimezone())->toBe('Europe/London');
        });

        it('can set `locale()` with a `Closure`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->locale(static fn (): string => 'ja');

            expect($picker->getLocale())->toBe('ja');
        });
    });

    describe('timezone fallback', function (): void {
        it('returns app timezone for `getTimezone()` when `hasTime()` is `false`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->time(false);

            expect($picker->getTimezone())->toBe(config('app.timezone'));
        });
    });

    describe('`getStep()` priority', function (): void {
        it('prioritizes `secondsStep()` over `minutesStep()` and `hoursStep()`', function (): void {
            $picker = DateTimePicker::make('dt')
                ->secondsStep(10)
                ->minutesStep(5)
                ->hoursStep(2);

            expect($picker->getStep())->toBe(10);
        });

        it('prioritizes `minutesStep()` over `hoursStep()` when `secondsStep()` is 1', function (): void {
            $picker = DateTimePicker::make('dt')
                ->minutesStep(5)
                ->hoursStep(2);

            expect($picker->getStep())->toBe(300);
        });

        it('uses explicit `step()` over all computed values', function (): void {
            $picker = DateTimePicker::make('dt')
                ->step(42)
                ->secondsStep(10)
                ->minutesStep(5);

            expect($picker->getStep())->toBe(42);
        });
    });
});

describe('validation rule closures', function (): void {
    it('compares only the visible parts of timed limits without changing getter values', function (string $pickerClass, bool $hasSeconds, bool $isNative): void {
        $picker = $pickerClass::make('appointment')
            ->native($isNative)
            ->seconds($hasSeconds)
            ->timezone('Asia/Kathmandu')
            ->minDate('2025-07-15 09:30:23')
            ->maxDate('2025-07-15 17:45:47');

        $datePrefix = $picker->hasDate() ? '2025-07-15 ' : ($isNative ? '' : '2031-02-04 ');

        foreach ([
            ['09:29:59', false],
            ['09:30:00', ! $hasSeconds],
            ['09:30:23', true],
            ['17:45:47', true],
            ['17:45:59', ! $hasSeconds],
            ['17:46:00', false],
        ] as [$time, $isValid]) {
            expect(Validator::make(
                ['appointment' => "{$datePrefix}{$time}"],
                ['appointment' => $picker->getValidationRules()],
            )->passes())->toBe($isValid);
        }

        expect($picker->getMinDate())->toBe('2025-07-15 09:30:23')
            ->and($picker->getMaxDate())->toBe('2025-07-15 17:45:47');
    })->with([[DateTimePicker::class], [TimePicker::class]])->with([true, false])->with([true, false]);

    it('does not wrap a hidden-second minimum into the next day', function (bool $isNative): void {
        $picker = TimePicker::make('appointment')
            ->native($isNative)
            ->seconds(false)
            ->minDate('23:59:23');

        foreach (['00:00:00' => false, '23:58:59' => false, '23:59:00' => true, '23:59:59' => true] as $time => $isValid) {
            expect(Validator::make(
                ['appointment' => ($isNative ? '' : '2031-02-04 ') . $time],
                ['appointment' => $picker->getValidationRules()],
            )->passes())->toBe($isValid);
        }
    })->with([true, false]);

    it('preserves the time of bounds when `hasTime()` is `true`', function (string $pickerClass, bool $isNative): void {
        $picker = $pickerClass::make('appointment')->native($isNative);
        $datePrefix = $picker->hasDate() ? '2025-07-15 ' : '';

        $picker->minDate("{$datePrefix}09:30:23")
            ->maxDate("{$datePrefix}17:45:47");

        foreach (['09:30:22' => false, '09:30:23' => true, '17:45:47' => true, '17:45:48' => false] as $time => $isValid) {
            expect(Validator::make(
                ['appointment' => "{$datePrefix}{$time}"],
                ['appointment' => $picker->getValidationRules()],
            )->passes())->toBe($isValid);
        }
    })->with([
        'date-time' => [DateTimePicker::class],
        'time-only' => [TimePicker::class],
    ])->with([true, false]);

    it('rejects date exceeding `maxDate()` via rule closure', function (): void {
        livewire(DateTimePickerWithMaxDate::class)
            ->fillForm(['dt' => '2025-12-31'])
            ->call('save')
            ->assertHasFormErrors(['dt']);
    });

    it('accepts date within `maxDate()` via rule closure', function (): void {
        livewire(DateTimePickerWithMaxDate::class)
            ->fillForm(['dt' => '2024-06-15'])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('rejects date before `minDate()` via rule closure', function (): void {
        livewire(DateTimePickerWithMinDate::class)
            ->fillForm(['dt' => '2020-01-01'])
            ->call('save')
            ->assertHasFormErrors(['dt']);
    });

    it('accepts date after `minDate()` via rule closure', function (): void {
        livewire(DateTimePickerWithMinDate::class)
            ->fillForm(['dt' => '2024-06-15'])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('applies `date` rule when `hasDate()` is `true`', function (): void {
        livewire(DateTimePickerWithDateValidation::class)
            ->fillForm(['dt' => 'not-a-date'])
            ->call('save')
            ->assertHasFormErrors(['dt']);
    });

    it('accepts valid date string', function (): void {
        livewire(DateTimePickerWithDateValidation::class)
            ->fillForm(['dt' => '2024-06-15 12:00:00'])
            ->call('save')
            ->assertHasNoFormErrors();
    });
});

describe('bounds and state persistence', function (): void {
    it('renders typed timed bounds and retains their meaning through save and reload', function (bool $hasDate, bool $hasSeconds, bool $isNative, string $source, string $timezone, string $savedHour): void {
        config(['app.timezone' => 'Europe/London']);
        $this->travelTo(Carbon::parse('2025-07-20 12:00:00', 'Europe/London'));

        $livewire = livewire(DateTimePickerWithBoundedTime::class, compact('hasDate', 'hasSeconds', 'isNative', 'source', 'timezone'));
        $seconds = $hasSeconds ? '23' : '00';
        $state = (($hasDate || (! $isNative)) ? '2025-07-15 ' : '') . "09:30:{$seconds}";
        $saved = ($hasDate ? '2025-07-15 ' : '') . $savedHour . ($hasSeconds ? ':23' : '');

        if ($isNative) {
            $livewire->assertSeeHtml('min="' . ($hasDate ? '2025-07-15T' : '') . '09:30' . ($hasSeconds ? ':23' : '') . '"')
                ->assertSeeHtml('max="' . ($hasDate ? '2025-07-17T' : '') . '17:45' . ($hasSeconds ? ':47' : '') . '"');
        }

        $livewire->set('data.appointment', $state)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.appointment', $saved)
            ->call('reloadForm')
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.appointment', $saved);
    })->with([true, false])->with([true, false])->with([true, false])
        ->with(['string', Carbon::class, CarbonImmutable::class])
        ->with([['America/New_York', '14:30'], ['Asia/Kathmandu', '04:45']]);

    it('preserves hidden seconds with an explicit storage format while ignoring them in limits', function (bool $hasDate): void {
        config(['app.timezone' => 'Europe/London']);
        $this->travelTo(Carbon::parse('2025-07-20 12:00:00', 'Europe/London'));

        $livewire = livewire(DateTimePickerWithBoundedTime::class, [
            'hasDate' => $hasDate,
            'hasSeconds' => false,
            'isNative' => false,
            'format' => $hasDate ? 'd/m/Y H:i:s' : 'H:i:s',
            'timezone' => 'Asia/Kathmandu',
        ]);

        foreach (['09:30:07' => '04:45:07', '17:45:59' => '13:00:59'] as $input => $saved) {
            $expected = ($hasDate ? '15/07/2025 ' : '') . $saved;
            $livewire->set('data.appointment', "2025-07-15 {$input}")
                ->call('save')
                ->assertHasNoFormErrors()
                ->assertSet('saved.appointment', $expected)
                ->call('reloadForm')
                ->call('save')
                ->assertHasNoFormErrors()
                ->assertSet('saved.appointment', $expected);
        }
    })->with([true, false]);

    it('preserves offset-string comparison semantics across app timezone and DST boundaries', function (string $appTimezone, string $minimum, string $maximum, string $before, string $first, string $last, string $after): void {
        $originalTimezone = date_default_timezone_get();
        config(['app.timezone' => $appTimezone]);
        date_default_timezone_set($appTimezone);

        try {
            $picker = DateTimePicker::make('appointment')
                ->seconds(false)
                ->timezone('Asia/Kathmandu')
                ->minDate($minimum)
                ->maxDate(static fn () => $maximum);

            foreach ([[$before, false], [$first, true], [$last, true], [$after, false]] as [$value, $isValid]) {
                expect(Validator::make(['appointment' => $value], ['appointment' => $picker->getValidationRules()])->passes())->toBe($isValid);
            }

            livewire(DateTimePickerWithBoundedTime::class, [
                'hasSeconds' => false,
                'minimumDate' => $minimum,
                'maximumDate' => $maximum,
                'timezone' => 'Asia/Kathmandu',
            ])
                ->assertSeeHtml('min="' . str_replace(' ', 'T', $first) . '"')
                ->assertSeeHtml('max="' . str_replace(' ', 'T', $last) . '"');
        } finally {
            date_default_timezone_set($originalTimezone);
        }
    })->with([
        ['Europe/London', '2025-03-30T00:30:23+00:00', '2025-03-30T02:45:47+01:00', '2025-03-30 00:29', '2025-03-30 00:30', '2025-03-30 02:45', '2025-03-30 02:46'],
        ['Europe/London', '2025-10-26T00:30:23+01:00', '2025-10-26T02:45:47+00:00', '2025-10-26 00:29', '2025-10-26 00:30', '2025-10-26 02:45', '2025-10-26 02:46'],
        ['Europe/London', '2025-07-15T00:15:23+09:00', '2025-07-15T17:45:47-04:00', '2025-07-14 16:14', '2025-07-14 16:15', '2025-07-15 22:45', '2025-07-15 22:46'],
    ]);

    it('preserves custom limit validation messages and `null` resets for time-only fields', function (): void {
        $picker = TimePicker::make('appointment')
            ->seconds(false)
            ->minDate('09:30:23')
            ->maxDate('17:45:47')
            ->validationMessages(['after_or_equal' => 'Too early.', 'before_or_equal' => 'Too late.']);

        foreach (['09:29' => ['AfterOrEqual', 'Too early.'], '17:46' => ['BeforeOrEqual', 'Too late.']] as $value => [$rule, $message]) {
            $validator = Validator::make(['appointment' => $value], ['appointment' => $picker->getValidationRules()], $picker->getValidationMessages());

            expect($validator->errors()->first())->toBe($message)
                ->and($validator->failed()['appointment'])->toHaveKey($rule);
        }

        $validator = Validator::make(
            ['appointment' => '2031-02-04 09:29:59'],
            ['appointment' => $picker->getValidationRules()],
            ['appointment.after_or_equal' => 'Business hours only.'],
        );

        expect($validator->errors()->first())->toBe('Business hours only.')
            ->and($validator->failed()['appointment'])->toHaveKey('AfterOrEqual');

        $picker->minDate(static fn () => null)->maxDate(null);

        expect(Validator::make(['appointment' => '03:17'], ['appointment' => $picker->getValidationRules()])->passes())->toBeTrue();
    });

    it('does not turn reversed time limits into an overnight range', function (bool $isNative): void {
        $livewire = livewire(DateTimePickerWithBoundedTime::class, [
            'hasDate' => false,
            'isNative' => $isNative,
            'minimumDate' => '18:30:23',
            'maximumDate' => '06:45:47',
        ]);

        if ($isNative) {
            $livewire->assertDontSeeHtml('min="18:30:23"')->assertDontSeeHtml('max="06:45:47"');
        }

        foreach (['05:00:00', '12:00:00', '20:00:00'] as $time) {
            $livewire->set('data.appointment', ($isNative ? '' : '2025-07-15 ') . $time)
                ->call('save')
                ->assertHasFormErrors(['appointment']);
        }
    })->with([true, false]);

    it('honors `date_format` and validation-time mutations when comparing time-only bounds', function (string $format): void {
        $picker = TimePicker::make('appointment')
            ->seconds(false)
            ->rule("date_format:{$format}")
            ->minDate('2025-07-15 09:30:23')
            ->maxDate('2025-07-17 17:45:47');

        $suffix = $format === 'H:i:s' ? ':59' : '';

        foreach (['09:29' => 'AfterOrEqual', '09:30' => null, '12:00' => null, '17:45' => null, '17:46' => 'BeforeOrEqual'] as $time => $failedRule) {
            $validator = Validator::make(['appointment' => "{$time}{$suffix}"], ['appointment' => $picker->getValidationRules()]);

            expect($validator->passes())->toBe($failedRule === null);

            if ($failedRule !== null) {
                expect($validator->failed()['appointment'])->toHaveKey($failedRule);
            }
        }

        $picker = TimePicker::make('appointment')->minDate('09:30:23')->maxDate('17:45:47');
        $validator = Validator::make(['appointment' => '2031-02-04 12:00:00'], ['appointment' => $picker->getValidationRules()]);
        $validator->setValue('appointment', '2031-02-05 12:00:00');

        expect($validator->passes())->toBeTrue();

        $validator->setValue('appointment', '2031-02-06 17:45:48');

        expect($validator->fails())->toBeTrue()
            ->and($validator->failed()['appointment'])->toHaveKey('BeforeOrEqual');
    })->with(['H:i', 'H:i:s']);

    it('saves and reloads valid field times without applying a DST gap from the app timezone', function (bool $isNative, bool $hasSeconds): void {
        $originalTimezone = date_default_timezone_get();
        config(['app.timezone' => 'Europe/London']);
        date_default_timezone_set('Europe/London');

        try {
            $livewire = livewire(DateTimePickerWithBoundedTime::class, [
                'isNative' => $isNative,
                'hasSeconds' => $hasSeconds,
                'timezone' => 'America/New_York',
                'format' => 'd/m/Y H:i:s',
                'minimumDate' => '2025-01-01 00:00:00',
                'maximumDate' => '2025-12-31 23:59:59',
            ]);
            $internal = '2025-03-30 01:30' . (($hasSeconds || (! $isNative)) ? ($hasSeconds ? ':17' : ':00') : '');
            $stored = '30/03/2025 06:30:' . ($hasSeconds ? '17' : '00');
            $livewire->set('data.appointment', $internal);

            for ($cycle = 0; $cycle < 3; $cycle++) {
                $livewire->call('save')
                    ->assertHasNoFormErrors()
                    ->assertSet('saved.appointment', $stored)
                    ->call('reloadForm')
                    ->assertSet('data.appointment', $internal);
            }
        } finally {
            date_default_timezone_set($originalTimezone);
        }
    })->with([true, false])->with([true, false]);

    it('keeps fractional native time input on the app calendar date when saving and reloading', function (): void {
        $originalTimezone = date_default_timezone_get();
        config(['app.timezone' => 'America/New_York']);
        date_default_timezone_set('America/New_York');
        $this->travelTo(Carbon::parse('2025-03-09 04:30:00', 'UTC'));

        try {
            DateTimePicker::configureUsing(static fn (DateTimePicker $component) => $component->step(0.001), during: function (): void {
                $livewire = livewire(DateTimePickerWithBoundedTime::class, [
                    'hasDate' => false,
                    'timezone' => 'Asia/Tokyo',
                    'minimumDate' => '00:00:00',
                    'maximumDate' => '23:59:59',
                ]);

                $livewire->assertSeeHtml('step="0.001"')
                    ->set('data.appointment', '20:15:47.123');

                for ($cycle = 0; $cycle < 3; $cycle++) {
                    $livewire->call('save')
                        ->assertHasNoFormErrors()
                        ->assertSet('saved.appointment', '06:15:47')
                        ->call('reloadForm')
                        ->assertSet('data.appointment', '20:15:47');
                }
            });
        } finally {
            date_default_timezone_set($originalTimezone);
        }
    });
});

class DateTimePickerWithBoundedTime extends Livewire
{
    public bool $hasDate = true;

    public bool $hasSeconds = true;

    public bool $isNative = true;

    public ?string $format = null;

    public string $minimumDate = '2025-07-15 09:30:23';

    public string $maximumDate = '2025-07-17 17:45:47';

    public string $source = 'string';

    public string $timezone = 'America/New_York';

    public array $saved = [];

    public function form(Schema $form): Schema
    {
        $minimum = $this->source === 'string' ? $this->minimumDate : $this->source::parse($this->minimumDate, 'Asia/Tokyo');
        $maximum = $this->source === 'string' ? $this->maximumDate : $this->source::parse($this->maximumDate, 'America/Los_Angeles');

        return $form->schema([
            DateTimePicker::make('appointment')
                ->date($this->hasDate)
                ->seconds($this->hasSeconds)
                ->native($this->isNative)
                ->format($this->format)
                ->timezone($this->timezone)
                ->minDate($minimum)
                ->maxDate(static fn () => $maximum),
        ])->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState();
    }

    public function reloadForm(): void
    {
        $this->form->fill($this->saved);
    }
}

class DateTimePickerWithMaxDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->maxDate('2025-01-01'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class DateTimePickerWithMinDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->minDate('2024-01-01'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class DateTimePickerWithDateValidation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

describe('rendering', function (): void {
    it('names the custom picker dialog using plain text or a non-stringable `Htmlable` label', function (bool $isHtml): void {
        $label = $isHtml ? new class implements Htmlable
        {
            public function toHtml(): string
            {
                return '<strong>Departure &lt;local&gt; &amp; return</strong>';
            }
        } : 'Departure <local> & return';

        DateTimePicker::configureUsing(
            static fn (DateTimePicker $component) => $component->label($label),
            during: static fn () => livewire(RenderDateTimePickerNonNative::class)
                ->assertSuccessful()
                ->assertSeeHtml('aria-label="Departure &lt;local&gt; &amp; return"'),
        );
    })->with([true, false]);

    it('escapes custom input IDs exactly once in accessible descriptions', function (): void {
        DateTimePicker::configureUsing(
            static fn (DateTimePicker $component) => $component
                ->id('departure&return')
                ->helperText('Choose your departure date'),
            during: static fn () => livewire(RenderDateTimePickerNonNative::class)
                ->assertSuccessful()
                ->assertSeeHtml('aria-describedby="departure&amp;return-helper-text"'),
        );
    });

    it('can render with `time(false)`', function (): void {
        livewire(RenderDateTimePickerWithTimeDisabled::class)
            ->assertSuccessful();
    });

    it('can render with `date(false)`', function (): void {
        livewire(RenderDateTimePickerWithDateDisabled::class)
            ->assertSuccessful();
    });

    it('can render with `date(false)` and `seconds(false)`', function (): void {
        livewire(RenderDateTimePickerWithDateAndSecondsDisabled::class)
            ->assertSuccessful();
    });

    it('can render with `seconds(false)`', function (): void {
        livewire(RenderDateTimePickerWithSecondsDisabled::class)
            ->assertSuccessful();
    });

    it('can render with `native(false)`', function (): void {
        livewire(RenderDateTimePickerNonNative::class)
            ->assertSuccessful();
    });

    it('can render with `native(false)` and `time(false)`', function (): void {
        livewire(RenderDateTimePickerNonNativeDateOnly::class)
            ->assertSuccessful();
    });

    it('can render with `native(false)` and `date(false)`', function (): void {
        livewire(RenderDateTimePickerNonNativeTimeOnly::class)
            ->assertSuccessful();
    });

    it('can render with custom `format()`', function (): void {
        livewire(RenderDateTimePickerWithFormat::class)
            ->assertSuccessful();
    });

    it('can render with `format()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureFormat::class)
            ->assertSuccessful();
    });

    it('can render with custom `displayFormat()` (non-native)', function (): void {
        livewire(RenderDateTimePickerWithDisplayFormat::class)
            ->assertSuccessful();
    });

    it('can render with `displayFormat()` set via `Closure` (non-native)', function (): void {
        livewire(RenderDateTimePickerWithClosureDisplayFormat::class)
            ->assertSuccessful();
    });

    it('can render with `hoursStep()`', function (): void {
        livewire(RenderDateTimePickerWithHoursStep::class)
            ->assertSuccessful();
    });

    it('can render with `hoursStep()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureHoursStep::class)
            ->assertSuccessful();
    });

    it('can render with `minutesStep()`', function (): void {
        livewire(RenderDateTimePickerWithMinutesStep::class)
            ->assertSuccessful();
    });

    it('can render with `minutesStep()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureMinutesStep::class)
            ->assertSuccessful();
    });

    it('can render with `secondsStep()`', function (): void {
        livewire(RenderDateTimePickerWithSecondsStep::class)
            ->assertSuccessful();
    });

    it('can render with `secondsStep()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureSecondsStep::class)
            ->assertSuccessful();
    });

    it('can render with `closeOnDateSelection()`', function (): void {
        livewire(RenderDateTimePickerWithCloseOnDateSelection::class)
            ->assertSuccessful();
    });

    it('can render with `closeOnDateSelection()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureCloseOnDateSelection::class)
            ->assertSuccessful();
    });

    it('can render with `locale()`', function (): void {
        livewire(RenderDateTimePickerWithLocale::class)
            ->assertSuccessful();
    });

    it('can render with `locale()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureLocale::class)
            ->assertSuccessful();
    });

    it('can render with `firstDayOfWeek()`', function (): void {
        livewire(RenderDateTimePickerWithFirstDayOfWeek::class)
            ->assertSuccessful();
    });

    it('can render with `weekStartsOnSunday()`', function (): void {
        livewire(RenderDateTimePickerWithWeekStartsOnSunday::class)
            ->assertSuccessful();
    });

    it('can render with `disabledDates()`', function (): void {
        livewire(RenderDateTimePickerWithDisabledDates::class)
            ->assertSuccessful();
    });

    it('can render with `disabledDates()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureDisabledDates::class)
            ->assertSuccessful();
    });

    it('can render with `maxDate()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureMaxDate::class)
            ->assertSuccessful();
    });

    it('can render with `minDate()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureMinDate::class)
            ->assertSuccessful();
    });

    it('can render with `defaultFocusedDate()`', function (): void {
        livewire(RenderDateTimePickerWithDefaultFocusedDate::class)
            ->assertSuccessful();
    });

    it('can render with `date()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureDate::class)
            ->assertSuccessful();
    });

    it('can render with `time()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureTime::class)
            ->assertSuccessful();
    });

    it('can render with `seconds()` set via `Closure`', function (): void {
        livewire(RenderDateTimePickerWithClosureSeconds::class)
            ->assertSuccessful();
    });

    it('can render with explicit `step()`', function (): void {
        livewire(RenderDateTimePickerWithExplicitStep::class)
            ->assertSuccessful();
    });

    it('can render with `defaultDateDisplayFormat()` (non-native)', function (): void {
        livewire(RenderDateTimePickerWithDefaultDateDisplayFormat::class)
            ->assertSuccessful();
    });

    it('can render with placeholder text', function (): void {
        livewire(RenderDateTimePickerWithPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('Pick a date and time...');
    });
});

describe('browser interactions', function (): void {
    it('enforces native timed bounds and reloads accepted values', function (bool $hasDate, bool $hasSeconds): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?bounds=1&date=' . (int) $hasDate . '&seconds=' . (int) $hasSeconds);
        $page->assertNoSmoke();

        $minimumPrefix = $hasDate ? '2025-07-15T' : '';
        $maximumPrefix = $hasDate ? '2025-07-17T' : '';
        $reloadCount = 0;

        foreach ([
            [$minimumPrefix . ($hasSeconds ? '00:30:22' : '00:29'), false, 0, null],
            [$minimumPrefix . '00:30' . ($hasSeconds ? ':23' : ''), true, 1, ($hasDate ? '2025-07-14 ' : '') . '18:45' . ($hasSeconds ? ':23' : '')],
            [$maximumPrefix . '17:45' . ($hasSeconds ? ':47' : ''), true, 2, ($hasDate ? '2025-07-17 ' : '') . '12:00' . ($hasSeconds ? ':47' : '')],
            [$maximumPrefix . ($hasSeconds ? '17:45:48' : '17:46'), false, 2, null],
        ] as [$value, $isValid, $saveCount, $saved]) {
            $page->fill('[data-testid="timed-input"]', $value)
                ->assertPresent('[data-testid="timed-input"]:' . ($isValid ? 'valid' : 'invalid'))
                ->click('[data-testid="save-timed"]');

            if (! $isValid) {
                $page->assertPresent('[data-testid="timed-input"]:invalid:focus');
            }

            $page->assertScript('document.querySelector(\'[data-testid="timed-save-count"]\').textContent.trim()', (string) $saveCount);

            if ($isValid) {
                $page->assertScript('document.querySelector(\'[data-testid="saved-timed"]\').textContent.trim()', $saved)
                    ->click('[data-testid="reload-timed"]')
                    ->assertAttribute('[data-testid="timed-save-count"]', 'data-reload-count', (string) ++$reloadCount)
                    ->assertValue('[data-testid="timed-input"]', $value);
            }
        }

        $page->click('[data-testid="reload-timed"]')
            ->assertAttribute('[data-testid="timed-save-count"]', 'data-reload-count', (string) ++$reloadCount)
            ->assertEnabled('[data-testid="reload-timed"]');

        if ($hasDate && $hasSeconds) {
            $page->assertNoAccessibilityIssues();
            $page->inDarkMode()->assertNoAccessibilityIssues();
        }
    })->with([true, false])->with([true, false]);

    it('saves a valid native field time through an app DST gap and reloads it unchanged', function (): void {
        $originalTimezone = date_default_timezone_get();
        config(['app.timezone' => 'Europe/London']);
        date_default_timezone_set('Europe/London');
        $this->actingAs(User::factory()->create());

        try {
            $page = visit('/date-time-picker-test?app-dst-gap=1')->withTimezone('America/New_York');

            $page->fill('[data-testid="timed-input"]', '2025-03-30T01:30:17')
                ->assertPresent('[data-testid="timed-input"]:valid');

            for ($cycle = 1; $cycle <= 3; $cycle++) {
                $page->click('[data-testid="save-timed"]')
                    ->assertScript('document.querySelector(\'[data-testid="timed-save-count"]\').textContent.trim()', (string) $cycle)
                    ->assertScript('document.querySelector(\'[data-testid="saved-timed"]\').textContent.trim()', '2025-03-30 06:30:17')
                    ->click('[data-testid="reload-timed"]')
                    ->assertAttribute('[data-testid="timed-save-count"]', 'data-reload-count', (string) $cycle)
                    ->assertValue('[data-testid="timed-input"]', '2025-03-30T01:30:17');
            }
            $page->assertNoSmoke();
        } finally {
            date_default_timezone_set($originalTimezone);
        }
    });

    it('preserves field calendar components through browser timezone gaps and folds, navigation, limits, saving and clearing', function (string $browserTimezone, string $state, bool $hasTime, bool $hasSeconds): void {
        $originalTimezone = date_default_timezone_get();
        config(['app.timezone' => 'UTC']);
        date_default_timezone_set('UTC');
        $this->actingAs(User::factory()->create());

        try {
            $page = visit('/date-time-picker-test?' . http_build_query([
                'native' => 0,
                'time' => (int) $hasTime,
                'seconds' => (int) $hasSeconds,
                'calendar-state' => $state,
                'display-format' => $hasTime ? ($hasSeconds ? 'Y-m-d H:i:s' : 'Y-m-d H:i') : 'Y-m-d',
            ]))->withTimezone($browserTimezone);

            $trigger = '[data-testid="timed-trigger"]';
            $format = $hasTime ? ($hasSeconds ? 'Y-m-d H:i:s' : 'Y-m-d H:i') : 'Y-m-d';
            $stored = Carbon::parse($state, 'UTC')->format($format);
            $internal = Carbon::parse($stored, 'UTC')->toDateTimeString();
            $lastDate = Carbon::parse($internal, 'UTC')->addDays(2)->toDateTimeString();
            $lastStored = Carbon::parse($lastDate, 'UTC')->format($format);

            $page->assertValue($trigger, $stored)
                ->assertNoSmoke();

            $page->click($trigger);

            if ($hasTime) {
                $hour = Carbon::parse($state, 'UTC')->hour;
                $page->fill('input[aria-label="Hour"]', (string) ($hour + 1))
                    ->assertValue($trigger, Carbon::parse($state, 'UTC')->addHour()->format($format))
                    ->fill('input[aria-label="Hour"]', (string) $hour)
                    ->assertValue($trigger, $stored);
            }

            $page->keys($trigger, 'Enter')
                ->assertPresent('[role="gridcell"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowLeft')
                ->assertPresent('[data-date="' . Carbon::parse($state, 'UTC')->subDay()->toDateString() . '"]:focus')
                ->keys('[role="gridcell"]:focus', 'Enter')
                ->assertValue($trigger, $stored)
                ->keys('[role="gridcell"]:focus', 'Escape')
                ->assertAttribute($trigger, 'aria-expanded', 'false')
                ->keys($trigger, 'Enter')
                ->assertAttribute($trigger, 'aria-expanded', 'true')
                ->assertPresent('[role="gridcell"]:focus')
                ->keys('[role="gridcell"]:focus', 'Enter')
                ->assertValue($trigger, $stored)
                ->keys('[role="gridcell"]:focus', 'Escape')
                ->assertAttribute($trigger, 'aria-expanded', 'false')
                ->assertValue($trigger, $stored);

            for ($cycle = 1; $cycle <= 2; $cycle++) {
                $page->click('[data-testid="save-timed"]')
                    ->assertScript('document.querySelector(\'[data-testid="timed-save-count"]\').textContent.trim()', (string) $cycle)
                    ->assertScript('document.querySelector(\'[data-testid="saved-timed"]\').textContent.trim()', $stored)
                    ->click('[data-testid="reload-timed"]')
                    ->assertAttribute('[data-testid="timed-save-count"]', 'data-reload-count', (string) $cycle)
                    ->assertValue($trigger, $stored);
            }

            $page->click($trigger)
                ->assertPresent('[data-date="' . Carbon::parse($internal, 'UTC')->toDateString() . '"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowRight')
                ->assertPresent('[data-date="' . Carbon::parse($internal, 'UTC')->addDay()->toDateString() . '"]:focus')
                ->keys('[role="gridcell"]:focus', 'Enter')
                ->assertValue($trigger, $stored)
                ->keys('[role="gridcell"]:focus', 'Escape')
                ->assertAttribute($trigger, 'aria-expanded', 'false')
                ->keys($trigger, 'Enter')
                ->assertAttribute($trigger, 'aria-expanded', 'true')
                ->assertPresent('[data-date="' . Carbon::parse($internal, 'UTC')->toDateString() . '"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowRight')
                ->assertPresent('[data-date="' . Carbon::parse($internal, 'UTC')->addDay()->toDateString() . '"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowRight')
                ->assertPresent('[data-date="' . Carbon::parse($lastDate, 'UTC')->toDateString() . '"]:focus')
                ->keys('[role="gridcell"]:focus', 'Enter')
                ->assertValue($trigger, $lastStored)
                ->keys('[role="gridcell"]:focus', 'Escape')
                ->click('[data-testid="save-timed"]')
                ->assertScript('document.querySelector(\'[data-testid="saved-timed"]\').textContent.trim()', $lastStored)
                ->click('[data-testid="reload-timed"]')
                ->assertAttribute('[data-testid="timed-save-count"]', 'data-reload-count', '3')
                ->click($trigger)
                ->assertPresent('[data-date="' . Carbon::parse($lastDate, 'UTC')->toDateString() . '"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowRight')
                ->assertPresent('[data-date="' . Carbon::parse($lastDate, 'UTC')->addDay()->toDateString() . '"]:focus')
                ->keys('[role="gridcell"]:focus', 'Enter')
                ->assertValue($trigger, $lastStored)
                ->keys('[role="gridcell"]:focus', 'Escape')
                ->keys($trigger, 'Backspace')
                ->assertValue($trigger, '')
                ->keys($trigger, 'Escape')
                ->click('[data-testid="save-timed"]')
                ->assertScript('document.querySelector(\'[data-testid="timed-save-count"]\').textContent.trim()', '4')
                ->assertScript('document.querySelector(\'[data-testid="saved-timed"]\').textContent.trim()', '')
                ->click('[data-testid="reload-timed"]')
                ->assertAttribute('[data-testid="timed-save-count"]', 'data-reload-count', '4')
                ->assertValue($trigger, '');

            $page->click($trigger)
                ->keys('[role="gridcell"]:focus', 'Shift+Tab')
                ->keys('input[aria-label="Year"]', 'Shift+Tab')
                ->assertPresent('select[aria-label="Month"]:focus')
                ->select('select[aria-label="Month"]', '1')
                ->fill('input[aria-label="Year"]', '2024')
                ->assertCount('[data-testid="date-time-picker"] [role="gridcell"][data-date]', 29)
                ->assertValue($trigger, '')
                ->keys('input[aria-label="Year"]', 'Escape');
            $page->assertNoSmoke();
        } finally {
            date_default_timezone_set($originalTimezone);
        }
    })->with([
        'skipped browser date' => ['Pacific/Apia', '2011-12-30 00:15:23'],
        'skipped browser midnight' => ['America/Santiago', '2025-09-07 00:15:23'],
        'skipped browser hour' => ['Europe/London', '2025-03-30 01:30:23'],
        'repeated browser midnight' => ['America/Havana', '2025-11-02 00:15:23'],
    ])->with([
        'date only' => [false, true],
        'datetime with seconds' => [true, true],
        'datetime without seconds' => [true, false],
    ]);

    it('preserves browser-based `displayFormat()` zone tokens and zoned `disabledDates()` calendar projection', function (bool $hasDate): void {
        config(['app.timezone' => 'UTC']);
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?' . http_build_query([
            'native' => 0,
            'date' => (int) $hasDate,
            'calendar-state' => '2025-07-14 12:00:00',
            'display-format' => ($hasDate ? 'Y-m-d ' : '') . 'H:i P O [Z]',
            'zoned-disabled-date' => 1,
        ]))->withTimezone('America/Los_Angeles');

        $page->assertValue('[data-testid="timed-trigger"]', $hasDate ? '2025-07-14 12:00 -07:00 -0700 Z' : '12:00 +00:00 +0000 Z')
            ->assertNoSmoke();

        if ($hasDate) {
            $page->click('[data-testid="timed-trigger"]')
                ->assertAttribute('[data-date="2025-07-14"]', 'aria-disabled', 'true')
                ->assertAttribute('[data-date="2025-07-15"]', 'aria-disabled', 'false');
        }
    })->with([true, false]);

    it('preserves hidden seconds after editing and reloading a custom time-only picker', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0&date=0&seconds=0&dst=1')->withTimezone('Europe/London');

        $page->assertValue('[data-testid="timed-trigger"]', '01:45')
            ->click('[data-testid="timed-trigger"]')
            ->assertVisible('input[aria-label="Minute"]')
            ->assertValue('input[aria-label="Hour"]', '1')
            ->assertValue('input[aria-label="Minute"]', '45')
            ->fill('input[aria-label="Minute"]', '30')
            ->assertValue('[data-testid="timed-trigger"]', '01:30')
            ->click('[data-testid="timed-trigger"]')
            ->click('[data-testid="save-timed"]')
            ->assertSeeIn('[data-testid="saved-timed"]', '01:30:07')
            ->click('[data-testid="reload-timed"]')
            ->assertAttribute('[data-testid="timed-save-count"]', 'data-reload-count', '1')
            ->click('[data-testid="timed-trigger"]')
            ->assertVisible('input[aria-label="Minute"]')
            ->assertValue('input[aria-label="Minute"]', '30')
            ->fill('input[aria-label="Minute"]', '55')
            ->assertValue('[data-testid="timed-trigger"]', '01:55')
            ->keys('input[aria-label="Minute"]', 'Escape')
            ->click('[data-testid="save-timed"]')
            ->assertSeeIn('[data-testid="saved-timed"]', '01:55:07')
            ->click('[data-testid="reload-timed"]')
            ->assertAttribute('[data-testid="timed-save-count"]', 'data-reload-count', '2')
            ->click('[data-testid="timed-trigger"]')
            ->assertValue('input[aria-label="Minute"]', '55');
        $page->assertNoSmoke();
    });

    it('uses one labelled input to open, select, copy and clear a custom picker with the keyboard', function (bool $hasDate, bool $hasTime): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0&close-on-selection=1&date=' . (int) $hasDate . '&time=' . (int) $hasTime)->withTimezone('UTC');
        $trigger = '[data-testid="timed-trigger"]';

        $page->assertValue($trigger, '')
            ->assertAttribute($trigger, 'aria-expanded', 'false')
            ->click('label[for="' . $page->attribute($trigger, 'id') . '"]')
            ->assertVisible('[role="dialog"]');

        $page->keys($hasDate ? '[role="gridcell"]:focus' : $trigger, 'Escape')
            ->assertPresent($trigger . ':focus')
            ->assertAttribute($trigger, 'aria-expanded', 'false')
            ->keys($trigger, 'Enter')
            ->assertAttribute($trigger, 'aria-expanded', 'true')
            ->assertVisible('[role="dialog"]')
            ->assertAttributeMissing('[role="dialog"]', 'aria-modal');

        $page->assertNoAccessibilityIssues();
        $page->inDarkMode()
            ->click($trigger)
            ->assertVisible('[role="dialog"]')
            ->assertNoAccessibilityIssues();

        if ($hasDate) {
            $page->assertPresent('[data-date="2025-07-15"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowRight')
                ->assertPresent('[data-date="2025-07-16"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowDown')
                ->assertPresent('[data-date="2025-07-23"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowLeft')
                ->assertPresent('[data-date="2025-07-22"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowUp')
                ->assertPresent('[data-date="2025-07-15"]:focus')
                ->keys('[role="gridcell"]:focus', 'ArrowRight')
                ->assertPresent('[data-date="2025-07-16"]:focus')
                ->keys('[role="gridcell"]:focus', 'Enter')
                ->assertAttribute('[data-date="2025-07-16"]', 'aria-selected', 'true')
                ->assertPresent($trigger . ':focus')
                ->assertAttribute($trigger, 'aria-expanded', 'false');
        } else {
            $displayedTime = sprintf('%02d:31:%02d', $page->page()->locator('input[aria-label="Hour"]')->inputValue(), $page->page()->locator('input[aria-label="Second"]')->inputValue());

            $page->assertPresent($trigger . ':focus')
                ->fill('input[aria-label="Minute"]', '31')
                ->assertValue('input[aria-label="Minute"]', '31')
                ->assertValue($trigger, $displayedTime);
        }

        $page->keys($trigger, 'Escape')
            ->assertAttribute($trigger, 'aria-expanded', 'false')
            ->assertPresent($trigger . ':focus')
            ->keys($trigger, ['ControlOrMeta+a'])
            ->assertScript('(() => { const input = document.querySelector(\'[data-testid="timed-trigger"]\'); return input.selectionEnd - input.selectionStart === input.value.length && input.value.length > 0; })()', true)
            ->keys($trigger, 'Delete')
            ->assertValue($trigger, '')
            ->click($trigger)
            ->assertAttribute($trigger, 'aria-expanded', 'true')
            ->click('h1[class]')
            ->assertAttribute($trigger, 'aria-expanded', 'false')
            ->assertScript('document.querySelector(\'[data-testid="timed-save-count"]\').textContent.trim()', '0')
            ->assertNoSmoke();
    })->with([
        'date and time' => [true, true],
        'date only' => [true, false],
        'time only' => [false, true],
    ]);

    it('navigates month and year boundaries without changing the selected date until activation', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0&time=0&initial-state=2024-01-31&display-format=Y-m-d')->withTimezone('UTC');
        $trigger = '[data-testid="timed-trigger"]';

        $page->keys($trigger, 'ArrowDown')
            ->assertPresent('[data-date="2024-01-31"]:focus')
            ->assertAttribute('[data-date="2024-01-31"]', 'aria-selected', 'true')
            ->keys('[role="gridcell"]:focus', 'PageDown')
            ->assertPresent('[data-date="2024-02-29"]:focus')
            ->assertAttribute('[data-date="2024-02-29"]', 'aria-selected', 'false')
            ->assertValue($trigger, '2024-01-31')
            ->keys('[role="gridcell"]:focus', ['Shift+PageDown'])
            ->assertPresent('[data-date="2025-02-28"]:focus')
            ->keys('[role="gridcell"]:focus', 'ArrowRight')
            ->assertPresent('[data-date="2025-03-01"]:focus')
            ->keys('[role="gridcell"]:focus', 'Home')
            ->assertPresent('[data-date="2025-02-24"]:focus')
            ->keys('[role="gridcell"]:focus', 'End')
            ->assertPresent('[data-date="2025-03-02"]:focus')
            ->keys('[role="gridcell"]:focus', ['ArrowUp', 'ArrowDown', 'PageUp'])
            ->assertPresent('[data-date="2025-02-02"]:focus')
            ->keys('[role="gridcell"]:focus', ['Shift+PageUp'])
            ->assertPresent('[data-date="2024-02-02"]:focus')
            ->assertCount('[role="gridcell"][tabindex="0"]', 1)
            ->assertScript('Array.from(document.querySelectorAll(\'[role="grid"] [role="row"]\')).every(row => row.querySelectorAll(\'[role="gridcell"], [role="columnheader"]\').length === 7)', true)
            ->keys('[role="gridcell"]:focus', ' ')
            ->assertValue($trigger, '2024-02-02')
            ->assertPresent('[data-date="2024-02-02"]:focus')
            ->assertAttribute('[data-date="2024-02-02"]', 'aria-selected', 'true')
            ->keys('[role="gridcell"]:focus', 'Escape')
            ->assertPresent($trigger . ':focus')
            ->keys($trigger, 'Enter')
            ->assertPresent('[data-date="2024-02-02"]:focus')
            ->keys('[role="gridcell"]:focus', 'Escape')
            ->keys($trigger, 'Delete')
            ->keys($trigger, 'Enter')
            ->assertPresent('[data-date="2024-02-02"]:focus')
            ->assertValue($trigger, '')
            ->assertCount('[role="gridcell"][aria-selected="true"]', 0)
            ->keys('[role="gridcell"]:focus', 'Escape')
            ->assertNoSmoke();

        $page->keys($trigger, 'Enter')
            ->fill('input[aria-label="Year"]', '2024')
            ->select('select[aria-label="Month"]', '11')
            ->click('[data-date="2024-12-31"]')
            ->assertPresent('[data-date="2024-12-31"]:focus')
            ->keys('[role="gridcell"]:focus', 'ArrowRight')
            ->assertPresent('[data-date="2025-01-01"]:focus')
            ->keys('[role="gridcell"]:focus', 'ArrowLeft')
            ->assertPresent('[data-date="2024-12-31"]:focus')
            ->assertValue($trigger, '2024-12-31')
            ->withKeyDown('ArrowRight', static fn () => null)
            ->withKeyDown('ArrowRight', static fn () => null)
            ->withKeyDown('Enter', static fn () => null)
            ->assertValue($trigger, '2025-01-02')
            ->assertPresent('[data-date="2025-01-02"]:focus')
            ->withKeyDown('PageUp', static fn () => null)
            ->withKeyDown('Tab', static fn () => null)
            ->assertPresent('[data-testid="save-timed"]:focus')
            ->assertAttribute($trigger, 'aria-expanded', 'false')
            ->assertValue($trigger, '2025-01-02');
    });

    it('keeps unavailable dates focusable but rejects activation, even in a wholly unavailable month', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0&bounds=1&disabled-dates=1&initial-state=2025-07-15%2007:39:37&display-format=Y-m-d%20H:i:s')->withTimezone('UTC');
        $trigger = '[data-testid="timed-trigger"]';

        $page->click($trigger)
            ->keys('[role="gridcell"]:focus', 'ArrowRight')
            ->assertPresent('[data-date="2025-07-16"]:focus')
            ->assertAttribute('[data-date="2025-07-16"]', 'aria-disabled', 'true')
            ->keys('[role="gridcell"]:focus', ['Enter', ' '])
            ->assertValue($trigger, '2025-07-15 13:24:37')
            ->assertAttribute($trigger, 'aria-expanded', 'true')
            ->assertAttribute('[data-date="2025-07-14"]', 'aria-disabled', 'true')
            ->assertAttribute('[data-date="2025-07-15"]', 'aria-disabled', 'false')
            ->assertAttribute('[data-date="2025-07-17"]', 'aria-disabled', 'false')
            ->assertAttribute('[data-date="2025-07-18"]', 'aria-disabled', 'true')
            ->assertNoAccessibilityIssues()
            ->keys('[role="gridcell"]:focus', 'PageDown')
            ->assertPresent('[data-date="2025-08-16"]:focus')
            ->assertCount('[role="gridcell"][data-date]:not([aria-disabled="true"])', 0)
            ->keys('[role="gridcell"]:focus', 'Enter')
            ->assertValue($trigger, '2025-07-15 13:24:37')
            ->keys('[role="gridcell"]:focus', 'Escape')
            ->assertPresent($trigger . ':focus')
            ->assertNoSmoke();

        $page->inDarkMode()->click($trigger)
            ->assertVisible('[role="dialog"]')
            ->assertNoAccessibilityIssues();
    });

    it('uses localized date names, a Sunday week start and RTL horizontal navigation', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0&time=0&locale=fr&week-start=7&rtl=1')->withTimezone('UTC');
        $trigger = '[data-testid="timed-trigger"]';

        $page->keys($trigger, 'Enter')
            ->assertPresent('[data-date="2025-07-15"]:focus')
            ->assertAttribute('[data-date="2025-07-15"]', 'aria-label', 'mardi, 15 juillet 2025')
            ->assertAttribute('[role="grid"]', 'aria-label', 'juillet 2025')
            ->assertAttribute('[role="columnheader"]:first-of-type', 'aria-label', 'dimanche')
            ->keys('[role="gridcell"]:focus', 'ArrowRight')
            ->assertPresent('[data-date="2025-07-14"]:focus')
            ->keys('[role="gridcell"]:focus', ['ArrowLeft', 'Home'])
            ->assertPresent('[data-date="2025-07-13"]:focus')
            ->keys('[role="gridcell"]:focus', 'End')
            ->assertPresent('[data-date="2025-07-19"]:focus')
            ->keys('[role="gridcell"]:focus', ['Shift+Tab'])
            ->assertPresent('input[aria-label="Year"]:focus')
            ->keys('input[aria-label="Year"]', 'Escape')
            ->assertPresent($trigger . ':focus')
            ->keys($trigger, 'Enter')
            ->keys('[role="gridcell"]:focus', 'Tab')
            ->assertPresent('[data-testid="save-timed"]:focus')
            ->assertAttribute($trigger, 'aria-expanded', 'false')
            ->assertNoSmoke();
    });

    it('closes the calendar before its nested action modal and restores each focus owner in turn', function (bool $overlaysParentModal): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0&overlay=' . (int) $overlaysParentModal);

        $page->keys('[data-testid="calendar-modal-trigger"]', 'Enter')
            ->assertVisible('[data-testid="calendar-modal"]')
            ->assertAttributeMissing('[data-testid="nested-calendar-modal-trigger"]', 'aria-disabled')
            ->keys('[data-testid="nested-calendar-modal-trigger"]', 'Enter')
            ->assertVisible('[data-testid="nested-calendar-modal"]')
            ->click('[data-testid="nested-calendar-trigger"]')
            ->assertPresent('[role="gridcell"]:focus');
        $page->assertNoAccessibilityIssues();
        $page->inDarkMode()
            ->click('[data-testid="calendar-modal-trigger"]')
            ->assertVisible('[data-testid="calendar-modal"]')
            ->click('[data-testid="nested-calendar-modal-trigger"]')
            ->assertVisible('[data-testid="nested-calendar-modal"]')
            ->click('[data-testid="nested-calendar-trigger"]')
            ->assertVisible('[data-testid="nested-calendar-modal"] [role="grid"]')
            ->assertNoAccessibilityIssues();

        $page->keys('[role="gridcell"]:focus', 'Escape')
            ->assertAttribute('[data-testid="nested-calendar-trigger"]', 'aria-expanded', 'false')
            ->assertPresent('[data-testid="nested-calendar-trigger"]:focus')
            ->assertVisible('[data-testid="nested-calendar-modal"]')
            ->keys('[data-testid="nested-calendar-trigger"]', 'Escape')
            ->assertMissing('[data-testid="nested-calendar-modal"]')
            ->assertPresent('[data-testid="nested-calendar-modal-trigger"]:focus')
            ->keys('[data-testid="nested-calendar-modal-trigger"]', 'Escape')
            ->assertMissing('[data-testid="calendar-modal"]')
            ->assertPresent('[data-testid="calendar-modal-trigger"]:focus')
            ->assertNoSmoke();
    })->with([
        'replacing parent modal' => [false],
        'overlaying parent modal' => [true],
    ]);

    it('preserves control focus and the selected date when browsing before editing time', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0&initial-state=2024-03-31%2013:24:37&display-format=Y-m-d%20H:i:s')->withTimezone('UTC');
        $trigger = '[data-testid="timed-trigger"]';

        $page->click($trigger)
            ->assertPresent('[data-date="2024-03-31"]:focus')
            ->keys('[role="gridcell"]:focus', ['Shift+Tab'])
            ->fill('input[aria-label="Year"]', '2025')
            ->assertPresent('[data-date="2025-03-31"][tabindex="0"]')
            ->assertPresent('input[aria-label="Year"]:focus')
            ->keys('input[aria-label="Year"]', ['Shift+Tab'])
            ->select('select[aria-label="Month"]', '1')
            ->assertPresent('[data-date="2025-02-28"][tabindex="0"]')
            ->assertValue($trigger, '2024-03-31 13:24:37')
            ->assertPresent('select[aria-label="Month"]:focus')
            ->keys('select[aria-label="Month"]', 'Tab')
            ->keys('input[aria-label="Year"]', 'Tab')
            ->assertPresent('[data-date="2025-02-28"]:focus')
            ->keys('[role="gridcell"]:focus', 'Tab')
            ->fill('input[aria-label="Minute"]', '31')
            ->assertValue($trigger, '2024-03-31 13:31:37')
            ->assertPresent('input[aria-label="Minute"]:focus')
            ->keys('input[aria-label="Minute"]', 'Escape')
            ->keys($trigger, 'Delete')
            ->keys($trigger, 'Enter')
            ->assertPresent('[data-date="2025-02-28"]:focus')
            ->fill('input[aria-label="Minute"]', '19')
            ->assertValue($trigger, '2025-02-28 00:19:00')
            ->keys('input[aria-label="Minute"]', 'Escape')
            ->assertNoSmoke();

        $page->keys($trigger, 'Enter')
            ->fill('input[aria-label="Year"]', '2024')
            ->select('select[aria-label="Month"]', '11')
            ->click('[data-date="2024-12-31"]')
            ->assertPresent('[data-date="2024-12-31"]:focus')
            ->withKeyDown('ArrowRight', static fn () => null)
            ->withKeyDown('Tab', static fn () => null)
            ->assertPresent('input[aria-label="Hour"]:focus')
            ->assertValue($trigger, '2024-12-31 00:19:00')
            ->keys('input[aria-label="Hour"]', 'Shift+Tab')
            ->assertPresent('[data-date="2025-01-01"]:focus')
            ->keys('[role="gridcell"]:focus', 'Escape')
            ->assertNoSmoke();
    });

    it('opens the calendar and focuses an invalid stepped time input on submission', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0&hour-step=2&minute-step=5&initial-state=2025-07-15%2014:25:00&display-format=Y-m-d%20H:i:s')->withTimezone('UTC');
        $trigger = '[data-testid="timed-trigger"]';

        $page->click($trigger)
            ->assertPresent('[role="gridcell"]:focus')
            ->fill('input[aria-label="Hour"]', '14')
            ->fill('input[aria-label="Minute"]', '31')
            ->keys('input[aria-label="Minute"]', 'Escape')
            ->click('[data-testid="save-timed"]')
            ->assertPresent('input[aria-label="Minute"]:focus')
            ->assertAttribute($trigger, 'aria-expanded', 'true')
            ->assertScript('document.querySelector(\'input[aria-label="Minute"]\').validity.stepMismatch', true)
            ->assertNoAccessibilityIssues()
            ->fill('input[aria-label="Minute"]', '30')
            ->assertValue($trigger, '2025-07-15 14:30:00')
            ->keys('input[aria-label="Minute"]', 'Escape')
            ->click('[data-testid="save-timed"]')
            ->assertSeeIn('[data-testid="saved-timed"]', '14:30')
            ->assertNoSmoke();

        $page->inDarkMode()->click($trigger)
            ->fill('input[aria-label="Minute"]', '31')
            ->keys('input[aria-label="Minute"]', 'Escape')
            ->click('[data-testid="save-timed"]')
            ->assertPresent('input[aria-label="Minute"]:invalid:focus')
            ->assertNoAccessibilityIssues();
    });

    it('can use the calendar after replacing its configuration', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?native=0');
        $trigger = '[data-testid="timed-trigger"]';

        $page->click($trigger)
            ->assertPresent('[data-date="2025-07-15"]:focus')
            ->click('[data-testid="replace-calendar"]')
            ->assertAttribute('[data-testid="replace-calendar"]', 'aria-pressed', 'true')
            ->assertAttribute($trigger, 'aria-expanded', 'false')
            ->assertPresent('[data-testid="replace-calendar"]:focus')
            ->click($trigger)
            ->assertPresent('[data-date="2025-07-15"]:focus')
            ->assertAttribute('[data-date="2025-07-16"]', 'aria-disabled', 'true')
            ->keys('[role="gridcell"]:focus', 'Escape')
            ->assertNoSmoke();
    });

    it('does not open or clear a disabled or read-only custom picker, including with `autofocus()`', function (string $mode, bool $hasDate, bool $hasTime): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-time-picker-test?' . http_build_query([
            'native' => 0,
            'date' => (int) $hasDate,
            'time' => (int) $hasTime,
            'dst' => 1,
            'autofocus' => 1,
            'calendar-state' => '2025-07-15 01:45:07',
            'display-format' => $hasDate ? ($hasTime ? 'Y-m-d H:i:s' : 'Y-m-d') : 'H:i:s',
            $mode => 1,
        ]));
        $trigger = '[data-testid="timed-trigger"]';

        $page->assertAttribute($trigger, 'aria-expanded', 'false');

        if ($mode === 'disabled') {
            $page->assertDisabled($trigger);
        } else {
            $page->click($trigger)
                ->assertPresent($trigger . ':focus')
                ->keys($trigger, ['Enter', 'ArrowRight', 'Backspace', 'Delete']);
        }

        $page->assertAttribute($trigger, 'aria-expanded', 'false')
            ->assertValue($trigger, $hasDate ? ($hasTime ? '2025-07-15 01:45:07' : '2025-07-15') : '01:45:07')
            ->assertScript('document.querySelector(\'[data-testid="timed-save-count"]\').textContent.trim()', '0')
            ->assertNoSmoke();

        if ($hasDate && $hasTime) {
            $page->assertNoAccessibilityIssues();
            $page->inDarkMode()->assertNoAccessibilityIssues();
        }
    })->with(['disabled', 'readonly'])->with([
        'time-only' => [false, true],
        'date-only' => [true, false],
        'date-and-time' => [true, true],
    ]);
});

class RenderDateTimePickerWithTimeDisabled extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->time(false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithDateDisabled extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->date(false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithDateAndSecondsDisabled extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->date(false)->seconds(false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithSecondsDisabled extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->seconds(false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerNonNative extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->native(false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerNonNativeDateOnly extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->native(false)->time(false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerNonNativeTimeOnly extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->native(false)->date(false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->format('d/m/Y'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->format(static fn (): string => 'U'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithDisplayFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->displayFormat('F j, Y g:i A'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureDisplayFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->displayFormat(static fn (): string => 'l, F j'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithHoursStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->hoursStep(2),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureHoursStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->hoursStep(static fn (): int => 3),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithMinutesStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->minutesStep(15),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureMinutesStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->minutesStep(static fn (): int => 15),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithSecondsStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->secondsStep(30),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureSecondsStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->secondsStep(static fn (): int => 30),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithCloseOnDateSelection extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->closeOnDateSelection(),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureCloseOnDateSelection extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->closeOnDateSelection(static fn (): bool => true),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithLocale extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->locale('fr'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureLocale extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->locale(static fn (): string => 'ja'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithFirstDayOfWeek extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->firstDayOfWeek(0),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithWeekStartsOnSunday extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->weekStartsOnSunday(),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithDisabledDates extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->disabledDates(['2025-01-01', '2025-12-25']),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureDisabledDates extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->disabledDates(static fn (): array => ['2025-12-25']),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureMaxDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->maxDate(static fn (): string => '2030-01-01'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureMinDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->minDate(static fn (): string => '2000-01-01'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithDefaultFocusedDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->defaultFocusedDate('2025-06-15'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->date(static fn (): bool => false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureTime extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->time(static fn (): bool => false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithClosureSeconds extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->seconds(static fn (): bool => false),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithExplicitStep extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')->step(42),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithDefaultDateDisplayFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->native(false)
                    ->time(false)
                    ->defaultDateDisplayFormat('d/m/Y'),
            ])
            ->statePath('data');
    }
}

class RenderDateTimePickerWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateTimePicker::make('dt')
                    ->placeholder('Pick a date and time...'),
            ])
            ->statePath('data');
    }
}

it('does not double-apply the `timezone()` conversion when nested under a relationship', function (): void {
    config(['app.timezone' => 'UTC']);

    $user = User::factory()->create();
    Profile::factory()->create([
        'user_id' => $user->id,
        'created_at' => '2026-04-08 21:00:00',
        'updated_at' => '2026-04-08 21:00:00',
    ]);

    livewire(DateTimePickerInRelationshipSection::class, ['record' => $user->fresh()])
        ->assertSchemaStateSet(function (array $state): array {
            // 21:00 UTC == 17:00 EDT (`America/New_York` is UTC-4 in April).
            expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');

            return [];
        });
});

class DateTimePickerInRelationshipSection extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Profile')
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

it('applies a `default()` value AND casts the `timezone()` exactly once when no related record exists', function (): void {
    config(['app.timezone' => 'UTC']);

    $user = User::factory()->create();

    livewire(DateTimePickerInRelationshipSectionWithDefault::class, ['record' => $user->fresh()])
        ->assertSchemaStateSet(function (array $state): array {
            // Default `'2026-04-08 21:00:00'` UTC == `17:00` EDT.
            expect($state['profile']['created_at'])->toBe('2026-04-08 17:00:00');

            return [];
        });
});

it('uses the related record value over `default()` and casts the `timezone()` exactly once', function (): void {
    config(['app.timezone' => 'UTC']);

    $user = User::factory()->create();
    Profile::factory()->create([
        'user_id' => $user->id,
        'created_at' => '2026-04-09 14:00:00',
        'updated_at' => '2026-04-09 14:00:00',
    ]);

    livewire(DateTimePickerInRelationshipSectionWithDefault::class, ['record' => $user->fresh()])
        ->assertSchemaStateSet(function (array $state): array {
            // Record's `2026-04-09 14:00:00` UTC == `10:00` EDT.
            expect($state['profile']['created_at'])->toBe('2026-04-09 10:00:00');

            return [];
        });
});

class DateTimePickerInRelationshipSectionWithDefault extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Profile')
                    ->relationship('profile')
                    ->schema([
                        DateTimePicker::make('created_at')
                            ->timezone('America/New_York')
                            ->default('2026-04-08 21:00:00'),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
