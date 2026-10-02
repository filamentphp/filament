<?php

namespace Filament\Tests\Forms\Components;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can render', function (): void {
    livewire(TestComponentWithDatePicker::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithDatePicker::class)
        ->fillForm(['date' => '2024-01-15'])
        ->assertSchemaStateSet(['date' => '2024-01-15']);
});

it('can render with min and max date', function (): void {
    livewire(TestComponentWithDatePickerMinMax::class)
        ->assertSuccessful();
});

it('accepts the entire boundary dates and rejects adjacent dates when `hasTime()` is `false`', function (string $pickerClass, string $source, bool $isNative): void {
    config(['app.timezone' => 'Europe/London']);

    $livewire = livewire(TestComponentWithDatePickerMinMax::class, compact('pickerClass', 'source', 'isNative'));

    if (! $isNative) {
        $livewire->assertSeeHtml('x-ref="minDate" type="hidden" value="2025-03-30 00:00:00"')
            ->assertSeeHtml('x-ref="maxDate" type="hidden" value="2025-03-31 23:59:59"');
    }

    foreach ([
        ['2025-03-29 23:59:59', null],
        ['2025-03-30 00:00:00', '30/03/2025'],
        ['2025-03-31 23:59:59', '31/03/2025'],
        ['2025-04-01 00:00:00', null],
    ] as [$state, $expectedSavedDate]) {
        $livewire->set('data.date', $isNative ? substr($state, 0, 10) : $state)
            ->call('save');

        if ($expectedSavedDate === null) {
            $livewire->assertHasFormErrors(['date']);

            continue;
        }

        $livewire->assertHasNoFormErrors()
            ->assertSet('saved.date', $expectedSavedDate)
            ->call('reloadForm')
            ->assertSet('data.date', static fn (string $reloadedState): bool => substr($reloadedState, 0, 10) === substr($state, 0, 10));
    }
})->with([
    'date picker' => [DatePicker::class],
    'date-time picker without time' => [DateTimePicker::class],
])->with(['string', Carbon::class, CarbonImmutable::class])
    ->with([true, false]);

it('ignores the time of a date-only limit in a timezone that skips midnight', function (string $pickerClass, bool $isNative): void {
    config(['app.timezone' => 'UTC']);

    $livewire = livewire(TestComponentWithDatePickerMinMax::class, [
        'pickerClass' => $pickerClass,
        'isNative' => $isNative,
        'minimumDate' => '2025-09-07 12:00:00 America/Santiago',
        'maximumDate' => '2025-09-09 16:45:47 America/Santiago',
    ]);

    $livewire->set('data.date', $isNative ? '2025-09-07' : '2025-09-07 00:00:00')
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSet('saved.date', '07/09/2025')
        ->call('reloadForm')
        ->assertSet('data.date', static fn (string $reloadedState): bool => substr($reloadedState, 0, 10) === '2025-09-07')
        ->set('data.date', $isNative ? '2025-09-06' : '2025-09-06 23:59:59')
        ->call('save')
        ->assertHasFormErrors(['date']);

    if (! $isNative) {
        $livewire->assertSeeHtml('x-ref="minDate" type="hidden" value="2025-09-07 00:00:00"')
            ->assertSeeHtml('x-ref="maxDate" type="hidden" value="2025-09-09 23:59:59"');
    }
})->with([[DatePicker::class], [DateTimePicker::class]])->with([true, false]);

it('can reset date-only bounds to `null`', function (bool $isNative): void {
    livewire(TestComponentWithDatePickerMinMax::class, ['isNative' => $isNative, 'resetLimits' => true])
        ->set('data.date', '2025-03-29')
        ->call('save')
        ->assertHasNoFormErrors()
        ->set('data.date', '2025-04-01')
        ->call('save')
        ->assertHasNoFormErrors();
})->with([true, false]);

