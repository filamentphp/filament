<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CalloutBrowserTest extends Page
{
    protected string $view = 'pages.callout-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?int $navigationSort = 6;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Information')
                    ->description('This is an info callout with important information.')
                    ->info(),

                Callout::make('Success')
                    ->description('Your changes have been saved successfully.')
                    ->success(),

                Callout::make('Warning')
                    ->description('Please review this information before proceeding.')
                    ->warning(),

                Callout::make('Error')
                    ->description('An error occurred while processing your request.')
                    ->danger(),

                Callout::make('No Background')
                    ->description('This callout has no background color.')
                    ->warning()
                    ->color(null),
            ])
            ->statePath('data');
    }
}
