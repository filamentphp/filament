<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Livewire\Component;

class AttachFilesAction
{
    public static function make(): Action
    {
        return Action::make('attachFiles')
            ->alpineClickHandler(fn (RichEditor $component): string => '$wire.mountAction(\'attachFiles\', { alt: getEditor().getAttributes(\'image\')?.alt, id: getEditor().getAttributes(\'image\')?.id, src: getEditor().getAttributes(\'image\')?.src, editorSelection }, ' . Js::from(['schemaComponent' => $component->getKey()]) . ')')
            ->modalHeading('Upload file')
            ->modalWidth(Width::Large)
            ->fillForm(fn (array $arguments): array => [
                'alt' => $arguments['alt'] ?? null,
            ])
            ->schema(fn (array $arguments): array => [
                FileUpload::make('file')
                    ->label(filled($arguments['src'] ?? null) ? 'Replace file' : 'File')
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/gif', 'image/webp'])
                    ->storeFiles(false)
                    ->required(blank($arguments['src'] ?? null))
                    ->hiddenLabel(blank($arguments['src'] ?? null)),
                TextInput::make('alt')
                    ->label(filled($arguments['src'] ?? null) ? 'Change alt text' : 'Alt text'),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component, Component $livewire): void {
                if ($data['file'] ?? null) {
                    $id = (string) Str::orderedUuid();

                    data_set($livewire, "componentFileAttachments.{$component->getStatePath()}.{$id}", $data['file']);
                    $src = $component->saveUploadedFileAttachment($id);
                }

                if (filled($arguments['src'] ?? null)) {
                    $id ??= $arguments['id'] ?? null;
                    $src ??= $arguments['src'];

                    $component->runCommands(
                        [
                            new EditorCommand(name: 'updateAttributes', arguments: [
                                'image',
                                [
                                    'alt' => $data['alt'] ?? null,
                                    'id' => $id,
                                    'src' => $src,
                                ],
                            ]),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );

                    return;
                }

                if (blank($id ?? null)) {
                    return;
                }

                if (blank($src ?? null)) {
                    return;
                }

                $component->runCommands(
                    [
                        new EditorCommand(name: 'insertContent', arguments: [[
                            'type' => 'image',
                            'attrs' => [
                                'alt' => $data['alt'] ?? null,
                                'id' => $id,
                                'src' => $src,
                            ],
                        ]]),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
