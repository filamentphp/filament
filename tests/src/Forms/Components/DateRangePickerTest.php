<?php

use Carbon\CarbonImmutable;
use Filament\Forms\Components\DateRangePicker;
use Filament\Forms\Components\DateRangePicker\Enums\Preset;
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

describe('configuration', function (): void {
    it('returns `Y-m-d` from `getFormat()` by default', function (): void {
        expect(DateRangePicker::make('period')->getFormat())->toBe('Y-m-d');
    });

    it('can set `format()` with a `Closure`', function (): void {
        expect(DateRangePicker::make('period')->format(fn (): string => 'd/m/Y')->getFormat())->toBe('d/m/Y');
    });

    it('returns the default date display format from `getDisplayFormat()`', function (): void {
        expect(DateRangePicker::make('period')->getDisplayFormat())->toBe('M j, Y')
            ->and(DateRangePicker::make('period')->defaultDateDisplayFormat('d.m.Y')->getDisplayFormat())->toBe('d.m.Y')
            ->and(DateRangePicker::make('period')->displayFormat(fn (): string => 'd/m/Y')->getDisplayFormat())->toBe('d/m/Y');
    });

    it('returns `null` for `getMinDate()`, `getMaxDate()` and `getMaxDays()` by default', function (): void {
        $picker = DateRangePicker::make('period');

        expect($picker->getMinDate())->toBeNull()
            ->and($picker->getMaxDate())->toBeNull()
            ->and($picker->getMaxDays())->toBeNull();
    });

    it('returns calendar dates from `getMinDate()` and `getMaxDate()`', function (): void {
        $picker = DateRangePicker::make('period')
            ->minDate('2026-01-01 18:00:00')
            ->maxDate(fn (): CarbonImmutable => CarbonImmutable::parse('2026-12-31 23:00:00'));

        expect($picker->getMinDate())->toBe('2026-01-01')
            ->and($picker->getMaxDate())->toBe('2026-12-31');
    });

    it('can set `maxDays()` with a `Closure`', function (): void {
        expect(DateRangePicker::make('period')->maxDays(fn (): int => 31)->getMaxDays())->toBe(31);
    });

    it('can set `disabledDates()` with a `Closure`', function (): void {
        expect(DateRangePicker::make('period')->disabledDates(fn (): array => ['2026-10-12'])->getDisabledDates())->toBe(['2026-10-12']);
    });

    it('defaults `getFirstDayOfWeek()` to `1` and falls back to it for out-of-range values', function (): void {
        expect(DateRangePicker::make('period')->getFirstDayOfWeek())->toBe(1)
            ->and(DateRangePicker::make('period')->firstDayOfWeek(8)->getFirstDayOfWeek())->toBe(1)
            ->and(DateRangePicker::make('period')->weekStartsOnSunday()->getFirstDayOfWeek())->toBe(7)
            ->and(DateRangePicker::make('period')->weekStartsOnSunday()->resetFirstDayOfWeek()->getFirstDayOfWeek())->toBe(1);
    });

    it('can set `closeOnDateSelection()` with a `Closure`', function (): void {
        expect(DateRangePicker::make('period')->shouldCloseOnDateSelection())->toBeFalse()
            ->and(DateRangePicker::make('period')->closeOnDateSelection(fn (): bool => true)->shouldCloseOnDateSelection())->toBeTrue();
    });

    it('returns the app locale from `getLocale()` by default', function (): void {
        expect(DateRangePicker::make('period')->getLocale())->toBe(config('app.locale'))
            ->and(DateRangePicker::make('period')->locale(fn (): string => 'pt_BR')->getLocale())->toBe('pt_BR');
    });

    it('returns `defaultFocusedDate()` in the internal format', function (): void {
        expect(DateRangePicker::make('period')->format('d/m/Y')->defaultFocusedDate('15/01/2027')->getDefaultFocusedDate())->toBe('2027-01-15');
    });
});

