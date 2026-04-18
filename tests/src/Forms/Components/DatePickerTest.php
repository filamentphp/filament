<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\DatePicker;
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

        expect($picker->getMinDate())->toBe('2024-01-01');
    });

    it('can set `minDate()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->minDate(static fn (): string => '2024-06-01');

        expect($picker->getMinDate())->toBe('2024-06-01');
    });

    it('returns `null` for `getMaxDate()` by default', function (): void {
        $picker = DatePicker::make('date');

        expect($picker->getMaxDate())->toBeNull();
    });

    it('can set `maxDate()`', function (): void {
        $picker = DatePicker::make('date')
            ->maxDate('2024-12-31');

        expect($picker->getMaxDate())->toBe('2024-12-31');
    });

    it('can set `maxDate()` with a `Closure`', function (): void {
        $picker = DatePicker::make('date')
            ->maxDate(static fn (): string => '2025-01-01');

        expect($picker->getMaxDate())->toBe('2025-01-01');
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

it('has no accessibility issues in light mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/date-picker-browser-test')
            ->assertSee('Test Date Picker')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    });
});

it('has no accessibility issues in dark mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/date-picker-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
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
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->minDate(now()->subYear())
                    ->maxDate(now()->addYear()),
            ])
            ->statePath('data');
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