it('ignores explicit and global timezones when loading, saving, and reloading date-only fields', function (string $pickerClass, bool $isNative, ?string $timezone): void {
    config(['app.timezone' => 'Europe/London']);
    FilamentTimezone::set('Pacific/Honolulu');

    foreach (['00:15:23', '23:45:47'] as $time) {
        $this->travelTo(Carbon::parse("2025-03-30 {$time}", 'UTC'));

        livewire(TestComponentWithDatePickerMinMax::class, [
            'pickerClass' => $pickerClass,
            'isNative' => $isNative,
            'timezone' => $timezone,
        ])
            ->set('saved.date', '30/03/2025')
            ->call('reloadForm')
            ->assertSet('data.date', $isNative ? '2025-03-30' : '2025-03-30 00:00:00')
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.date', '30/03/2025')
            ->call('reloadForm')
            ->assertSet('data.date', $isNative ? '2025-03-30' : '2025-03-30 00:00:00')
            ->call('save')
            ->assertSet('saved.date', '30/03/2025');
    }
})->with([[DatePicker::class], [DateTimePicker::class]])
    ->with([true, false])
    ->with([null, 'Asia/Tokyo', 'America/Los_Angeles']);

it('preserves date-only focused dates without timezone conversion or an implicit clock', function (string $source): void {
    config(['app.timezone' => 'Europe/London']);
    $this->travelTo(Carbon::parse('2025-07-20 23:45:47', 'UTC'));

    $date = $source === 'string' ? '15/07/2025' : $source::parse('2025-07-15 00:00:00', 'Asia/Tokyo');
    $picker = DatePicker::make('date')
        ->native(false)
        ->format('d/m/Y')
        ->timezone('America/Los_Angeles')
        ->defaultFocusedDate(static fn () => $date);

    expect($picker->getDefaultFocusedDate())->toBe('2025-07-15 00:00:00');

    if ($source !== 'string') {
        expect($date->format('Y-m-d H:i:s e'))->toBe('2025-07-15 00:00:00 Asia/Tokyo');
    }

    expect($picker->defaultFocusedDate('not a date')->getDefaultFocusedDate())->toBeNull()
        ->and($picker->defaultFocusedDate(null)->getDefaultFocusedDate())->toBeNull();
})->with(['string', Carbon::class, CarbonImmutable::class]);

it('preserves the app year for a partial `defaultFocusedDate()` format', function (): void {
    config(['app.timezone' => 'Pacific/Honolulu']);
    $this->travelTo(Carbon::parse('2025-01-01 00:30:47', 'UTC'));

    expect(DatePicker::make('birthday')
        ->format('m-d')
        ->timezone('Asia/Tokyo')
        ->defaultFocusedDate('02-29')
        ->getDefaultFocusedDate())->toBe('2024-02-29 00:00:00');
});

describe('`hasTime()` override', function (): void {
    it('returns `false` for `hasTime()`', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->hasTime())->toBeFalse();
    });

    it('returns `true` for `hasDate()`', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->hasDate())->toBeTrue();
    });
});

describe('computed formats', function (): void {
    it('returns date-only format `Y-m-d` from `getFormat()` by default', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getFormat())->toBe('Y-m-d');
    });

    it('returns `date` from `getType()`', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getType())->toBe('date');
    });

    it('returns default date display format from `getDisplayFormat()`', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getDisplayFormat())->toBe('M j, Y');
    });

    it('can override `displayFormat()`', function (): void {
        $picker = DatePicker::make('date')
            ->displayFormat('d/m/Y');

        expect($picker->getDisplayFormat())->toBe('d/m/Y');
    });

    it('can override `displayFormat()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->displayFormat(static fn (): string => 'Y.m.d');

        expect($picker->getDisplayFormat())->toBe('Y.m.d');
    });

    it('returns `null` from `getStep()` since no time', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getStep())->toBeNull();
    });

    it('can use custom `format()`', function (): void {
        $picker = DatePicker::make('date')
            ->format('d-m-Y');

        expect($picker->getFormat())->toBe('d-m-Y');
    });

    it('can use custom `format()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->format(static fn (): string => 'm/d/Y');

        expect($picker->getFormat())->toBe('m/d/Y');
    });

    it('returns `Y-m-d` from `getInternalFormat()` when native', function (): void {
        $picker = DatePicker::make('date')->native();

        expect($picker->getInternalFormat())->toBe('Y-m-d');
    });
});

