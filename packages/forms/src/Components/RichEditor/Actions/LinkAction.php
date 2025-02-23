<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Illuminate\Support\Js;

class LinkAction
{
    public static function make(): Action
    {
        return Action::make('link')
            ->alpineClickHandler(fn (RichEditor $component): string => '$wire.mountAction(\'link\', { url: getEditor().getAttributes(\'link\')?.href, shouldOpenInNewTab: getEditor().getAttributes(\'link\')?.target === \'_blank\', editorSelection }, ' . Js::from(['schemaComponent' => $component->getKey()]) . ')')
            ->modalHeading('Link')
            ->modalWidth(Width::Large)
            ->fillForm(fn (array $arguments): array => [
                'url' => $arguments['url'] ?? null,
                'shouldOpenInNewTab' => $arguments['shouldOpenInNewTab'] ?? false,
            ])
            ->schema([
                TextInput::make('url')
                    ->label('URL')
                    ->url(),
                Checkbox::make('shouldOpenInNewTab')
                    ->label('Open in new tab'),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                $isSingleCharacterSelection = ($arguments['editorSelection']['head'] ?? null) === ($arguments['editorSelection']['anchor'] ?? null);

                if (blank($data['url'])) {
                    $component->runCommands(
                        [
                            ...($isSingleCharacterSelection ? [new EditorCommand(
                                name: 'extendMarkRange',
                                arguments: ['link'],
                            )] : []),
                            new EditorCommand(name: 'unsetLink'),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );

                    return;
                }

                $component->runCommands(
                    [
                        ...($isSingleCharacterSelection ? [new EditorCommand(
                            name: 'extendMarkRange',
                            arguments: ['link'],
                        )] : []),
                        new EditorCommand(
                            name: 'setLink',
                            arguments: [[
                                'href' => $data['url'],
                                'target' => $data['shouldOpenInNewTab'] ? '_blank' : null,
                            ]],
                        ),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
