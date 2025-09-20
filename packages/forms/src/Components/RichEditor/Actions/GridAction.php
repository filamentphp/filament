<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
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
                Grid::make()
                    ->schema([
                        Select::make('preset')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.preset.label'))
                            ->live()
                            ->options([
                                'custom' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.custom'),
                                'two' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.two'),
                                'three' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.three'),
                                'four' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.four'),
                                'five' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.five'),
                                'asy_left_thirds' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.asy_left_thirds'),
                                'asy_right_thirds' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.asy_right_thirds'),
                                'asy_left_fourths' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.asy_left_fourths'),
                                'asy_right_fourths' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.asy_right_fourths'),
                            ]),
                        Select::make('stackAt')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.stack_at.label'))
                            ->selectablePlaceholder(false)
                            ->options([
                                'none' => __('filament-forms::components.rich_editor.actions.grid.modal.form.stack_at.dont_stack.label'),
                                'sm' => 'sm',
                                'md' => 'md',
                                'lg' => 'lg',
                            ])
                            ->default('md'),
                    ]),
                Grid::make()
                    ->schema([
                        TextInput::make('columns')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.columns.label'))
                            ->required()
                            ->minValue(2)
                            ->maxValue(12)
                            ->numeric()
                            ->step(1),
                        Toggle::make('asymmetric')
                            ->label(fn () => __('filament-forms::components.rich_editor.actions.grid.modal.form.asymmetric.label'))
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('leftSpan')
                            ->label(fn () => __('filament-forms::components.rich_editor.actions.grid.modal.form.left_span.label'))
                            ->required()
                            ->minValue(1)
                            ->maxValue(12)
                            ->numeric()
                            ->visible(fn (Get $get): mixed => $get('asymmetric'))
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                                    if ($value + $get('rightSpan') !== $get('columns')) {
                                        $fail(__('filament-forms::components.rich_editor.messages.invalid_col_spans'));
                                    }
                                },
                            ]),
                        TextInput::make('rightSpan')
                            ->label(fn () => __('filament-forms::components.rich_editor.actions.grid.modal.form.right_span.label'))
                            ->required()
                            ->minValue(1)
                            ->maxValue(12)
                            ->numeric()
                            ->visible(fn (Get $get): mixed => $get('asymmetric'))
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                                    if ($value + $get('leftSpan') !== $get('columns')) {
                                        $fail(__('filament-forms::components.rich_editor.messages.invalid_col_spans'));
                                    }
                                },
                            ]),
                    ])
                    ->visible(fn (Get $get) => $get('preset') === 'custom'),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                if ($data['preset'] !== 'custom') {
                    $data = [
                        ...$data,
                        ...self::getPreset($data['preset']),
                    ];
                }

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

    /**
     * Get preset configuration.
     *
     * @return array{columns: int, asymmetric: bool, leftSpan: ?int, rightSpan: ?int}
     */
    public static function getPreset(string $preset): array
    {
        $presets = [
            'two' => [
                'columns' => 2,
                'asymmetric' => false,
                'leftSpan' => null,
                'rightSpan' => null,
            ],
            'three' => [
                'columns' => 3,
                'asymmetric' => false,
                'leftSpan' => null,
                'rightSpan' => null,
            ],
            'four' => [
                'columns' => 4,
                'asymmetric' => false,
                'leftSpan' => null,
                'rightSpan' => null,
            ],
            'five' => [
                'columns' => 5,
                'asymmetric' => false,
                'leftSpan' => null,
                'rightSpan' => null,
            ],
            'asy_left_thirds' => [
                'columns' => 3,
                'asymmetric' => true,
                'leftSpan' => 1,
                'rightSpan' => 2,
            ],
            'asy_right_thirds' => [
                'columns' => 3,
                'asymmetric' => true,
                'leftSpan' => 2,
                'rightSpan' => 1,
            ],
            'asy_left_fourths' => [
                'columns' => 4,
                'asymmetric' => true,
                'leftSpan' => 1,
                'rightSpan' => 3,
            ],
            'asy_right_fourths' => [
                'columns' => 4,
                'asymmetric' => true,
                'leftSpan' => 3,
                'rightSpan' => 1,
            ],
        ];

        return $presets[$preset] ?? [];
    }
}
