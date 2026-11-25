<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\DateRangePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DateRangePickerBrowserTest extends Page
{
    protected string $view = 'pages.date-range-picker-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalendarDateRange;

    protected static ?int $navigationSort = 36;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public array $saved = [];

    public int $saveCount = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DateRangePicker::make('period')
                    ->label('Reporting period')
                    ->defaultFocusedDate('2025-07-01')
                    ->presets([
                        'Launch week' => ['2025-07-14', '2025-07-20'],
                    ])
                    ->extraTriggerAttributes(['data-testid' => 'period']),
                DateRangePicker::make('stay')
                    ->label('Stay')
                    ->defaultFocusedDate('2025-07-01')
                    ->minDate('2025-07-10')
                    ->maxDate('2025-07-25')
                    ->maxDays(7)
                    ->disabledDates(['2025-07-20'])
                    ->closeOnDateSelection()
                    ->presets([
                        'Whole season' => ['2025-07-10', '2025-07-25'],
                        'First weekend' => ['2025-07-12', '2025-07-13'],
                    ])
                    ->extraTriggerAttributes(['data-testid' => 'stay']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState();
        $this->saveCount++;
    }
}
