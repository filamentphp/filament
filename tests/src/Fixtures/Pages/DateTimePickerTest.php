<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DateTimePickerTest extends Page
{
    protected string $view = 'pages.date-time-picker-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?int $navigationSort = 9;

    public ?array $data = [];

    public bool $hasDate = true;

    public bool $hasTime = true;

    public bool $isDisabled = false;

    public bool $isReadOnly = false;

    public bool $isAutofocused = false;

    public bool $hasSeconds = true;

    public bool $hasLimits = false;

    public bool $isNative = true;

    public bool $hasDstLimits = false;

    public bool $hasAppDstGap = false;

    public ?string $calendarState = null;

    public ?string $displayFormat = null;

    public bool $hasZonedDisabledDate = false;

    public bool $hasDisabledDates = false;

    public string $calendarLocale = 'en';

    public int $calendarWeekStart = 1;

    public bool $overlaysParentCalendarModal = false;

    public bool $closesOnDateSelection = false;

    public int $hourStep = 1;

    public int $minuteStep = 1;

    public array $saved = [];

    public int $saveCount = 0;

    public int $reloadCount = 0;

    public function mount(): void
    {
        $this->hasDate = request()->boolean('date', true);
        $this->hasTime = request()->boolean('time', true);
        $this->isDisabled = request()->boolean('disabled');
        $this->isReadOnly = request()->boolean('readonly');
        $this->isAutofocused = request()->boolean('autofocus');
        $this->hasSeconds = request()->boolean('seconds', true);
        $this->hasLimits = request()->boolean('bounds');
        $this->isNative = request()->boolean('native', true);
        $this->hasDstLimits = request()->boolean('dst');
        $this->hasAppDstGap = request()->boolean('app-dst-gap');
        $this->calendarState = request()->query('calendar-state');
        $this->displayFormat = request()->query('display-format');
        $this->hasZonedDisabledDate = request()->boolean('zoned-disabled-date');
        $this->hasDisabledDates = request()->boolean('disabled-dates');
        $this->calendarLocale = request()->string('locale', 'en')->toString();
        $this->calendarWeekStart = request()->integer('week-start', 1);
        $this->overlaysParentCalendarModal = request()->boolean('overlay');
        $this->closesOnDateSelection = request()->boolean('close-on-selection');
        $this->hourStep = request()->integer('hour-step', 1);
        $this->minuteStep = request()->integer('minute-step', 1);
        $this->form->fill($this->calendarState !== null ? ['field' => $this->calendarState] : ($this->hasDstLimits ? ['field' => '01:45:07'] : []));
    }

    public function form(Schema $form): Schema
    {
        $field = ($this->hasDate ? DateTimePicker::make('field') : TimePicker::make('field'))
            ->label('Test DateTimePicker')
            ->native($this->isNative)
            ->time($this->hasTime)
            ->disabled($this->isDisabled)
            ->readOnly($this->isReadOnly)
            ->autofocus($this->isAutofocused)
            ->locale($this->calendarLocale)
            ->firstDayOfWeek($this->calendarWeekStart)
            ->closeOnDateSelection($this->closesOnDateSelection)
            ->hoursStep($this->hourStep)
            ->minutesStep($this->minuteStep)
            ->disabledDates($this->hasDisabledDates ? ['2025-07-16'] : [])
            ->defaultFocusedDate('2025-07-15 13:24:37')
            ->placeholder('Choose a date or time')
            ->seconds($this->hasSeconds)
            ->displayFormat($this->displayFormat)
            ->extraAttributes(['data-testid' => 'date-time-picker'])
            ->extraInputAttributes(['data-testid' => 'timed-input'])
            ->extraTriggerAttributes(['data-testid' => 'timed-trigger']);

        if ($this->hasLimits) {
            $field->timezone('Asia/Kathmandu')
                ->minDate(Carbon::parse('2025-07-15 00:30:23', 'Asia/Tokyo'))
                ->maxDate(static fn () => CarbonImmutable::parse('2025-07-17 17:45:47', 'America/Los_Angeles'));
        }

        if ($this->hasDstLimits) {
            $field->timezone('UTC')
                ->format('H:i:s')
                ->minDate('2025-03-30 01:30:23')
                ->maxDate('2025-10-26 01:55:47');
        }

        if ($this->hasAppDstGap) {
            $field->timezone('America/New_York');
        }

        if ($this->calendarState !== null) {
            $date = Carbon::parse($this->calendarState, 'UTC');
            $field->timezone('UTC')
                ->defaultFocusedDate($this->calendarState)
                ->minDate($date->copy()->startOfDay()->toDateTimeString())
                ->maxDate($date->copy()->addDays(2)->endOfDay()->toDateTimeString())
                ->disabledDates([$date->copy()->addDay()->toDateString()]);
        }

        if ($this->hasZonedDisabledDate) {
            $field->disabledDates(['2025-07-15T00:00:00Z']);
        }

        return $form
            ->schema([$field])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calendarModal')
                ->schema([
                    DateTimePicker::make('parentDate')->native(false),
                ])
                ->modalSubmitAction(false)
                ->extraAttributes(['data-testid' => 'calendar-modal-trigger'])
                ->extraModalWindowAttributes(['data-testid' => 'calendar-modal'])
                ->extraModalFooterActions([
                    Action::make('nestedCalendarModal')
                        ->schema([
                            DateTimePicker::make('nestedDate')
                                ->native(false)
                                ->extraTriggerAttributes(['data-testid' => 'nested-calendar-trigger']),
                        ])
                        ->overlayParentActions($this->overlaysParentCalendarModal)
                        ->modalSubmitAction(false)
                        ->extraAttributes(['data-testid' => 'nested-calendar-modal-trigger'])
                        ->extraModalWindowAttributes(['data-testid' => 'nested-calendar-modal']),
                ]),
        ];
    }

    public function save(): void
    {
        $this->saved = $this->form->getState();
        $this->saveCount++;
    }

    public function reloadForm(): void
    {
        $this->form->fill($this->saved);
        $this->reloadCount++;
    }
}
