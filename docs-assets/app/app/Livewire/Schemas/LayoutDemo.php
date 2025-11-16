<?php

namespace App\Livewire\Schemas;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('fieldset')
                    ->schema([
                        Fieldset::make('Rate limiting')
                            ->columns(3)
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->options([
                                        'hour' => 'Hour',
                                    ])
                                    ->default('hour'),
                                TextInput::make('maximum')
                                    ->default(100),
                            ])
                            ->statePath('fieldset'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('fieldsetNotContained')
                    ->schema([
                        Fieldset::make('Rate limiting')
                            ->columns(3)
                            ->contained(false)
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->options([
                                        'hour' => 'Hour',
                                    ])
                                    ->default('hour'),
                                TextInput::make('maximum')
                                    ->default(100),
                            ])
                            ->statePath('fieldsetNotContained'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('dense')
                    ->schema([
                        Fieldset::make('Dense')
                            ->columns(1)
                            ->dense()
                            ->schema([
                                TextEntry::make('name')
                                    ->state('Dan Harrin'),
                                TextEntry::make('role')
                                    ->state('Admin'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('noGap')
                    ->schema([
                        Fieldset::make('No gap')
                            ->columns(1)
                            ->gap(false)
                            ->schema([
                                TextEntry::make('name')
                                    ->state('Dan Harrin'),
                                TextEntry::make('role')
                                    ->state('Admin'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('tabs')
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tab::make('Rate Limiting')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('hits')
                                            ->default(30),
                                        Select::make('period')
                                            ->options([
                                                'hour' => 'Hour',
                                            ])
                                            ->default('hour'),
                                        TextInput::make('maximum')
                                            ->default(100),
                                        Textarea::make('notes')
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make('Proxy'),
                                Tab::make('Meta'),
                            ])
                            ->statePath('tabs'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('tabsIcons')
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tab::make('Notifications')
                                    ->icon(Heroicon::Bell)
                                    ->schema([
                                        Checkbox::make('enabled')
                                            ->default(true),
                                        Select::make('frequency')
                                            ->options([
                                                'hourly' => 'Hourly',
                                            ])
                                            ->default('hourly'),
                                    ]),
                                Tab::make('Security')
                                    ->icon(Heroicon::LockClosed),
                                Tab::make('Meta')
                                    ->icon(Heroicon::Bars3CenterLeft),
                            ])
                            ->statePath('tabsIcons'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('tabsIconsAfter')
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tab::make('Notifications')
                                    ->icon(Heroicon::Bell)
                                    ->iconPosition(IconPosition::After)
                                    ->schema([
                                        Checkbox::make('enabled')
                                            ->default(true),
                                        Select::make('frequency')
                                            ->options([
                                                'hourly' => 'Hourly',
                                            ])
                                            ->default('hourly'),
                                    ]),
                                Tab::make('Security')
                                    ->icon(Heroicon::LockClosed)
                                    ->iconPosition(IconPosition::After),
                                Tab::make('Meta')
                                    ->icon(Heroicon::Bars3CenterLeft)
                                    ->iconPosition(IconPosition::After),
                            ])
                            ->statePath('tabsIconsAfter'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('tabsBadges')
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tab::make('Notifications')
                                    ->badge(5)
                                    ->schema([
                                        Checkbox::make('enabled')
                                            ->default(true),
                                        Select::make('frequency')
                                            ->options([
                                                'hourly' => 'Hourly',
                                            ])
                                            ->default('hourly'),
                                    ]),
                                Tab::make('Security'),
                                Tab::make('Meta'),
                            ])
                            ->statePath('tabsBadges'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('tabsBadgesColor')
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tab::make('Notifications')
                                    ->badge(5)
                                    ->badgeColor('info')
                                    ->schema([
                                        Checkbox::make('enabled')
                                            ->default(true),
                                        Select::make('frequency')
                                            ->options([
                                                'hourly' => 'Hourly',
                                            ])
                                            ->default('hourly'),
                                    ]),
                                Tab::make('Security'),
                                Tab::make('Meta'),
                            ])
                            ->statePath('tabsBadgesColor'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('tabsVertical')
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tab::make('Rate Limiting')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('hits')
                                            ->default(30),
                                        Select::make('period')
                                            ->options([
                                                'hour' => 'Hour',
                                            ])
                                            ->default('hour'),
                                        TextInput::make('maximum')
                                            ->default(100),
                                        Textarea::make('notes')
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make('Proxy'),
                                Tab::make('Meta'),
                            ])
                            ->vertical()
                            ->statePath('tabsVertical'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('wizard')
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Order')
                                ->schema([
                                    Repeater::make('items')
                                        ->addActionLabel('Add to order')
                                        ->columns(2)
                                        ->hiddenLabel()
                                        ->reorderable(false)
                                        ->schema([
                                            Select::make('product')
                                                ->options([
                                                    'tshirt' => 'Filament t-shirt',
                                                ]),
                                            TextInput::make('quantity'),
                                        ])
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('wizardIcons')
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Order')
                                ->icon(Heroicon::ShoppingBag)
                                ->schema([
                                    Repeater::make('items')
                                        ->addActionLabel('Add to order')
                                        ->columns(2)
                                        ->hiddenLabel()
                                        ->reorderable(false)
                                        ->schema([
                                            Select::make('product')
                                                ->options([
                                                    'tshirt' => 'Filament t-shirt',
                                                ]),
                                            TextInput::make('quantity'),
                                        ])
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('wizardCompletedIcons')
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Order')
                                ->completedIcon(Heroicon::HandThumbUp)
                                ->icon(Heroicon::ShoppingBag),
                            Wizard\Step::make('Delivery')
                                ->completedIcon(Heroicon::HandThumbUp)
                                ->icon(Heroicon::Truck),
                            Wizard\Step::make('Billing')
                                ->completedIcon(Heroicon::HandThumbUp)
                                ->icon(Heroicon::CreditCard)
                                ->schema([
                                    Repeater::make('items')
                                        ->addActionLabel('Add to order')
                                        ->columns(2)
                                        ->hiddenLabel()
                                        ->reorderable(false)
                                        ->schema([
                                            Select::make('product')
                                                ->options([
                                                    'tshirt' => 'Filament t-shirt',
                                                ]),
                                            TextInput::make('quantity'),
                                        ])
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('wizardDescriptions')
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Order')
                                ->description('Review your basket')
                                ->schema([
                                    Repeater::make('items')
                                        ->addActionLabel('Add to order')
                                        ->columns(2)
                                        ->hiddenLabel()
                                        ->reorderable(false)
                                        ->schema([
                                            Select::make('product')
                                                ->options([
                                                    'tshirt' => 'Filament t-shirt',
                                                ]),
                                            TextInput::make('quantity'),
                                        ])
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('section')
                    ->schema([
                        Section::make('Rate limiting')
                            ->columns(3)
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->options([
                                        'hour' => 'Hour',
                                    ])
                                    ->default('hour'),
                                TextInput::make('maximum')
                                    ->default(100),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->statePath('section'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('sectionHeaderActions')
                    ->schema([
                        Section::make('Rate limiting')
                            ->afterHeader([
                                Action::make('test'),
                            ])
                            ->columns(3)
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->options([
                                        'hour' => 'Hour',
                                    ])
                                    ->default('hour'),
                                TextInput::make('maximum')
                                    ->default(100),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->statePath('section'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('sectionFooterActions')
                    ->schema([
                        Section::make('Rate limiting')
                            ->columns(3)
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->footer([
                                Action::make('test'),
                            ])
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->options([
                                        'hour' => 'Hour',
                                    ])
                                    ->default('hour'),
                                TextInput::make('maximum')
                                    ->default(100),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->statePath('section'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('sectionIcons')
                    ->schema([
                        Section::make('Cart')
                            ->description('The items you have selected for purchase')
                            ->icon(Heroicon::ShoppingBag)
                            ->schema([
                                Repeater::make('items')
                                    ->addActionLabel('Add to order')
                                    ->columns(2)
                                    ->hiddenLabel()
                                    ->reorderable(false)
                                    ->schema([
                                        Select::make('product')
                                            ->options([
                                                'tshirt' => 'Filament t-shirt',
                                            ]),
                                        TextInput::make('quantity'),
                                    ])
                                    ->default([
                                        [
                                            'product' => 'tshirt',
                                            'quantity' => 3,
                                        ],
                                    ]),
                                Textarea::make('specialOrderNotes'),
                            ])
                            ->statePath('sectionIcons'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('sectionAside')
                    ->schema([
                        Section::make('Rate limiting')
                            ->aside()
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->options([
                                        'hour' => 'Hour',
                                    ])
                                    ->default('hour'),
                                TextInput::make('maximum')
                                    ->default(100),
                                Textarea::make('notes'),
                            ])
                            ->statePath('sectionAside'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('sectionCollapsed')
                    ->schema([
                        Section::make('Cart')
                            ->collapsed()
                            ->description('The items you have selected for purchase')
                            ->statePath('sectionCollapsed'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('sectionCompact')
                    ->schema([
                        Section::make('Rate limiting')
                            ->columns(3)
                            ->compact()
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->options([
                                        'hour' => 'Hour',
                                    ])
                                    ->default('hour'),
                                TextInput::make('maximum')
                                    ->default(100),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->statePath('sectionCompact'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('sectionSecondary')
                    ->schema([
                        Section::make('Rate limiting')
                            ->columns(3)
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->schema([
                                TextInput::make('hits')
                                    ->default(30),
                                Select::make('period')
                                    ->options([
                                        'hour' => 'Hour',
                                    ])
                                    ->default('hour'),
                                TextInput::make('maximum')
                                    ->default(100),
                                Section::make('Notes')
                                    ->columnSpanFull()
                                    ->compact()
                                    ->schema([
                                        Textarea::make('notes')
                                            ->hiddenLabel(),
                                    ])
                                    ->secondary(),
                            ])
                            ->statePath('sectionSecondary'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('sectionWithoutHeader')
                    ->schema([
                        Section::make([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->options([
                                    'hour' => 'Hour',
                                ])
                                ->default('hour'),
                            TextInput::make('maximum')
                                ->default(100),
                            Textarea::make('notes')
                                ->columnSpanFull(),
                        ])
                            ->columns(3)
                            ->statePath('sectionWithoutHeader'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('flex')
                    ->schema([
                        Flex::make([
                            Section::make([
                                TextInput::make('title')
                                    ->default('Lorem ipsum dolor sit amet'),
                                Textarea::make('content')
                                    ->rows(5)
                                    ->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec euismod, nisl eget tempor aliquam, nunc nisl aliquet nunc, quis aliquam nisl nunc quis nisl. Donec euismod, nisl eget tempor aliquam, nunc nisl aliquet nunc, quis aliquam nisl nunc quis nisl.'),
                            ]),
                            Section::make([
                                Toggle::make('is_published')
                                    ->default(true),
                                Toggle::make('is_featured'),
                            ])->grow(false),
                        ])->statePath('flex'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('independentActions')
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon(Heroicon::Star),
                            Action::make('resetStars')
                                ->color('danger')
                                ->icon(Heroicon::XMark),
                        ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('independentActionsFullWidth')
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon(Heroicon::Star),
                            Action::make('resetStars')
                                ->color('danger')
                                ->icon(Heroicon::XMark),
                        ])->fullWidth(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('independentActionsHorizontallyAlignedCenter')
                    ->schema([
                        Actions::make([
                            Action::make('star')
                                ->icon(Heroicon::Star),
                            Action::make('resetStars')
                                ->color('danger')
                                ->icon(Heroicon::XMark),
                        ])->alignment(Alignment::Center),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->id('independentActionsVerticallyAlignedEnd')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('stars')
                                    ->default('4572100479'),
                                Actions::make([
                                    Action::make('star')
                                        ->icon(Heroicon::Star),
                                    Action::make('resetStars')
                                        ->color('danger')
                                        ->icon(Heroicon::XMark),
                                ])->verticalAlignment(VerticalAlignment::End),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('emptyState')
                    ->schema([
                        EmptyState::make('No users yet')
                            ->description('Get started by creating a new user.')
                            ->footer([
                                Action::make('createUser')
                                    ->icon(Heroicon::Plus),
                            ])
                            ->icon(Heroicon::OutlinedUser),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.schema.layout');
    }
}
