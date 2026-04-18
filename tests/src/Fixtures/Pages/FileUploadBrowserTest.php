<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class FileUploadBrowserTest extends Page
{
    protected string $view = 'pages.file-upload-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?int $navigationSort = 17;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                FileUpload::make('attachment')
                    ->label('Attachment')
                    ->disk('public'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