describe('date constraints', function (): void {
    it('returns `null` for `getMinDate()` by default', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getMinDate())->toBeNull();
    });

    it('can set `minDate()`', function (): void {
        $picker = DatePicker::make('date')
            ->minDate('2024-01-01');

        expect($picker->getMinDate())->toBe('2024-01-01 00:00:00');
    });

    it('can set `minDate()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->minDate(static fn (): string => '2024-06-01');

        expect($picker->getMinDate())->toBe('2024-06-01 00:00:00');
    });

    it('returns `null` for `getMaxDate()` by default', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getMaxDate())->toBeNull();
    });

    it('can set `maxDate()`', function (): void {
        $picker = DatePicker::make('date')
            ->maxDate('2024-12-31');

        expect($picker->getMaxDate())->toBe('2024-12-31 23:59:59');
    });

    it('can set `maxDate()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->maxDate(static fn (): string => '2025-01-01');

        expect($picker->getMaxDate())->toBe('2025-01-01 23:59:59');
    });

    it('returns empty array for `getDisabledDates()` by default', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getDisabledDates())->toBe([]);
    });

    it('can set `disabledDates()`', function (): void {
        $picker = DatePicker::make('date')
            ->disabledDates(['2024-12-25', '2024-01-01']);

        expect($picker->getDisabledDates())->toBe(['2024-12-25', '2024-01-01']);
    });

    it('can set `disabledDates()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->disabledDates(static fn (): array => ['2024-07-04']);

        expect($picker->getDisabledDates())->toBe(['2024-07-04']);
    });
});

describe('first day of week', function (): void {
    it('defaults `getFirstDayOfWeek()` to `1` (Monday)', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getFirstDayOfWeek())->toBe(1);
    });

    it('can set `firstDayOfWeek()`', function (): void {
        $picker = DatePicker::make('date')
            ->firstDayOfWeek(0);

        expect($picker->getFirstDayOfWeek())->toBe(0);
    });

    it('clamps out-of-range values to `null` in `firstDayOfWeek()`', function (): void {
        $picker = DatePicker::make('date')
            ->firstDayOfWeek(8);

        expect($picker->getFirstDayOfWeek())->toBe(1);
    });

    it('clamps negative values to `null` in `firstDayOfWeek()`', function (): void {
        $picker = DatePicker::make('date')
            ->firstDayOfWeek(-1);

        expect($picker->getFirstDayOfWeek())->toBe(1);
    });

    it('can use `weekStartsOnMonday()` shortcut', function (): void {
        $picker = DatePicker::make('date')
            ->weekStartsOnMonday();

        expect($picker->getFirstDayOfWeek())->toBe(1);
    });

    it('can use `weekStartsOnSunday()` shortcut', function (): void {
        $picker = DatePicker::make('date')
            ->weekStartsOnSunday();

        expect($picker->getFirstDayOfWeek())->toBe(7);
    });
});

describe('close on date selection', function (): void {
    it('defaults `shouldCloseOnDateSelection()` to `false`', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->shouldCloseOnDateSelection())->toBeFalse();
    });

    it('can set `closeOnDateSelection()`', function (): void {
        $picker = DatePicker::make('date')
            ->closeOnDateSelection();

        expect($picker->shouldCloseOnDateSelection())->toBeTrue();
    });

    it('can set `closeOnDateSelection()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->closeOnDateSelection(static fn (): bool => true);

        expect($picker->shouldCloseOnDateSelection())->toBeTrue();
    });
});

describe('locale', function (): void {
    it('returns app locale for `getLocale()` by default', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getLocale())->toBe(config('app.locale'));
    });

    it('can set `locale()`', function (): void {
        $picker = DatePicker::make('date')
            ->locale('fr');

        expect($picker->getLocale())->toBe('fr');
    });

    it('can set `locale()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->locale(static fn (): string => 'de');

        expect($picker->getLocale())->toBe('de');
    });
});

describe('timezone', function (): void {
    it('returns app timezone for `getTimezone()` when `hasTime()` is false', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getTimezone())->toBe(config('app.timezone'));
    });

    it('can set `timezone()`', function (): void {
        $picker = DatePicker::make('date')
            ->timezone('America/New_York');

        expect($picker->getTimezone())->toBe('America/New_York');
    });
});

