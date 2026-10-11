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

    public ?string $activeTab = 'second';

    public bool $showSecondTab = true;

    public bool $showZeroTab = true;

    public ?string $activeFilter = 'all';

    public bool $showKeyboardTabs = false;

    public bool $showOverflowTabs = false;

    public bool $showEnclosingDropdown = false;

    public bool $hasDisabledProfileHeader = false;

    public function mount(): void
    {
        $this->showKeyboardTabs = request()->boolean('keyboard');
        $this->showOverflowTabs = request()->boolean('overflow');
        $this->showEnclosingDropdown = request()->boolean('dropdown');
        $this->hasDisabledProfileHeader = request()->boolean('disabled-profile');
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
                            ->extraAttributes(fn (): array => $this->hasDisabledProfileHeader ? ['disabled' => true] : [])
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
                        Tab::make('Billing')->key('billing')->id('profile-billing')
                            ->visible(fn (): bool => $this->hasDisabledProfileHeader),
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
                Tabs::make('Overflow Tabs')
                    ->key('overflow')
                    ->id('overflow-tabs')
                    ->visible(fn (): bool => $this->showOverflowTabs)
                    ->scrollable(false)
                    ->tabs([
                        Tab::make('Overview and activity')->key('overview'),
                        Tab::make('Contact information')->key('contact')->badge('12')->deferBadge(),
                        Tab::make('Billing information')->key('billing'),
                        Tab::make('Security settings')->key('security'),
                        Tab::make('Additional preferences')->key('hidden')->visibleJs('showOverflowChoice'),
                        Tab::make('Notification preferences')->key('notifications'),
                    ]),
                Tabs::make('Keyboard tabs')
                    ->key('keyboard')
                    ->id('keyboard-tabs')
                    ->visible(fn (): bool => $this->showKeyboardTabs)
                    ->tabs([
                        Tab::make('Account')->key('account')->visibleJs('showAll')->schema([
                            TextInput::make('keyboard_username')->autofocus(),
                            Tabs::make('Nested tabs')->id('nested-tabs')->persistTab()->tabs([
                                Tab::make('Account')->key('account'),
                                Tab::make('Contact')->key('contact'),
                            ]),
                        ]),
                        Tab::make('Hidden')->key('hidden')->hiddenJs('true'),
                        Tab::make('Disabled header')->key('disabled')->visibleJs('showAll')->extraAttributes(['disabled' => true]),
                        Tab::make('Unavailable')->key('unavailable')->visibleJs('showAll')->extraAttributes(['aria-disabled' => 'true']),
                        Tab::make('Contact')->key('contact')->visibleJs('showContact && showAll')->schema([
                            TextInput::make('keyboard_phone')->autofocus(),
                        ]),
                        Tab::make('Read only')->key('readonly')->disabled()->visibleJs('showAll')->schema([
                            TextInput::make('readonly'),
                        ]),
                    ]),
                Tabs::make('Dynamic tabs')
                    ->key('dynamic')
                    ->id('dynamic-tabs')
                    ->activeTab(2)
                    ->livewireProperty('activeTab')
                    ->visible(fn (): bool => $this->showKeyboardTabs)
                    ->tabs(fn (): array => [
                        '' => Tab::make('All')->schema([TextInput::make('dynamic_all')]),
                        ...($this->showZeroTab ? ['0' => Tab::make('Zero')->schema([TextInput::make('dynamic_zero')])] : []),
                        'unavailable' => Tab::make('Unavailable')->extraAttributes(['aria-disabled' => 'true']),
                        ...($this->showSecondTab ? ['second' => Tab::make('Details')->schema([TextInput::make('dynamic_details')->autofocus()])] : []),
                    ]),
                Tabs::make('Status filters')->key('filters')->id('filter-tabs')
                    ->visible(fn (): bool => $this->showKeyboardTabs)
                    ->livewireProperty('activeFilter')->tabPanels(false)->tabs([
                        'all' => Tab::make('All'),
                        'published' => Tab::make('Published'),
                    ]),
                Tabs::make('Vertical tabs')->key('vertical')->id('vertical-tabs')->vertical()
                    ->visible(fn (): bool => $this->showKeyboardTabs)->tabs([
                        Tab::make('Account')->key('account'),
                        Tab::make('Contact')->key('contact'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