describe('presets', function (): void {
    it('returns no presets from `getPresets()` by default', function (): void {
        expect(DateRangePicker::make('period')->getPresets())->toBe([])
            ->and(DateRangePicker::make('period')->presets(false)->getPresets())->toBe([]);
    });

    it('returns every `Preset` from `getPresets()` when `presets()` is `true`', function (): void {
        expect(DateRangePicker::make('period')->presets()->getPresets())->toBe(Preset::cases());
    });

    it('can set `presets()` with a `Closure`', function (): void {
        expect(DateRangePicker::make('period')->presets(fn (): array => [Preset::Today, Preset::MonthToDate])->getPresets())
            ->toBe([Preset::Today, Preset::MonthToDate]);
    });

    it('resolves the dates of custom presets in order', function (): void {
        $this->travelTo(CarbonImmutable::parse('2026-11-25 12:00:00'));

        expect(DateRangePicker::make('period')->presets([
            Preset::Today,
            'Black Friday week' => ['2026-11-23', CarbonImmutable::parse('2026-11-29 23:59:59')],
            'Previous quarter' => [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()],
            'Since Monday' => ['monday this week', 'today'],
            'Reversed' => ['2026-12-31', '2026-12-01'],
        ])->getPresets())->toBe([
            0 => Preset::Today,
            'Black Friday week' => ['2026-11-23', '2026-11-29'],
            'Previous quarter' => ['2026-07-01', '2026-09-30'],
            'Since Monday' => ['2026-11-23', '2026-11-25'],
            'Reversed' => ['2026-12-01', '2026-12-31'],
        ]);
    });

    it('translates `Preset` labels', function (): void {
        expect(Preset::PastSevenDays->getLabel())->toBe('Past 7 days');

        app()->setLocale('pt_BR');

        expect(Preset::PastSevenDays->getLabel())->toBe('Últimos 7 dias');
    });
});

