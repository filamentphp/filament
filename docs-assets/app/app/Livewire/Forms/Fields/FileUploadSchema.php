<?php

namespace App\Livewire\Forms\Fields;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class FileUploadSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('fileUpload')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUpload')
                        ->label('Attachment'),
                ]),
            Group::make()
                ->id('fileUploadAvatar')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUploadAvatar')
                        ->label('Avatar')
                        ->avatar(),
                ]),
            Group::make()
                ->id('fileUploadImagePreview')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUploadImagePreview')
                        ->label('Featured image')
                        ->disk('public')
                        ->image()
                        ->default('test/sample-image.jpg'),
                ]),
            Group::make()
                ->id('fileUploadMultipleGrid')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUploadMultipleGrid')
                        ->label('Gallery')
                        ->disk('public')
                        ->image()
                        ->multiple()
                        ->panelLayout('grid')
                        ->default([
                            'test/sample-image.jpg',
                            'test/sample-image-2.jpg',
                            'test/sample-image-3.jpg',
                        ]),
                ]),
            Group::make()
                ->id('fileUploadOpenable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUploadOpenable')
                        ->label('Attachments')
                        ->disk('public')
                        ->openable()
                        ->default('test/sample-image.jpg'),
                ]),
            Group::make()
                ->id('fileUploadDownloadable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUploadDownloadable')
                        ->label('Attachments')
                        ->disk('public')
                        ->downloadable()
                        ->default('test/sample-image.jpg'),
                ]),
            Group::make()
                ->id('fileUploadImageEditor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUploadImageEditor')
                        ->label('Image')
                        ->disk('public')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->default('test/sample-image.jpg'),
                ]),
        ];
    }
}
