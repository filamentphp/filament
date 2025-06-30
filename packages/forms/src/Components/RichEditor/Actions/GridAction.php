<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;

class GridAction
{
    public static function make(): Action
    {
        return Action::make('grid')
            ->label(__('filament-forms::components.rich_editor.actions.grid.label'))
            ->modalHeading(__('filament-forms::components.rich_editor.actions.grid.modal.heading'))
            ->modalWidth(Width::Large)
            ->fillForm(fn (array $arguments): array => [
                'columns' => $arguments['columns'] ?? 2,
                'stackAt' => $arguments['stackAt'] ?? 'md',
                'asymmetric' => $arguments['asymmetric'] ?? false,
                'leftSpan' => $arguments['leftSpan'] ?? null,
                'rightSpan' => $arguments['rightSpan'] ?? null,
            ])
            ->schema([
                TextInput::make('columns')
                    ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.columns.label'))
                    ->required()
                    ->live()
                    ->minValue(2)
                    ->maxValue(12)
                    ->numeric()
                    ->step(1),
                Select::make('stackAt')
                    ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.stack_at.label'))
                    ->live()
                    ->selectablePlaceholder(false)
                    ->options([
                        'none' => __('filament-forms::components.rich_editor.actions.grid.modal.form.stack_at.dont_stack.label'),
                        'sm' => 'sm',
                        'md' => 'md',
                        'lg' => 'lg',
                    ])
                    ->default('md'),
                Toggle::make('asymmetric')
                    ->label(fn () => trans('filament-forms::components.rich_editor.actions.grid.modal.form.asymmetric.label'))
                    ->default(false)
                    ->live()
                    ->columnSpanFull(),
                TextInput::make('leftSpan')
                    ->label(fn () => trans('filament-forms::components.rich_editor.actions.grid.modal.form.left_span.label'))
                    ->required()
                    ->live()
                    ->minValue(1)
                    ->maxValue(12)
                    ->numeric()
                    ->visible(fn (Get $get): mixed => $get('asymmetric')),
                TextInput::make('rightSpan')
                    ->label(fn () => trans('filament-forms::components.rich_editor.actions.grid.modal.form.right_span.label'))
                    ->required()
                    ->live()
                    ->minValue(1)
                    ->maxValue(12)
                    ->numeric()
                    ->visible(fn (Get $get): mixed => $get('asymmetric')),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                $component->runCommands(
                    [
                        EditorCommand::make(
                            'insertGrid',
                            arguments: [[
                                ...$data,
                            ]],
                        ),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