describe('state', function (): void {
    it('can set and get state', function (): void {
        livewire(TestComponentWithDateRangePicker::class)
            ->fillForm(['period' => ['start' => '2026-10-01', 'end' => '2026-10-09']])
            ->assertSchemaStateSet(['period' => ['start' => '2026-10-01', 'end' => '2026-10-09']])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.period', ['start' => '2026-10-01', 'end' => '2026-10-09']);
    });

    it('reads and saves its state in its `format()`', function (): void {
        livewire(TestComponentWithDateRangePicker::class, ['format' => 'd/m/Y', 'initialData' => ['period' => ['start' => '01/10/2026', 'end' => '09/10/2026']]])
            ->assertSet('data.period', ['start' => '2026-10-01', 'end' => '2026-10-09'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.period', ['start' => '01/10/2026', 'end' => '09/10/2026']);
    });

    it('reads dates given as `Carbon` instances', function (): void {
        livewire(TestComponentWithDateRangePicker::class, ['initialData' => ['period' => ['start' => CarbonImmutable::parse('2026-10-01'), 'end' => CarbonImmutable::parse('2026-10-09')]]])
            ->assertSet('data.period', ['start' => '2026-10-01', 'end' => '2026-10-09']);
    });

    it('saves `null` without a complete range', function (?array $state): void {
        livewire(TestComponentWithDateRangePicker::class, ['initialData' => ['period' => $state]])
            ->assertSet('data.period', null)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.period', null);
    })->with([
        'no state' => [null],
        'start only' => [['start' => '2026-10-01', 'end' => null]],
    ]);
});

describe('validation', function (): void {
    it('rejects a range that is not valid', function (array $state): void {
        livewire(TestComponentWithDateRangePicker::class)
            ->set('data.period', $state)
            ->call('save')
            ->assertHasFormErrors(['period' => 'date']);
    })->with([
        'reversed' => [['start' => '2026-10-09', 'end' => '2026-10-01']],
        'impossible date' => [['start' => '2026-02-30', 'end' => '2026-03-01']],
        'not a date' => [['start' => 'yesterday', 'end' => '2026-03-01']],
    ]);

    it('accepts the limits of `minDate()`, `maxDate()` and `maxDays()`', function (): void {
        livewire(TestComponentWithDateRangePicker::class, ['minDate' => '2026-10-01', 'maxDate' => '2026-10-31', 'maxDays' => 31])
            ->set('data.period', ['start' => '2026-10-01', 'end' => '2026-10-31'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertSet('saved.period', ['start' => '2026-10-01', 'end' => '2026-10-31']);
    });

    it('rejects a range outside `minDate()` and `maxDate()` with Laravel\'s messages', function (array $state, string $rule): void {
        livewire(TestComponentWithDateRangePicker::class, ['minDate' => '2026-10-01', 'maxDate' => '2026-10-31'])
            ->set('data.period', $state)
            ->call('save')
            ->assertHasFormErrors(['period' => $rule]);
    })->with([
        'before the minimum' => [['start' => '2026-09-30', 'end' => '2026-10-05'], 'after_or_equal'],
        'after the maximum' => [['start' => '2026-10-25', 'end' => '2026-11-01'], 'before_or_equal'],
    ]);

    it('rejects a range longer than `maxDays()`', function (): void {
        livewire(TestComponentWithDateRangePicker::class, ['maxDays' => 7])
            ->set('data.period', ['start' => '2026-10-01', 'end' => '2026-10-08'])
            ->call('save')
            ->assertHasFormErrors(['period' => 'The period field must not be longer than 7 days.']);
    });

    it('can customize the `maxDays()` validation message', function (): void {
        livewire(TestComponentWithDateRangePicker::class, ['maxDays' => 1, 'maxDaysMessage' => 'Pick one day.'])
            ->set('data.period', ['start' => '2026-10-01', 'end' => '2026-10-02'])
            ->call('save')
            ->assertHasFormErrors(['period' => 'Pick one day.']);
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(TestComponentWithDateRangePicker::class)
            ->assertSuccessful()
            ->assertSeeHtml('dateRangePickerFormComponent(')
            ->assertDontSeeHtml('fi-fo-date-range-picker-presets');
    });

    it('can render with every option', function (): void {
        livewire(RenderDateRangePickerWithEveryOption::class)
            ->assertSuccessful()
            ->assertSeeHtml([
                'maxDays: 14',
                'firstDayOfWeek: 7',
                'shouldCloseOnDateSelection: true',
                'locale: \'pt_BR\'',
                'x-ref="minDate" type="hidden" value="2026-01-01"',
                'x-ref="maxDate" type="hidden" value="2026-12-31"',
                'placeholder="Any time"',
            ]);
    });

    it('can render `Preset` and custom presets', function (): void {
        livewire(RenderDateRangePickerWithPresets::class)
            ->assertSuccessful()
            ->assertSeeHtmlInOrder([
                'selectPreset(\'past_7_days\')',
                'Past 7 days',
                'selectPreset(JSON.parse(\'[\\u00222026-11-23\\u0022,\\u00222026-11-29\\u0022]\'))',
                'Black Friday week',
            ]);
    });

    it('escapes the labels of custom presets', function (): void {
        livewire(RenderDateRangePickerWithPresets::class)
            ->assertDontSeeHtml('<script>alert(1)</script>')
            ->assertSeeHtml('&lt;script&gt;alert(1)&lt;/script&gt;');
    });

    it('cannot open the panel when disabled or read-only', function (string $component): void {
        livewire($component)
            ->assertSuccessful()
            ->assertDontSeeHtml('x-on:click="togglePanelVisibility()"');
    })->with([
        'disabled' => [RenderDisabledDateRangePicker::class],
        'read-only' => [RenderReadOnlyDateRangePicker::class],
    ]);
});

describe('browser interactions', function (): void {
    it('selects a range with the pointer and keyboard, and saves it', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-range-picker-browser-test');

        $panel = '[id="form.period-panel"]';

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[data-testid="period"]')
            ->assertPresent("{$panel} [data-date=\"2025-07-01\"]:focus")
            ->click("{$panel} [data-date=\"2025-07-03\"]")
            ->hover("{$panel} [data-date=\"2025-07-06\"]")
            ->assertAttribute("{$panel} [data-date=\"2025-07-05\"]", 'aria-selected', 'true')
            ->assertAttribute("{$panel} [data-date=\"2025-07-07\"]", 'aria-selected', 'false')
            ->assertPresent("{$panel} [data-date=\"2025-07-03\"]:focus")
            ->click("{$panel} [data-date=\"2025-07-08\"]")
            ->assertAttribute("{$panel} [data-date=\"2025-07-05\"]", 'aria-selected', 'true')
            ->assertAttribute("{$panel} [data-date=\"2025-07-09\"]", 'aria-selected', 'false')
            ->keys("{$panel} [data-date=\"2025-07-08\"]", 'Escape')
            ->assertPresent('[data-testid="period"]:focus')
            ->assertValue('[data-testid="period"]', 'Jul 3, 2025 – Jul 8, 2025');

        $page->keys('[data-testid="period"]', 'Enter')
            ->assertPresent("{$panel} [data-date=\"2025-07-03\"]:focus")
            ->keys("{$panel} [role=\"gridcell\"]:focus", 'ArrowRight')
            ->assertPresent("{$panel} [data-date=\"2025-07-04\"]:focus")
            ->keys("{$panel} [role=\"gridcell\"]:focus", 'Enter')
            ->keys("{$panel} [role=\"gridcell\"]:focus", 'ArrowDown')
            ->assertPresent("{$panel} [data-date=\"2025-07-11\"]:focus")
            ->keys("{$panel} [role=\"gridcell\"]:focus", 'Enter')
            ->keys("{$panel} [role=\"gridcell\"]:focus", 'Escape')
            ->assertValue('[data-testid="period"]', 'Jul 4, 2025 – Jul 11, 2025')
            ->click('[data-testid="save-ranges"]')
            ->assertScript('document.querySelector(\'[data-testid="save-count"]\').textContent.trim()', '1')
            ->assertScript('document.querySelector(\'[data-testid="saved-period"]\').textContent.trim()', '2025-07-04,2025-07-11');
    });

    it('moves the calendar to keep the focused date visible', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-range-picker-browser-test');

        $panel = '[id="form.period-panel"]';

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[data-testid="period"]')
            ->assertPresent("{$panel} [data-date=\"2025-07-01\"]:focus")
            ->keys("{$panel} [role=\"gridcell\"]:focus", 'PageDown')
            ->assertPresent("{$panel} [data-date=\"2025-08-01\"]:focus")
            ->keys("{$panel} [role=\"gridcell\"]:focus", 'PageDown')
            ->assertPresent("{$panel} [data-date=\"2025-09-01\"]:focus")
            ->assertMissing("{$panel} [data-date=\"2025-07-01\"]")
            ->keys("{$panel} [role=\"gridcell\"]:focus", 'Shift+PageUp')
            ->assertPresent("{$panel} [data-date=\"2024-09-01\"]:focus");
    });

    it('stays open while the month and year are changed and its empty space is clicked', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-range-picker-browser-test');

        $panel = '[id="form.period-panel"]';
        $monthSelect = "{$panel} select";
        $yearInput = "{$panel} input[type=\"number\"]";

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[data-testid="period"]')
            ->click($monthSelect)
            ->select($monthSelect, '8')
            ->wait(0.5)
            ->assertVisible($panel)
            ->assertPresent("{$monthSelect}:focus")
            ->assertPresent("{$panel} [data-date=\"2025-09-01\"]")
            ->type($yearInput, '2026')
            ->wait(0.5)
            ->assertVisible($panel)
            ->assertPresent("{$yearInput}:focus")
            ->assertPresent("{$panel} [data-date=\"2026-09-01\"]")
            ->click("{$panel} .fi-fo-date-range-picker-month:last-child .fi-fo-date-range-picker-next-month-btn")
            ->wait(0.5)
            ->assertVisible($panel)
            ->assertPresent("{$panel} [data-date=\"2026-10-01\"]")
            ->click("{$panel} [role=\"group\"]")
            ->wait(0.5)
            ->assertVisible($panel);
    });

    it('selects a preset', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-range-picker-browser-test');

        $panel = '[id="form.period-panel"]';

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[data-testid="period"]')
            ->assertAttribute("{$panel} [role=\"group\"] button", 'aria-pressed', 'false')
            ->click("{$panel} [role=\"group\"] button")
            ->assertAttribute("{$panel} [role=\"group\"] button", 'aria-pressed', 'true')
            ->keys("{$panel} [role=\"group\"] button", 'Escape')
            ->assertPresent('[data-testid="period"]:focus')
            ->assertValue('[data-testid="period"]', 'Jul 14, 2025 – Jul 20, 2025')
            ->click('[data-testid="save-ranges"]')
            ->assertScript('document.querySelector(\'[data-testid="saved-period"]\').textContent.trim()', '2025-07-14,2025-07-20');
    });

    it('disables the dates and presets that the limits refuse', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-range-picker-browser-test');

        $panel = '[id="form.stay-panel"]';

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[data-testid="stay"]')
            ->assertAttribute("{$panel} [data-date=\"2025-07-09\"]", 'aria-disabled', 'true')
            ->assertAttribute("{$panel} [data-date=\"2025-07-20\"]", 'aria-disabled', 'true')
            ->assertAttribute("{$panel} [data-date=\"2025-07-26\"]", 'aria-disabled', 'true')
            ->assertAttribute("{$panel} [data-date=\"2025-07-12\"]", 'aria-disabled', 'false')
            ->assertScript('Array.from(document.querySelectorAll(\'[id="form.stay-panel"] [role="group"] button\')).map((button) => button.disabled).join()', 'true,false')
            ->click("{$panel} [data-date=\"2025-07-12\"]")
            ->assertAttribute("{$panel} [data-date=\"2025-07-18\"]", 'aria-disabled', 'false')
            ->assertAttribute("{$panel} [data-date=\"2025-07-19\"]", 'aria-disabled', 'true')
            ->assertNoAccessibilityIssues();

        $page->inDarkMode()
            ->waitForEvent('networkidle')
            ->click('[data-testid="stay"]')
            ->assertVisible($panel)
            ->assertNoAccessibilityIssues();
    });

    it('closes after a range is chosen with `closeOnDateSelection()`', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/date-range-picker-browser-test');

        $panel = '[id="form.stay-panel"]';

        $page->waitForEvent('networkidle')
            ->assertNoSmoke()
            ->click('[data-testid="stay"]')
            ->click("{$panel} [data-date=\"2025-07-12\"]")
            ->click("{$panel} [data-date=\"2025-07-15\"]")
            ->assertPresent('[data-testid="stay"]:focus')
            ->assertAttribute('[data-testid="stay"]', 'aria-expanded', 'false')
            ->assertValue('[data-testid="stay"]', 'Jul 12, 2025 – Jul 15, 2025');
    });
});

