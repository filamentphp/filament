<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\PhoneInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PhoneInputBrowserTest extends Page
{
    protected string $view = 'pages.phone-input-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?int $navigationSort = 36;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public array $saved = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                PhoneInput::make('phone')
                    ->label('Phone')
                    ->defaultCountry('BR')
                    ->preferredCountries(['BR', 'US', 'PT'])
                    ->extraInputAttributes(['data-testid' => 'phone']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState();
    }
}
