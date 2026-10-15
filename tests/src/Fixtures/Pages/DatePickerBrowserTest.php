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
        $record = auth()->user()->mergeCasts(['email_verified_at' => 'date']);

        $this->form->fill();
        $this->form->fillPartially($record->attributesToArray(), ['email_verified_at']);
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
                DatePicker::make('timestamp')
                    ->label('Timestamp date')
                    ->format('U')
                    ->timezone('Asia/Tokyo')
                    ->default('1752505200')
                    ->extraInputAttributes(['data-testid' => 'timestamp-date']),
                DatePicker::make('customTimestamp')
                    ->label('Custom timestamp date')
                    ->native(false)
                    ->format('U')
                    ->displayFormat('Y-m-d')
                    ->timezone('Asia/Tokyo')
                    ->default('1752505200')
                    ->extraTriggerAttributes(['data-testid' => 'custom-timestamp-date']),
                DatePicker::make('midnightTimestamp')
                    ->label('Midnight timestamp date')
                    ->format('U')
                    ->timezone('UTC')
                    ->default('1757203200')
                    ->extraInputAttributes(['data-testid' => 'midnight-timestamp-date']),
                DatePicker::make('email_verified_at')
                    ->label('Verified date')
                    ->extraInputAttributes(['data-testid' => 'model-date']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState();
        auth()->user()->mergeCasts(['email_verified_at' => 'date'])
            ->update(['email_verified_at' => $this->saved['email_verified_at']]);
        $this->saveCount++;
    }

    public function reloadForm(): void
    {
        $record = auth()->user()->refresh()->mergeCasts(['email_verified_at' => 'date']);

        $this->form->fill([
            ...$this->saved,
            'email_verified_at' => $record->attributesToArray()['email_verified_at'],
        ]);
        $this->reloadCount++;
    }
}