describe('native', function (): void {
    it('defaults `isNative()` to `true`', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->isNative())->toBeTrue();
    });

    it('can set `native()` to `false`', function (): void {
        $picker = DatePicker::make('date')
            ->native(false);

        expect($picker->isNative())->toBeFalse();
    });
});

describe('rendering', function (): void {
    it('can render with `displayFormat()`', function (): void {
        livewire(RenderDatePickerWithDisplayFormat::class)
            ->assertSuccessful();
    });

    it('can render with `displayFormat()` set via `Closure`', function (): void {
        livewire(RenderDatePickerWithClosureDisplayFormat::class)
            ->assertSuccessful();
    });

    it('can render with custom `format()`', function (): void {
        livewire(RenderDatePickerWithCustomFormat::class)
            ->assertSuccessful();
    });

    it('can render with `format()` set via `Closure`', function (): void {
        livewire(RenderDatePickerWithClosureFormat::class)
            ->assertSuccessful();
    });

    it('can render with `minDate()`', function (): void {
        livewire(RenderDatePickerWithMinDate::class)
            ->assertSuccessful();
    });

    it('can render with `minDate()` set via `Closure`', function (): void {
        livewire(RenderDatePickerWithClosureMinDate::class)
            ->assertSuccessful();
    });

    it('can render with `maxDate()`', function (): void {
        livewire(RenderDatePickerWithMaxDate::class)
            ->assertSuccessful();
    });

    it('can render with `maxDate()` set via `Closure`', function (): void {
        livewire(RenderDatePickerWithClosureMaxDate::class)
            ->assertSuccessful();
    });

    it('can render with `disabledDates()`', function (): void {
        livewire(RenderDatePickerWithDisabledDates::class)
            ->assertSuccessful();
    });

    it('can render with `disabledDates()` set via `Closure`', function (): void {
        livewire(RenderDatePickerWithClosureDisabledDates::class)
            ->assertSuccessful();
    });

    it('can render with `firstDayOfWeek()`', function (): void {
        livewire(RenderDatePickerWithFirstDayOfWeek::class)
            ->assertSuccessful();
    });

    it('can render with `weekStartsOnSunday()`', function (): void {
        livewire(RenderDatePickerWithWeekStartsOnSunday::class)
            ->assertSuccessful();
    });

    it('can render with `closeOnDateSelection()`', function (): void {
        livewire(RenderDatePickerWithCloseOnDateSelection::class)
            ->assertSuccessful();
    });

    it('can render with `closeOnDateSelection()` set via `Closure`', function (): void {
        livewire(RenderDatePickerWithClosureCloseOnDateSelection::class)
            ->assertSuccessful();
    });

    it('can render with `locale()`', function (): void {
        livewire(RenderDatePickerWithLocale::class)
            ->assertSuccessful();
    });

    it('can render with `locale()` set via `Closure`', function (): void {
        livewire(RenderDatePickerWithClosureLocale::class)
            ->assertSuccessful();
    });

    it('can render with `native(false)`', function (): void {
        livewire(RenderDatePickerWithNonNative::class)
            ->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        livewire(RenderDatePickerWithPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('Select a date...');
    });
});

it('selects and saves full boundary dates in a native picker with accessible light and dark states', function (): void {
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $page = visit('/date-picker-browser-test');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page->script('new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))).then(() => Promise.all(document.getAnimations().filter(animation => animation.effect.getComputedTiming().iterations !== Infinity).map(animation => animation.finished.catch(() => {}))))');
        $page->assertNoSmoke()->assertNoAccessibilityIssues();

        $reloadCount = 0;

        foreach ([
            ['2025-07-14', false, 0],
            ['2025-07-15', true, 1],
            ['2025-07-17', true, 2],
            ['2025-07-18', false, 2],
        ] as [$date, $isValid, $saveCount]) {
            $page->script("(() => { const input = document.querySelector('[data-testid=\"native-date\"]'); input.oninvalid = () => { input.dataset.rejected = 'true'; }; input.dataset.rejected = 'false'; input.value = '{$date}'; input.dispatchEvent(new Event('input', { bubbles: true })); })()");

            $page->assertScript('document.querySelector(\'[data-testid="native-date"]\').validity.valid', $isValid)
                ->click('[data-testid="save-dates"]');

            if (! $isValid) {
                $page->assertScript('document.querySelector(\'[data-testid="native-date"]\').dataset.rejected', 'true');
            }

            $page->assertScript('document.querySelector(\'[data-testid="save-count"]\').textContent.trim()', (string) $saveCount);

            if ($isValid) {
                $page->assertScript('document.querySelector(\'[data-testid="saved-native-date"]\').textContent.trim()', $date)
                    ->click('[data-testid="reload-dates"]')
                    ->assertScript('document.querySelector(\'[data-testid="save-count"]\').dataset.reloadCount', (string) ++$reloadCount)
                    ->assertValue('[data-testid="native-date"]', $date);
            }
        }

        $page->click('[data-testid="reload-dates"]')
            ->assertScript('document.querySelector(\'[data-testid="save-count"]\').dataset.reloadCount', (string) ++$reloadCount)
            ->assertValue('[data-testid="native-date"]', '2025-07-17')
            ->assertScript('document.querySelector(\'[data-testid="reload-dates"]\').disabled', false);

        $page->script('new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))).then(() => Promise.all(document.getAnimations().filter(animation => animation.effect.getComputedTiming().iterations !== Infinity).map(animation => animation.finished.catch(() => {}))))');
        $page->assertNoAccessibilityIssues();
    }
});

class TestComponentWithDatePicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithDatePickerMinMax extends Livewire
{
    public string $pickerClass = DatePicker::class;

    public string $source = 'string';

    public string $minimumDate = '2025-03-30 09:30:23';

    public string $maximumDate = '2025-03-31 17:45:47';

    public bool $isNative = true;

    public bool $resetLimits = false;

    public ?string $timezone = null;

    public array $saved = [];

    public function form(Schema $form): Schema
    {
        $minimum = $this->source === 'string' ? $this->minimumDate : $this->source::parse($this->minimumDate, 'Asia/Tokyo');
        $maximum = $this->source === 'string' ? $this->maximumDate : $this->source::parse($this->maximumDate, 'America/New_York');
        $field = $this->pickerClass::make('date')
            ->time(false)
            ->native($this->isNative)
            ->format('d/m/Y')
            ->minDate($minimum)
            ->maxDate(static fn () => $maximum);

        if ($this->resetLimits) {
            $field->minDate(null)->maxDate(static fn () => null);
        }

        if ($this->timezone !== null) {
            $field->timezone(fn (): string => $this->timezone);
        }

        return $form
            ->schema([$field])
            ->statePath('data');
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

class RenderDatePickerWithDisplayFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->displayFormat('d/m/Y'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithClosureDisplayFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->displayFormat(static fn (): string => 'Y.m.d'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithCustomFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->format('d-m-Y'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithClosureFormat extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->format(static fn (): string => 'm/d/Y'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithMinDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->minDate('2024-01-01'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithClosureMinDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->minDate(static fn (): string => '2024-06-01'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithMaxDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->maxDate('2024-12-31'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithClosureMaxDate extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->maxDate(static fn (): string => '2025-01-01'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithDisabledDates extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->disabledDates(['2024-12-25', '2024-01-01']),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithClosureDisabledDates extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->disabledDates(static fn (): array => ['2024-07-04']),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithFirstDayOfWeek extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->firstDayOfWeek(0),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithWeekStartsOnSunday extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->weekStartsOnSunday(),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithCloseOnDateSelection extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->closeOnDateSelection(),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithClosureCloseOnDateSelection extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->closeOnDateSelection(static fn (): bool => true),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithLocale extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->locale('fr'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithClosureLocale extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->locale(static fn (): string => 'de'),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithNonNative extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->native(false),
            ])
            ->statePath('data');
    }
}

class RenderDatePickerWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->placeholder('Select a date...'),
            ])
            ->statePath('data');
    }
}
