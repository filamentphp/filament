<?php

namespace App\Livewire\Forms;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\VerticalAlignment;
use Livewire\Component;

class LayoutDemo extends Component implements HasForms
{
    use InteractsWithForms;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Group::make()
                    ->id('fieldset')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Fieldset::make('Rate limiting')
                            ->statePath('fieldset')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->default('hour')
                                    ->options([
                                        'hour' => 'Hour',
                                    ]),
                                TextInput::make('maximum')
                                    ->default(100),
                            ])
                            ->columns(3),
                    ]),
                Group::make()
                    ->id('tabs')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Tabs::make('Tabs')
                            ->statePath('tabs')
                            ->schema([
                                Tabs\Tab::make('Rate Limiting')
                                    ->schema([
                                        TextInput::make('hits')
                                            ->default(30),
                                        Select::make('period')
                                            ->default('hour')
                                            ->options([
                                                'hour' => 'Hour',
                                            ]),
                                        TextInput::make('maximum')
                                            ->default(100),
                                        Textarea::make('notes')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),
                                Tabs\Tab::make('Proxy'),
                                Tabs\Tab::make('Meta'),
                            ]),
                    ]),
                Group::make()
                    ->id('tabsIcons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Tabs::make('Tabs')
                            ->statePath('tabsIcons')
                            ->schema([
                                Tabs\Tab::make('Notifications')
                                    ->icon('heroicon-m-bell')
                                    ->schema([
                                        Checkbox::make('enabled')
                                            ->default(true),
                                        Select::make('frequency')
                                            ->default('hourly')
                                            ->options([
                                                'hourly' => 'Hourly',
                                            ]),
                                    ]),
                                Tabs\Tab::make('Security')
                                    ->icon('heroicon-m-lock-closed'),
                                Tabs\Tab::make('Meta')
                                    ->icon('heroicon-m-bars-3-center-left'),
                            ]),
                    ]),
                Group::make()
                    ->id('tabsIconsAfter')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Tabs::make('Tabs')
                            ->statePath('tabsIconsAfter')
                            ->schema([
                                Tabs\Tab::make('Notifications')
                                    ->icon('heroicon-m-bell')
                                    ->iconPosition(IconPosition::After)
                                    ->schema([
                                        Checkbox::make('enabled')
                                            ->default(true),
                                        Select::make('frequency')
                                            ->default('hourly')
                                            ->options([
                                                'hourly' => 'Hourly',
                                            ]),
                                    ]),
                                Tabs\Tab::make('Security')
                                    ->icon('heroicon-m-lock-closed')
                                    ->iconPosition(IconPosition::After),
                                Tabs\Tab::make('Meta')
                                    ->icon('heroicon-m-bars-3-center-left')
                                    ->iconPosition(IconPosition::After),
                            ]),
                    ]),
                Group::make()
                    ->id('tabsBadges')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Tabs::make('Tabs')
                            ->statePath('tabsBadges')
                            ->schema([
                                Tabs\Tab::make('Notifications')
                                    ->badge(5)
                                    ->schema([
                                        Checkbox::make('enabled')
                                            ->default(true),
                                        Select::make('frequency')
                                            ->default('hourly')
                                            ->options([
                                                'hourly' => 'Hourly',
                                            ]),
                                    ]),
                                Tabs\Tab::make('Security'),
                                Tabs\Tab::make('Meta'),
                            ]),
                    ]),
                Group::make()
                    ->id('wizard')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Order')
                                ->schema([
                                    Repeater::make('items')
                                        ->hiddenLabel()
                                        ->schema([
                                            Select::make('product')
                                                ->options([
                                                    'tshirt' => 'Filament t-shirt',
                                                ]),
                                            TextInput::make('quantity'),
                                        ])
                                        ->columns(2)
                                        ->reorderable(false)
                                        ->addActionLabel('Add to order')
                                        ->default([
                                            [
                                                'product' => 'tshirt',
                                                'quantity' => 3,
                                            ],
                                        ]),
                                    Textarea::make('specialOrderNotes'),
                                ]),
                            Wizard\Step::make('Delivery'),
                            Wizard\Step::make('Billing'),
                        ])
                            ->statePath('wizard'),
                    ]),
                Group::make()
                    ->id('wizardIcons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Order')
                                ->icon('heroicon-m-shopping-bag')
                                ->schema([
                                    Repeater::make('items')
                                        ->hiddenLabel()
                                        ->schema([
                                            Select::make('product')
                                                ->options([
                                                    'tshirt' => 'Filament t-shirt',
                                                ]),
                                            TextInput::make('quantity'),
                                        ])
                                        ->columns(2)
                                        ->reorderable(false)
                                        ->addActionLabel('Add to order')
                                        ->default([
                                            [
                                                'product' => 'tshirt',
                                                'quantity' => 3,
                                            ],
                                        ]),
                                    Textarea::make('specialOrderNotes'),
                                ]),
                            Wizard\Step::make('Delivery')
                                ->icon('heroicon-m-truck'),
                            Wizard\Step::make('Billing')
                                ->icon('heroicon-m-credit-card'),
                        ])
                            ->statePath('wizardIcons'),
                    ]),
                Group::make()
                    ->id('wizardDescriptions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Order')
                                ->description('Review your basket')
                                ->schema([
                                    Repeater::make('items')
                                        ->hiddenLabel()
                                        ->schema([
                                            Select::make('product')
                                                ->options([
                                                    'tshirt' => 'Filament t-shirt',
                                                ]),
                                            TextInput::make('quantity'),
                                        ])
                                        ->columns(2)
                                        ->reorderable(false)
                                        ->addActionLabel('Add to order')
                                        ->default([
                                            [
                                                'product' => 'tshirt',
                                                'quantity' => 3,
                                            ],
                                        ]),
                                    Textarea::make('specialOrderNotes'),
                                ]),
                            Wizard\Step::make('Delivery')
                                ->description('Send us your address'),
                            Wizard\Step::make('Billing')
                                ->description('Select a payment method'),
                        ])
                            ->statePath('wizardDescriptions'),
                    ]),
                Group::make()
                    ->id('section')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Section::make('Rate limiting')
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->statePath('section')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->default('hour')
                                    ->options([
                                        'hour' => 'Hour',
                                    ]),
                                TextInput::make('maximum')
                                    ->default(100),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ]),
                Group::make()
                    ->id('sectionIcons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Section::make('Cart')
                            ->description('The items you have selected for purchase')
                            ->icon('heroicon-m-shopping-bag')
                            ->statePath('sectionIcons')
                            ->schema([
                                Repeater::make('items')
                                    ->hiddenLabel()
                                    ->schema([
                                        Select::make('product')
                                            ->options([
                                                'tshirt' => 'Filament t-shirt',
                                            ]),
                                        TextInput::make('quantity'),
                                    ])
                                    ->columns(2)
                                    ->reorderable(false)
                                    ->addActionLabel('Add to order')
                                    ->default([
                                        [
                                            'product' => 'tshirt',
                                            'quantity' => 3,
                                        ],
                                    ]),
                                Textarea::make('specialOrderNotes'),
                            ]),
                    ]),
                Group::make()
                    ->id('sectionAside')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Section::make('Rate limiting')
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->aside()
                            ->statePath('sectionAside')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->default('hour')
                                    ->options([
                                        'hour' => 'Hour',
                                    ]),
                                TextInput::make('maximum')
                                    ->default(100),
                                Textarea::make('notes'),
                            ]),
                    ]),
                Group::make()
                    ->id('sectionCollapsed')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Section::make('Cart')
                            ->description('The items you have selected for purchase')
                            ->collapsed()
                            ->statePath('sectionCollapsed'),
                    ]),
                Group::make()
                    ->id('sectionCompact')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Section::make('Rate limiting')
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->compact()
                            ->statePath('sectionCompact')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->default('hour')
                                    ->options([
                                        'hour' => 'Hour',
                                    ]),
                                TextInput::make('maximum')
                                    ->default(100),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ]),
                Group::make()
                    ->id('placeholder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Placeholder::make('created')
                            ->content('May 21, 2021'),
                    ]),
                Group::make()
                    ->id('sectionWithoutHeader')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Section::make([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                            Textarea::make('notes')
                                ->columnSpanFull(),
                        ])
                            ->statePath('sectionWithoutHeader')
                            ->columns(3),
                    ]),
                Group::make()
                    ->id('anonymousActions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon('heroicon-m-star'),
                            Action::make('resetStars')
                                ->icon('heroicon-m-x-mark')
                                ->color('danger'),
                        ]),
                    ]),
                Group::make()
                    ->id('anonymousActionsFullWidth')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon('heroicon-m-star'),
                            Action::make('resetStars')
                                ->icon('heroicon-m-x-mark')
                                ->color('danger'),
                        ])->fullWidth(),
                    ]),
                Group::make()
                    ->id('anonymousActionsHorizontallyAlignedCenter')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon('heroicon-m-star'),
                            Action::make('resetStars')
                                ->icon('heroicon-m-x-mark')
                                ->color('danger'),
                        ])->alignment(Alignment::Center),
                    ]),
                Group::make()
                    ->id('anonymousActionsVerticallyAlignedEnd')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('stars')
                                    ->default('4572100479'),
                                Actions::make([
                                    Action::make('star')
                                        ->icon('heroicon-m-star'),
                                    Action::make('resetStars')
                                        ->icon('heroicon-m-x-mark')
                                        ->color('danger'),
                                ])->verticalAlignment(VerticalAlignment::End),
                            ]),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.forms.layout');
    }
}
