<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Width;

class ColorAction
{
    public static function make(): Action
    {
        return Action::make('color')
            ->label(__('filament-forms::components.rich_editor.actions.color.label'))
            ->modalHeading(__('filament-forms::components.rich_editor.actions.color.modal.heading'))
            ->modalWidth(Width::Large)
            ->fillForm(fn (array $arguments): array => [
                'color' => $arguments['color'] ?? null,
            ])
            ->schema(function (RichEditor $component): array {
                return [
                    Select::make('color')
                        ->label(__('filament-forms::components.rich_editor.actions.color.modal.form.color.label'))
                        ->options(fn (): array => $component->getColors()),
                ];
            })
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                $isSingleCharacterSelection = ($arguments['editorSelection']['head'] ?? null) === ($arguments['editorSelection']['anchor'] ?? null);

                if (blank($data['color'])) {
                    $component->runCommands(
                        [
                            ...($isSingleCharacterSelection ? [EditorCommand::make(
                                'extendMarkRange',
                                arguments: ['textStyle'],
                            )] : []),
                            EditorCommand::make('unsetColor'),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );

                    return;
                }

                $component->runCommands(
                    [
                        ...($isSingleCharacterSelection ? [EditorCommand::make(
                            'extendMarkRange',
                            arguments: ['textStyle'],
                        )] : []),
                        EditorCommand::make(
                            'setColor',
                            arguments: [
                                $data['color'],
                            ],
                        ),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            })
            ->extraModalFooterActions([
                Action::make('unsetColor')
                    ->color('danger')
                    ->label('Remove Color')
                    ->action(function (array $arguments, RichEditor $component): void {
                        $isSingleCharacterSelection = ($arguments['editorSelection']['head'] ?? null) === ($arguments['editorSelection']['anchor'] ?? null);

                        $component->runCommands(
                            [
                                ...($isSingleCharacterSelection ? [EditorCommand::make(
                                    'extendMarkRange',
                                    arguments: ['textStyle'],
                                )] : []),
                                EditorCommand::make('unsetColor'),
                            ],
                            editorSelection: $arguments['editorSelection'],
                        );

                        return;
                    }),
            ]);
    }
}
