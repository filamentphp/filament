<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Wizard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class WizardSchema
{
    public static function schema(): array
    {
        return [
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
                ->id('wizardSubmitAction')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Wizard::make([
                        Wizard\Step::make('Order')
                            ->icon(Heroicon::ShoppingBag),
                        Wizard\Step::make('Delivery')
                            ->icon(Heroicon::Truck),
                        Wizard\Step::make('Billing')
                            ->icon(Heroicon::CreditCard)
                            ->schema([
                                Select::make('paymentMethod')
                                    ->label('Payment method')
                                    ->options([
                                        'credit_card' => 'Credit card',
                                        'bank_transfer' => 'Bank transfer',
                                        'paypal' => 'PayPal',
                                    ])
                                    ->default('credit_card'),
                                TextInput::make('cardNumber')
                                    ->label('Card number')
                                    ->default('4242 4242 4242 4242'),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('expiryDate')
                                            ->label('Expiry date')
                                            ->default('12/28'),
                                        TextInput::make('cvc')
                                            ->label('CVC')
                                            ->default('123'),
                                    ]),
                            ]),
                    ])
                        ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                            <x-filament::button
                                type="submit"
                                size="sm"
                            >
                                Submit
                            </x-filament::button>
                        BLADE)))
                        ->startOnStep(3)
                        ->statePath('wizardSubmitAction'),
                ]),
        ];
    }
}
