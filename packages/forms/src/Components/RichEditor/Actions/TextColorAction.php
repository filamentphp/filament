<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Width;
use Illuminate\Support\Arr;

class TextColorAction
{
    public static function make(): Action
    {
        return Action::make('textColor')
            ->label(__('filament-forms::components.rich_editor.actions.text_color.label'))
            ->modalHeading(__('filament-forms::components.rich_editor.actions.text_color.modal.heading'))
            ->modalWidth(Width::Large)
            ->fillForm(fn (array $arguments): array => [
                'color' => $arguments['color'] ?? null,
            ])
            ->schema(fn (RichEditor $component) => [
                Select::make('color')
                    ->label(__('filament-forms::components.rich_editor.actions.text_color.modal.form.color.label'))
                    ->options(Arr::mapWithKeys(
                        $component->getTextColors(),
                        fn (TextColor $color, string $name): array => [$name => <<<HTML
                            <div class="fi-fo-rich-editor-text-color-select-option">
                                <div class="fi-fo-rich-editor-text-color-select-option-preview" style="--color: {$color->getColor()}; --dark-color: {$color->getDarkColor()}"></div>

                                <div>{$color->getSafeLabelHtml()}</div>
                            </div>
                            HTML],
                    ))
                    ->allowHtml()
                    ->native(false),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                $isSingleCharacterSelection = ($arguments['editorSelection']['head'] ?? null) === ($arguments['editorSelection']['anchor'] ?? null);

                if (blank($data['color'])) {
                    $component->runCommands(
                        [
                            ...($isSingleCharacterSelection ? [EditorCommand::make(
                                'extendMarkRange',
                                arguments: ['textColor'],
                            )] : []),
                            EditorCommand::make('unsetTextColor'),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );

                    return;
                }

                $component->runCommands(
                    [
                        ...($isSingleCharacterSelection ? [EditorCommand::make(
                            'extendMarkRange',
                            arguments: ['link'],
                        )] : []),
                        EditorCommand::make(
                            'setTextColor',
                            arguments: [[
                                'color' => $data['color'],
                            ]],
                        ),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
