<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class IndistinctStateBrowserTest extends Page
{
    protected string $view = 'pages.indistinct-state-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public bool $isSaved = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Repeater::make('items')
                    ->schema([
                        Select::make('choice')
                            ->options([0 => 'Zero', 1 => 'One'])
                            ->fixIndistinctState()
                            ->extraInputAttributes(['data-testid' => 'choice']),
                    ])
                    ->default([
                        ['choice' => 0],
                        ['choice' => null],
                    ]),
            ]);
    }

    public function save(): void
    {
        $this->form->getState();
        $this->isSaved = true;
    }
}
