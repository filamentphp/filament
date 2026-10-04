<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Checkbox;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CheckboxTest extends Page
{
    protected string $view = 'pages.checkbox-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?int $navigationSort = 7;

    public ?array $data = [];

    public int $updateCount = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Checkbox::make('field')
                    ->label('Test Checkbox')
                    ->inline()
                    ->live()
                    ->afterStateUpdated(function (): void {
                        $this->updateCount++;

                        usleep(250_000);
                    })
                    ->extraAttributes(['data-testid' => 'checkbox']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
