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
use Livewire\Attributes\Url;
use RuntimeException;

class LazySchemaLoadingBrowserTest extends Page
{
    protected string $view = 'pages.lazy-schema-loading-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?int $navigationSort = 15;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    #[Url]
    public bool $shouldFailLoading = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Concealed lazy-loaded details')
                    ->key('concealedLazyLoadedDetails')
                    ->collapsed()
                    ->extraAttributes([
                        'data-testid' => 'concealed-lazy-loaded-section',
                    ])
                    ->schema(
                        Schema::make()
                            ->components([
                                TextInput::make('concealed_lazy_loaded_name')
                                    ->label(fn (): string => $this->shouldFailLoading
                                        ? throw new RuntimeException('Simulated schema loading failure.')
                                        : 'Concealed lazy-loaded name'),
                            ])
                            ->lazyLoaded(),
                    ),
                Text::make('Scroll to load the lazy-loaded schema.')
                    ->extraAttributes([
                        'data-testid' => 'lazy-loaded-schema-spacer',
                        'style' => 'display: block; height: 100vh',
                    ]),
                Section::make('Lazy-loaded details')
                    ->key('lazyLoadedDetails')
                    ->extraAttributes([
                        'data-testid' => 'viewport-lazy-loaded-section',
                    ])
                    ->schema(
                        Schema::make()
                            ->components([
                                TextInput::make('lazy_loaded_name')
                                    ->label('Lazy-loaded name')
                                    ->required(),
                            ])
                            ->lazyLoaded(),
                    ),
                Tabs::make('Lazy-loaded tabs')
                    ->key('lazyLoadedTabs')
                    ->extraAttributes([
                        'data-testid' => 'lazy-loaded-tabs',
                    ])
                    ->tabs([
                        Tab::make('Profile')
                            ->key('profileTab')
                            ->schema(
                                Schema::make()
                                    ->components([
                                        TextInput::make('profile_name')
                                            ->label('Lazy-loaded profile name'),
                                    ])
                                    ->lazyLoaded(),
                            ),
                        Tab::make('Preferences')
                            ->key('preferencesTab')
                            ->schema(
                                Schema::make()
                                    ->components([
                                        TextInput::make('timezone')
                                            ->label('Lazy-loaded timezone'),
                                    ])
                                    ->lazyLoaded(),
                            ),
                    ]),
                Wizard::make([
                    Step::make('Account')
                        ->key('accountStep')
                        ->schema(
                            Schema::make()
                                ->components([
                                    TextInput::make('account_name')
                                        ->label('Lazy-loaded account name'),
                                ])
                                ->lazyLoaded(),
                        ),
                    Step::make('Confirmation')
                        ->key('confirmationStep')
                        ->schema(
                            Schema::make()
                                ->components([
                                    TextInput::make('confirmation_note')
                                        ->label('Lazy-loaded confirmation note'),
                                ])
                                ->lazyLoaded(),
                        ),
                ])
                    ->key('lazyLoadedWizard')
                    ->extraAttributes([
                        'data-testid' => 'lazy-loaded-wizard',
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
