<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class FileUploadBrowserTest extends Page
{
    protected string $view = 'pages.file-upload-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?int $navigationSort = 17;

    public ?array $data = [];

    public string $uploadLayout = 'default';

    public function mount(): void
    {
        $this->uploadLayout = request()->query('layout', 'default');
        if ($this->uploadLayout === 'repeater') {
            $this->form->fill(['items' => [['attachment' => []]]]);

            return;
        }

        $this->form->fill(request()->boolean('testReordering') ? [
            'attachment' => [
                'first-key' => 'first.txt',
                'second-key' => 'second.txt',
            ],
        ] : []);
    }

    public function form(Schema $form): Schema
    {
        if ($this->uploadLayout !== 'default') {
            $field = FileUpload::make('attachment')
                ->extraAttributes(['data-testid' => 'attachment-upload']);

            return $form->statePath('data')->schema([
                $this->uploadLayout === 'footer'
                    ? Section::make('Files')->footer([$field])
                    : Repeater::make('items')->schema([$field])->defaultItems(1),
            ]);
        }

        return $form
            ->schema([
                FileUpload::make('attachment')
                    ->label('Attachment')
                    ->extraAttributes(['data-testid' => 'attachment-upload'])
                    ->multiple()
                    ->reorderable()
                    ->fetchFileInformation(false)
                    ->getUploadedFileUsing(static function (string $file): array {
                        return [
                            'name' => $file,
                            'size' => 0,
                            'type' => 'text/plain',
                            'url' => "https://cdn.example.com/{$file}?signature=" . Str::random(),
                        ];
                    }),
            ])
            ->statePath('data');
    }

    public function replaceAttachment(): void
    {
        $this->data['attachment']['first-key'] = 'replacement.txt';
    }

    public function reorderAttachments(): void
    {
        $this->data['attachment'] = array_reverse($this->data['attachment'], preserve_keys: true);
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
