<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SelectTest extends Page
{
    protected string $view = 'pages.select-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('Single Select')
                    ->options([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ])
                    ->native(false)
                    ->required()
                    ->extraAttributes(['data-testid' => 'single-select']),

                Select::make('multiple_status')
                    ->label('Multiple Select')
                    ->options([
                        'apple' => 'Apple',
                        'banana' => 'Banana',
                        'cherry' => 'Cherry',
                        'date' => 'Date',
                    ])
                    ->multiple()
                    ->extraAttributes(['data-testid' => 'multiple-select']),

                Select::make('searchable_status')
                    ->label('Searchable Select')
                    ->options([
                        'red' => 'Red',
                        'green' => 'Green',
                        'blue' => 'Blue',
                        'yellow' => 'Yellow',
                        'purple' => 'Purple',
                    ])
                    ->searchable()
                    ->extraAttributes(['data-testid' => 'searchable-select']),

                Select::make('clearable_status')
                    ->label('Clearable Select')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                    ])
                    ->native(false)
                    ->extraAttributes(['data-testid' => 'clearable-select']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
