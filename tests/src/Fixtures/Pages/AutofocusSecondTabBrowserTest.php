<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AutofocusSecondTabBrowserTest extends Page
{
    protected string $view = 'pages.autofocus-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCursorArrowRays;

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
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('First Tab')
                            ->schema([
                                TextInput::make('name'),
                            ]),
                        Tab::make('Second Tab')
                            ->schema([
                                TextInput::make('email')
                                    ->autofocus(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }
}
