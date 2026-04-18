<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TimePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TimePickerBrowserTest extends Page
{
    protected string $view = 'pages.time-picker-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?int $navigationSort = 34;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TimePicker::make('time')
                    ->label('Test Time Picker'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
