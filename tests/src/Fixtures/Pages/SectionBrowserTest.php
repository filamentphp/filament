<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SectionBrowserTest extends Page
{
    protected string $view = 'pages.section-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?int $navigationSort = 14;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->description('Enter your personal details below.')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required(),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required(),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
