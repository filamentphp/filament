<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Group;

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
