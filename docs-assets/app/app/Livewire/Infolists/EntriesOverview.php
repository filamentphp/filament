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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-6xl',
                    ])
                    ->id('product_info')
                    ->schema([
                        Section::make('Product information')
                            ->collapsible()
                            ->columnSpan(['lg' => 2])
                            ->description('Detailed information about the product')
                            ->icon(Heroicon::Wallet)
                            ->persistCollapsed(false)
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Product name')
                                            ->columnSpan(2)
                                            ->icon(Heroicon::InformationCircle)
                                            ->iconPosition(IconPosition::Before)
                                            ->size(TextSize::Large)
                                            ->weight(FontWeight::Bold)
                                            ->state('Premium Ergonomic Office Chair'),

                                        TextEntry::make('sku')
                                            ->label('SKU')
                                            ->badge()
                                            ->color('gray')
                                            ->copyMessage('SKU copied to clipboard!')
                                            ->copyMessageDuration(1500)
                                            ->copyable()
                                            ->fontFamily(FontFamily::Mono)
                                            ->size(TextSize::Large)
                                            ->state('CHAIR-ERG-2023'),

                                        TextEntry::make('status')
                                            ->label('Stock status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'in_stock' => 'success',
                                                'low_stock' => 'warning',
                                                'out_of_stock' => 'danger',
                                                default => 'gray',
                                            })
                                            ->icon(fn (string $state): string => match ($state) {
                                                'in_stock' => 'heroicon-o-check-circle',
                                                'low_stock' => 'heroicon-o-exclamation-circle',
                                                'out_of_stock' => 'heroicon-o-x-circle',
                                                default => 'heroicon-o-question-mark-circle',
                                            })
                                            ->formatStateUsing(
                                                fn (string $state) => str($state)
                                                    ->replace('_', ' ')
                                                    ->title()
                                            )
                                            ->state('in_stock'),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('description')
                                            ->columnSpan(2)
                                            ->markdown()
                                            ->state('Experience unparalleled comfort with our Premium Ergonomic Office Chair. Designed for professionals who spend long hours at their desk, this chair offers adjustable lumbar support, breathable mesh backing, and customizable armrests.'),

                                        ImageEntry::make('product_image')
                                            ->columnSpan(1)
                                            ->state('https://cdn.pixabay.com/photo/2021/09/26/11/44/chair-6657315_1280.jpg'),
                                    ]),
                            ]),

                        Section::make('Pricing & features')
                            ->collapsible()
                            ->columnSpan(['lg' => 1])
                            ->description('Information about pricing and features')
                            ->icon(Heroicon::CurrencyDollar)
                            ->schema([
                                TextEntry::make('price')
                                    ->color('primary')
                                    ->prefix('$')
                                    ->size(TextSize::Large)
                                    ->suffix(' USD')
                                    ->weight(FontWeight::Bold)
                                    ->state('249.99'),

                                IconEntry::make('featured')
                                    ->label('Featured product')
                                    ->boolean()
                                    ->falseIcon(Heroicon::XMark)
                                    ->size(IconSize::Large)
                                    ->trueColor('warning')
                                    ->trueIcon(Heroicon::Star)
                                    ->state(true),

                                ColorEntry::make('color_options')
                                    ->label('Available colors')
                                    ->afterStateHydrated(function (ColorEntry $component, array $state): void {
                                        $component->tooltip(function (string $color) {
                                            return match ($color) {
                                                '#000000' => 'Midnight Black',
                                                '#0047AB' => 'Cobalt Blue',
                                                '#8B4513' => 'Saddle Brown',
                                                default => $color,
                                            };
                                        });
                                    })
                                    ->state(['#000000', '#0047AB', '#8B4513']),

                            ]),

                        Tabs::make('Product Details')
                            ->columnSpan(['lg' => 3])
                            ->tabs([
                                Tabs\Tab::make('Specifications')
                                    ->icon(Heroicon::ClipboardDocumentList)
                                    ->schema([
                                        KeyValueEntry::make('specifications')
                                            ->label(false)
                                            ->keyLabel('Specification')
                                            ->valueLabel('Details')
                                            ->state([
                                                'Material' => 'Mesh, aluminum, high-grade plastic',
                                                'Weight capacity' => '300 lbs',
                                                'Height adjustment' => '17" to 21"',
                                                'Warranty' => '5 years limited',
                                                'Assembly required' => 'Yes (Tools Included)',
                                            ]),
                                    ]),

                                Tabs\Tab::make('Reviews')
                                    ->badge(fn () => 3)
                                    ->icon(Heroicon::OutlinedPencilSquare),
                            ]),
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
