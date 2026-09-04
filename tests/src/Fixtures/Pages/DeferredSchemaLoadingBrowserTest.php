<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DeferredSchemaLoadingBrowserTest extends Page
{
    protected string $view = 'pages.deferred-schema-loading-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?int $navigationSort = 15;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Concealed deferred details')
                    ->key('concealedDeferredDetails')
                    ->collapsed()
                    ->extraAttributes([
                        'data-testid' => 'concealed-deferred-section',
                    ])
                    ->schema(
                        Schema::make()
                            ->components([
                                TextInput::make('concealed_deferred_name')
                                    ->label('Concealed deferred name'),
                            ])
                            ->deferLoading(),
                    ),
                Text::make('Scroll to load the deferred schema.')
                    ->extraAttributes([
                        'data-testid' => 'deferred-schema-spacer',
                        'style' => 'display: block; height: 100vh',
                    ]),
                Section::make('Deferred details')
                    ->key('deferredDetails')
                    ->extraAttributes([
                        'data-testid' => 'viewport-deferred-section',
                    ])
                    ->schema(
                        Schema::make()
                            ->components([
                                TextInput::make('deferred_name')
                                    ->label('Deferred name')
                                    ->required(),
                            ])
                            ->deferLoading(),
                    ),
                Tabs::make('Deferred tabs')
                    ->key('deferredTabs')
                    ->extraAttributes([
                        'data-testid' => 'deferred-tabs',
                    ])
                    ->tabs([
                        Tab::make('Profile')
                            ->key('profileTab')
                            ->schema(
                                Schema::make()
                                    ->components([
                                        TextInput::make('profile_name')
                                            ->label('Deferred profile name'),
                                    ])
                                    ->deferLoading(),
                            ),
                        Tab::make('Preferences')
                            ->key('preferencesTab')
                            ->schema(
                                Schema::make()
                                    ->components([
                                        TextInput::make('timezone')
                                            ->label('Deferred timezone'),
                                    ])
                                    ->deferLoading(),
                            ),
                    ]),
                Wizard::make([
                    Step::make('Account')
                        ->key('accountStep')
                        ->schema(
                            Schema::make()
                                ->components([
                                    TextInput::make('account_name')
                                        ->label('Deferred account name'),
                                ])
                                ->deferLoading(),
                        ),
                    Step::make('Confirmation')
                        ->key('confirmationStep')
                        ->schema(
                            Schema::make()
                                ->components([
                                    TextInput::make('confirmation_note')
                                        ->label('Deferred confirmation note'),
                                ])
                                ->deferLoading(),
                        ),
                ])
                    ->key('deferredWizard')
                    ->extraAttributes([
                        'data-testid' => 'deferred-wizard',
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
