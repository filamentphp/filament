<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class PartialRenderingTest extends Page
{
    protected string $view = 'pages.partial-rendering-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Partially render components')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('product_sku')
                            ->label('Product SKU')
                            ->state(fn (): string => Str::upper(Str::random(8)))
                            ->extraAttributes(['class' => 'product-sku']),
                        TextInput::make('product_name')
                            ->label('Product Name')
                            ->required()
                            ->live(debounce: 500)
                            ->partiallyRenderComponentsAfterStateUpdated(['product_slug'])
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('product_slug', Str::slug($state))),
                        TextInput::make('product_slug')
                            ->required()
                            ->label('Product Slug'),
                    ]),
                Section::make('Partially render self')
                    ->columns(2)
                    ->schema([
                        TextInput::make('post_title')
                            ->label('Post Title')
                            ->live(debounce: 500)
                            ->partiallyRenderAfterStateUpdated()
                            ->afterLabel(fn (Get $get): ?string => filled($get('post_title')) ?
                                '/' . Str::slug($get('post_title')) :
                                null),
                        TextEntry::make('post_reference')
                            ->label('Post Reference')
                            ->state(static fn (): string => Str::upper(Str::random(8)))
                            ->extraAttributes(['class' => 'post-reference']),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
