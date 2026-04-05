<?php

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('returns full datetime format by default (native with date, time and seconds)', function (): void {
    $picker = DateTimePicker::make('dt');

    expect($picker->getInternalFormat())->toBe('Y-m-d H:i:s');
});

it('returns date-only format when native and time is disabled', function (): void {
    $picker = DateTimePicker::make('dt')
        ->time(false);

    expect($picker->getInternalFormat())->toBe('Y-m-d');
});

it('returns time-only format without seconds when native, date disabled and seconds disabled', function (): void {
    $picker = DateTimePicker::make('dt')
        ->date(false)
        ->seconds(false);

    expect($picker->getInternalFormat())->toBe('H:i');
});

it('returns time-only format with seconds when native and date disabled', function (): void {
    $picker = DateTimePicker::make('dt')
        ->date(false); // seconds enabled by default

    expect($picker->getInternalFormat())->toBe('H:i:s');
});

it('returns datetime format without seconds when native and seconds are disabled', function (): void {
    $picker = DateTimePicker::make('dt')
        ->seconds(false);

    expect($picker->getInternalFormat())->toBe('Y-m-d H:i');
});

it('returns full datetime format for non-native pickers regardless of other flags', function (): void {
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

    $pickerNeg = DateTimePicker::make('dt')
        ->firstDayOfWeek(-1);

    expect($pickerNeg->getFirstDayOfWeek())->toBe(1);
});

it('can set `defaultFocusedDate()`', function (): void {
    $picker = DateTimePicker::make('dt');

    expect($picker->getDefaultFocusedDate())->toBeNull();

    $picker->defaultFocusedDate('2025-06-15');

    expect($picker->getDefaultFocusedDate())->not->toBeNull();
});

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

describe('validation rule closures', function (): void {
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

it('can render `DateTimePicker` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/date-time-picker-test')
            ->assertSee('Test DateTimePicker')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/date-time-picker-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
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
