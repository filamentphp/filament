<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class RadioTest extends Page
{
    protected string $view = 'pages.radio-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRadio;

    protected static ?int $navigationSort = 8;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('field')
                    ->label('Test Radio')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->extraAttributes(['data-testid' => 'radio']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
