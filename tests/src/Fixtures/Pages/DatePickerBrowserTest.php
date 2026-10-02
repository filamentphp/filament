<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DatePickerBrowserTest extends Page
{
    protected string $view = 'pages.date-picker-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?int $navigationSort = 31;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public array $saved = [];

    public int $saveCount = 0;

    public int $reloadCount = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Native date')
                    ->timezone('Asia/Tokyo')
                    ->minDate(Carbon::parse('2025-07-15 09:30:23', 'Asia/Tokyo'))
                    ->maxDate(static fn () => CarbonImmutable::parse('2025-07-17 17:45:47', 'America/New_York'))
                    ->extraInputAttributes(['data-testid' => 'native-date']),
            ])
            ->statePath('data');
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
