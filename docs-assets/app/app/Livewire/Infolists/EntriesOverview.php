<?php

namespace App\Livewire\Infolists;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

class EntriesOverview extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                Group::make()
                    ->id('product_info')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-6xl',
                    ])
                    ->schema([
                        Section::make('Product information')
                            ->description('Detailed information about the product')
                            ->icon(Heroicon::Wallet)
                            ->collapsible()
                            ->persistCollapsed(false)
                            ->columnSpan(['lg' => 2])
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Product name')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextSize::Large)
                                            ->state('Premium Ergonomic Office Chair')
                                            ->icon(Heroicon::InformationCircle)
                                            ->iconPosition(IconPosition::Before)
                                            ->columnSpan(2),

                                        TextEntry::make('sku')
                                            ->label('SKU')
                                            ->state('CHAIR-ERG-2023')
                                            ->fontFamily(FontFamily::Mono)
                                            ->size(TextSize::Large)
                                            ->copyable()
                                            ->copyMessage('SKU copied to clipboard!')
                                            ->copyMessageDuration(1500)
                                            ->badge()
                                            ->color('gray'),

                                        TextEntry::make('status')
                                            ->label('Stock status')
                                            ->state('in_stock')
                                            ->badge()
                                            ->formatStateUsing(
                                                fn (string $state) => str($state)
                                                    ->replace('_', ' ')
                                                    ->title()
                                            )
                                            ->icon(fn (string $state): string => match ($state) {
                                                'in_stock' => 'heroicon-o-check-circle',
                                                'low_stock' => 'heroicon-o-exclamation-circle',
                                                'out_of_stock' => 'heroicon-o-x-circle',
                                                default => 'heroicon-o-question-mark-circle',
                                            })
                                            ->color(fn (string $state): string => match ($state) {
                                                'in_stock' => 'success',
                                                'low_stock' => 'warning',
                                                'out_of_stock' => 'danger',
                                                default => 'gray',
                                            }),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('description')
                                            ->state('Experience unparalleled comfort with our Premium Ergonomic Office Chair. Designed for professionals who spend long hours at their desk, this chair offers adjustable lumbar support, breathable mesh backing, and customizable armrests.')
                                            ->markdown()
                                            ->columnSpan(2),

                                        ImageEntry::make('product_image')
                                            ->state('https://cdn.pixabay.com/photo/2021/09/26/11/44/chair-6657315_1280.jpg')
                                            ->columnSpan(1),
                                    ]),
                            ]),

                        Section::make('Pricing & features')
                            ->icon(Heroicon::CurrencyDollar)
                            ->description('Information about pricing and features')
                            ->collapsible()
                            ->columnSpan(['lg' => 1])
                            ->schema([
                                TextEntry::make('price')
                                    ->state('249.99')
                                    ->prefix('$')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->suffix(' USD')
                                    ->color('primary'),

                                IconEntry::make('featured')
                                    ->label('Featured product')
                                    ->state(true)
                                    ->boolean()
                                    ->trueIcon(Heroicon::Star)
                                    ->falseIcon(Heroicon::XMark)
                                    ->trueColor('warning')
                                    ->size(IconSize::Large),

                                ColorEntry::make('color_options')
                                    ->label('Available colors')
                                    ->state(['#000000', '#0047AB', '#8B4513'])
                                    ->afterStateHydrated(function (ColorEntry $component, array $state): void {
                                        $component->tooltip(function (string $color) {
                                            return match ($color) {
                                                '#000000' => 'Midnight Black',
                                                '#0047AB' => 'Cobalt Blue',
                                                '#8B4513' => 'Saddle Brown',
                                                default => $color,
                                            };
                                        });
                                    }),

                            ]),

                        Tabs::make('Product Details')
                            ->tabs([
                                Tabs\Tab::make('Specifications')
                                    ->icon(Heroicon::ClipboardDocumentList)
                                    ->schema([
                                        KeyValueEntry::make('specifications')
                                            ->label(false)
                                            ->state([
                                                'Material' => 'Mesh, aluminum, high-grade plastic',
                                                'Weight capacity' => '300 lbs',
                                                'Height adjustment' => '17" to 21"',
                                                'Warranty' => '5 years limited',
                                                'Assembly required' => 'Yes (Tools Included)',
                                            ])
                                            ->keyLabel('Specification')
                                            ->valueLabel('Details'),
                                    ]),

                                Tabs\Tab::make('Reviews')
                                    ->icon(Heroicon::OutlinedPencilSquare)
                                    ->badge(fn () => 3),
                            ])
                            ->columnSpan(['lg' => 3]),
                    ]),
            ])
            ->constantState([
                'reviews' => [
                    [
                        'name' => 'Jane Smith',
                        'stars' => '5',
                        'comment' => 'Best office chair I\'ve ever purchased! The lumbar support has helped my back pain significantly.',
                    ],
                    [
                        'name' => 'Michael Johnson',
                        'stars' => '4',
                        'comment' => 'Very comfortable and well-built. Assembly was straightforward. Removed one star because the armrests could be more padded.',
                    ],
                    [
                        'name' => 'Sarah Williams',
                        'stars' => '5',
                        'comment' => 'Worth every penny! I\'m sitting comfortably for 8+ hours of work each day.',
                    ],
                ],
            ]);
    }

    public function render()
    {
        return view('livewire.infolists.overview');
    }
}
