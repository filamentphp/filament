<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CheckboxListTest extends Page
{
    protected string $view = 'pages.checkbox-list-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?int $navigationSort = 12;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CheckboxList::make('field')
                    ->label('Test CheckboxList')
                    ->options(['a' => 'Option A', 'b' => 'Option B', 'c' => 'Option C'])
                    ->extraAttributes(['data-testid' => 'checkbox-list']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
