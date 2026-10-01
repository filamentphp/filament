<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
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
                    ->key('profile')
                    ->id('profile-tabs')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Account')
                            ->key('account')
                            ->id('profile-account')
                            ->badge('Available')
                            ->badgeIcon(Heroicon::OutlinedCheckCircle)
                            ->schema([
                                TextInput::make('username')
                                    ->label('Username')
                                    ->required(),
                            ]),

                        Tab::make('Contact')
                            ->key('contact')
                            ->id('profile-contact')
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel(),
                            ]),
                    ]),
                Group::make([
                    Tabs::make('Delivery Tabs')
                        ->key('profile')
                        ->id('delivery-tabs')
                        ->persistTabInQueryString('delivery_tab')
                        ->tabs([
                            Tab::make('Account')->key('account')->id('delivery-account')
                                ->schema([TextInput::make('recipient')->label('Recipient')]),
                            Tab::make('Contact')->key('contact')->id('delivery-contact')
                                ->schema([TextInput::make('delivery_phone')->label('Delivery phone')]),
                        ]),
                ])->key('delivery'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
