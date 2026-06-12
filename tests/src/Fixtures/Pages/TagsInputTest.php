<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TagsInputTest extends Page
{
    protected string $view = 'pages.tags-input-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TagsInput::make('tags')
                    ->label('Basic Tags')
                    ->extraAttributes(['data-testid' => 'basic-tags']),

                TagsInput::make('suggested_tags')
                    ->label('Tags with Suggestions')
                    ->suggestions([
                        'Laravel',
                        'Livewire',
                        'Alpine.js',
                        'Tailwind CSS',
                        'Filament',
                    ])
                    ->extraAttributes(['data-testid' => 'suggested-tags']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
