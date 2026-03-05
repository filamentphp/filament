<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AutofocusBasicBrowserTest extends Page
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
                TextInput::make('name'),
                TextInput::make('email')
                    ->autofocus(),
            ])
            ->statePath('data');
    }
}
