<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TabsBrowserTest extends Page
{
    protected string $view = 'pages.tabs-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?int $navigationSort = 15;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Profile Tabs')
                    ->tabs([
                        Tab::make('Account')
                            ->schema([
                                TextInput::make('username')
                                    ->label('Username')
                                    ->required(),
                            ]),

                        Tab::make('Contact')
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
