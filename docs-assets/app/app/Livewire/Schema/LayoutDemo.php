<?php

namespace App\Livewire\Schema;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Split;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

class LayoutDemo extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
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
                                Tab::make('Rate Limiting')
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
                                Tab::make('Proxy'),
                                Tab::make('Meta'),
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
                                Tab::make('Notifications')
                                    ->icon(Heroicon::Bell)
                                    ->schema([
                                        Checkbox::make('enabled')
                                            ->default(true),
                                        Select::make('frequency')
                                            ->default('hourly')
                                            ->options([
                                                'hourly' => 'Hourly',
                                            ]),
                                    ]),
                                Tab::make('Security')
                                    ->icon(Heroicon::LockClosed),
                                Tab::make('Meta')
                                    ->icon(Heroicon::Bars3CenterLeft),
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
                                Tab::make('Notifications')
                                    ->icon(Heroicon::Bell)
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
                                Tab::make('Security')
                                    ->icon(Heroicon::LockClosed)
                                    ->iconPosition(IconPosition::After),
                                Tab::make('Meta')
                                    ->icon(Heroicon::Bars3CenterLeft)
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
                                Tab::make('Notifications')
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
                                Tab::make('Security'),
                                Tab::make('Meta'),
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
                                ->icon(Heroicon::ShoppingBag)
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
                                ->icon(Heroicon::Truck),
                            Wizard\Step::make('Billing')
                                ->icon(Heroicon::CreditCard),
                        ])
                            ->statePath('wizardIcons'),
                    ]),
                Group::make()
                    ->id('wizardCompletedIcons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Order')
                                ->icon(Heroicon::ShoppingBag)
                                ->completedIcon(Heroicon::HandThumbUp),
                            Wizard\Step::make('Delivery')
                                ->icon(Heroicon::Truck)
                                ->completedIcon(Heroicon::HandThumbUp),
                            Wizard\Step::make('Billing')
                                ->icon(Heroicon::CreditCard)
                                ->completedIcon(Heroicon::HandThumbUp)
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
                        ])
                            ->startOnStep(3)
                            ->statePath('wizardCompletedIcons'),
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
                    ->id('sectionHeaderActions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Section::make('Rate limiting')
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->headerActions([
                                Action::make('test'),
                            ])
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
                    ->id('sectionFooterActions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Section::make('Rate limiting')
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->footerActions([
                                Action::make('test'),
                            ])
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
                            ->icon(Heroicon::ShoppingBag)
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
                    ->id('split')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Split::make([
                            Section::make([
                                TextInput::make('title')
                                    ->default('Lorem ipsum dolor sit amet'),
                                Textarea::make('content')
                                    ->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec euismod, nisl eget tempor aliquam, nunc nisl aliquet nunc, quis aliquam nisl nunc quis nisl. Donec euismod, nisl eget tempor aliquam, nunc nisl aliquet nunc, quis aliquam nisl nunc quis nisl.')
                                    ->rows(5),
                            ]),
                            Section::make([
                                Toggle::make('is_published')
                                    ->default(true),
                                Toggle::make('is_featured'),
                            ])->grow(false),
                        ])->statePath('split'),
                    ]),
                Group::make()
                    ->id('independentActions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon(Heroicon::Star),
                            Action::make('resetStars')
                                ->icon(Heroicon::XMark)
                                ->color('danger'),
                        ]),
                    ]),
                Group::make()
                    ->id('independentActionsFullWidth')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon(Heroicon::Star),
                            Action::make('resetStars')
                                ->icon(Heroicon::XMark)
                                ->color('danger'),
                        ])->fullWidth(),
                    ]),
                Group::make()
                    ->id('independentActionsHorizontallyAlignedCenter')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon(Heroicon::Star),
                            Action::make('resetStars')
                                ->icon(Heroicon::XMark)
                                ->color('danger'),
                        ])->alignment(Alignment::Center),
                    ]),
                Group::make()
                    ->id('independentActionsVerticallyAlignedEnd')
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
                                        ->icon(Heroicon::Star),
                                    Action::make('resetStars')
                                        ->icon(Heroicon::XMark)
                                        ->color('danger'),
                                ])->verticalAlignment(VerticalAlignment::End),
                            ]),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.schema.layout');
    }
}
