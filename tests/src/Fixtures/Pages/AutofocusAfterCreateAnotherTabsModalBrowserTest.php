<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Models\Department;

class AutofocusAfterCreateAnotherTabsModalBrowserTest extends Page
{
    protected string $view = 'pages.autofocus-after-create-another-tabs-modal-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCursorArrowRays;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function openModalAction(): CreateAction
    {
        return CreateAction::make('openModal')
            ->model(Department::class)
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('First Tab')
                            ->schema([
                                TextInput::make('name')
                                    ->autofocus(),
                            ]),
                        Tab::make('Second Tab')
                            ->schema([
                                Text::make('Nothing here'),
                            ]),
                    ]),
            ]);
    }
}
