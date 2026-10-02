<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class DropdownTest extends Page
{
    protected string $view = 'pages.dropdown-test';

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
                    ->searchable()
                    ->options(['draft' => 'Draft', 'published' => 'Published']),
            ])
            ->statePath('data');
    }
}
