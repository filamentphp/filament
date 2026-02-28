<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AutofocusWizardBrowserTest extends Page
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
                Wizard::make([
                    Step::make('First Step')
                        ->schema([
                            TextInput::make('name')
                                ->autofocus(),
                        ]),
                    Step::make('Second Step')
                        ->schema([
                            TextInput::make('email')
                                ->autofocus(),
                        ]),
                ]),
            ])
            ->statePath('data');
    }
}