class TestComponentWithDateRangePicker extends Livewire
{
    public string $format = 'Y-m-d';

    public ?string $minDate = null;

    public ?string $maxDate = null;

    public ?int $maxDays = null;

    public ?string $maxDaysMessage = null;

    public array $initialData = [];

    public array $saved = [];

    public function mount(): void
    {
        $this->form->fill($this->initialData);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateRangePicker::make('period')
                    ->format($this->format)
                    ->minDate($this->minDate)
                    ->maxDate($this->maxDate)
                    ->maxDays($this->maxDays)
                    ->validationMessages(array_filter(['max_days' => $this->maxDaysMessage])),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState();
    }
}

class RenderDateRangePickerWithEveryOption extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateRangePicker::make('period')
                    ->displayFormat('d/m/Y')
                    ->minDate('2026-01-01')
                    ->maxDate(fn (): string => '2026-12-31')
                    ->maxDays(14)
                    ->disabledDates(['2026-10-12'])
                    ->defaultFocusedDate('2026-10-01')
                    ->weekStartsOnSunday()
                    ->closeOnDateSelection()
                    ->locale('pt_BR')
                    ->placeholder('Any time'),
            ])
            ->statePath('data');
    }
}

class RenderDateRangePickerWithPresets extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateRangePicker::make('period')
                    ->presets([
                        Preset::PastSevenDays,
                        'Black Friday week' => ['2026-11-23', '2026-11-29'],
                        '<script>alert(1)</script>' => ['2026-12-01', '2026-12-02'],
                    ]),
            ])
            ->statePath('data');
    }
}

class RenderDisabledDateRangePicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateRangePicker::make('period')
                    ->disabled(),
            ])
            ->statePath('data');
    }
}

class RenderReadOnlyDateRangePicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateRangePicker::make('period')
                    ->readOnly(),
            ])
            ->statePath('data');
    }
}
