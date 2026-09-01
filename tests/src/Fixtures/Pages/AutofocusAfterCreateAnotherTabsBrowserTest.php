<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AutofocusAfterCreateAnotherTabsBrowserTest extends Page
{
    protected string $view = 'pages.autofocus-after-create-another-tabs-browser-test';

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
                                TextInput::make('name')
                                    ->autofocus(),
                                TextInput::make('email'),
                            ]),
                        Tab::make('Second Tab')
                            ->schema([
                                TextInput::make('phone'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function simulateCreateAnother(): void
    {
        $this->form->fill();

        $hydratedDefaultState = null;
        $this->form->hydrateState($hydratedDefaultState, shouldCallHydrationHooks: false);

        $this->form->dispatchClientSideStateReset();
    }
}
