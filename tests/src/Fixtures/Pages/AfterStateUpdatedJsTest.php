<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AfterStateUpdatedJsTest extends Page
{
    protected string $view = 'pages.after-state-updated-js-test';

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
                TextInput::make('name')
                    ->label('Name')
                    ->extraAttributes(['data-testid' => 'name-input'])
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('email', ($state ?? '').replaceAll(' ', '.').toLowerCase() + '@example.com')
                    JS),
                TextInput::make('email')
                    ->label('Email')
                    ->extraAttributes(['data-testid' => 'email-input']),
                Flex::make([
                    TextInput::make('flex_name')
                        ->label('Name')
                        ->extraAttributes(['data-testid' => 'flex-name-input'])
                        ->afterStateUpdatedJs(<<<'JS'
                                $set('flex_email', ($state ?? '').replaceAll(' ', '.').toLowerCase() + '@example.com')
                            JS),
                    TextInput::make('flex_email')
                        ->label('Email')
                        ->extraAttributes(['data-testid' => 'flex-email-input']),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
