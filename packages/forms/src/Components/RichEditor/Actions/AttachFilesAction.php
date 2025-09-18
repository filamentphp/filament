<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;
use Livewire\Component;

class AttachFilesAction
{
    public static function make(): Action
    {
        return Action::make('attachFiles')
            ->label(__('filament-forms::components.rich_editor.actions.attach_files.label'))
            ->modalHeading(__('filament-forms::components.rich_editor.actions.attach_files.modal.heading'))
            ->modalWidth(Width::Large)
            ->fillForm(fn (array $arguments): array => [
                'alt' => $arguments['alt'] ?? null,
            ])
            ->schema(fn (array $arguments, RichEditor $component): array => [
                FileUpload::make('file')
                    ->label(filled($arguments['src'] ?? null)
                        ? __('filament-forms::components.rich_editor.actions.attach_files.modal.form.file.label.existing')
                        : __('filament-forms::components.rich_editor.actions.attach_files.modal.form.file.label.new'))
                    ->acceptedFileTypes($component->getFileAttachmentsAcceptedFileTypes())
                    ->maxSize($component->getFileAttachmentsMaxSize())
                    ->storeFiles(false)
                    ->required(blank($arguments['src'] ?? null))
                    ->hiddenLabel(blank($arguments['src'] ?? null)),
                TextInput::make('alt')
                    ->label(filled($arguments['src'] ?? null)
                        ? __('filament-forms::components.rich_editor.actions.attach_files.modal.form.alt.label.existing')
                        : __('filament-forms::components.rich_editor.actions.attach_files.modal.form.alt.label.new'))
                    ->maxLength(1000),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component, Component $livewire): void {
                if ($data['file'] ?? null) {
                    $id = (string) Str::orderedUuid();

                    data_set($livewire, "componentFileAttachments.{$component->getStatePath()}.{$id}", $data['file']);
                    $src = $component->getUploadedFileAttachmentTemporaryUrl($data['file']);
                }

                if (filled($arguments['src'] ?? null)) {
                    // Fixes an issue where the editor selection is sent as text instead of a node,
                    // which causes the image update to fail when though the image is selected.
                    if ($arguments['editorSelection']['type'] !== 'node') {
                        $arguments['editorSelection']['type'] = 'node';
                        $arguments['editorSelection']['anchor']--;

                        unset($arguments['editorSelection']['head']);
                    }

                    $id ??= $arguments['id'] ?? null;
                    $src ??= $arguments['src'];

                    $component->runCommands(
                        [
                            EditorCommand::make('updateAttributes', arguments: [
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
                        EditorCommand::make('insertContent', arguments: [[
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
