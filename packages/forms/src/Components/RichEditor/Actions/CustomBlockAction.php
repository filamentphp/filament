<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Support\Enums\Width;

class CustomBlockAction
{
    public const NAME = 'customBlock';

    public static function make(): Action
    {
        return Action::make(static::NAME)
            ->fillForm(fn (array $arguments): ?array => $arguments['config'] ?? null)
            ->modalHeading(function (array $arguments, RichEditor $component): ?string {
                $block = $component->getCustomBlock($arguments['id']);

                if (blank($block)) {
                    return null;
                }

                return $block::getLabel();
            })
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel(fn (array $arguments): ?string => match ($arguments['mode']) {
                'insert' => __('filament-forms::components.rich_editor.actions.custom_block.modal.actions.insert.label'),
                'edit' => __('filament-forms::components.rich_editor.actions.custom_block.modal.actions.save.label'),
                default => null,
            })
            ->bootUsing(function (Action $action, array $arguments, RichEditor $component) {
                $block = $component->getCustomBlock($arguments['id']);

                if (blank($block)) {
                    return;
                }

                return $block::configureEditorAction($action);
            })
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                $block = $component->getCustomBlock($arguments['id']);

                if (blank($block)) {
                    return;
                }

                $customBlockContent = [
                    'type' => 'customBlock',
                    'attrs' => [
                        'config' => $data,
                        'id' => $arguments['id'],
                        'label' => $block::getPreviewLabel($data),
                        'preview' => base64_encode($block::toPreviewHtml($data)),
                    ],
                ];

                // Insert at the dragged position
                if (filled($arguments['dragPosition'] ?? null)) {
                    $component->runCommands(
                        [
                            EditorCommand::make(
                                'insertContentAt',
                                arguments: [
                                    $arguments['dragPosition'],
                                    $customBlockContent,
                                ],
                            ),
                        ],
                    );

                    return;
                }

                // Insert after the currently selected node
                if (
                    ($arguments['editorSelection']['type'] === 'node') &&
                    (($arguments['mode'] ?? null) === 'insert')
                ) {
                    $component->runCommands(
                        [
                            EditorCommand::make(
                                'insertContentAt',
                                arguments: [
                                    ($arguments['editorSelection']['anchor'] ?? -1) + 1,
                                    $customBlockContent,
                                ],
                            ),
                        ],
                    );

                    return;
                }

                // Fixes an issue where the editor selection is sent as text instead of a node,
                // which causes the block update to fail when though the block is selected.
                if (
                    (($arguments['mode'] ?? null) === 'edit') &&
                    ($arguments['editorSelection']['type'] !== 'node')
                ) {
                    $arguments['editorSelection']['type'] = 'node';
                    $arguments['editorSelection']['anchor']--;

                    unset($arguments['editorSelection']['head']);
                }

                // Insert at the current selection
                $component->runCommands(
                    [
                        EditorCommand::make(
                            'insertContent',
                            arguments: [
                                $customBlockContent,
                            ],
                        ),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
