<?php

namespace Filament\Tests\Panels\Fixtures\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;

class HookPage extends Page
{
    protected static string $view = 'app.fixtures.pages.hook-page';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-circle';

    protected static ?int $navigationSort = 2;

    public $name;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
            ]);
    }

    public function save()
    {
        $this->callHook('afterSave');
    }
}
