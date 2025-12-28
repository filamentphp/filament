<?php

namespace Filament\Forms\Components\RichEditor\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;

class GridAction
{
    public static function make(): Action
    {
        return Action::make('grid')
            ->label(__('filament-forms::components.rich_editor.actions.grid.label'))
            ->modalHeading(__('filament-forms::components.rich_editor.actions.grid.modal.heading'))
            ->modalWidth(Width::Large)
            ->schema([
                Grid::make()
                    ->schema([
                        Select::make('preset')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.preset.label'))
                            ->placeholder(__('filament-forms::components.rich_editor.actions.grid.modal.form.preset.placeholder'))
                            ->options([
                                'two' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.options.two'),
                                'three' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.options.three'),
                                'four' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.options.four'),
                                'five' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.options.five'),
                                'two_start_third' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.options.two_start_third'),
                                'two_end_third' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.options.two_end_third'),
                                'two_start_fourth' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.options.two_start_fourth'),
                                'two_end_fourth' => __('filament-forms::components.rich_editor.actions.grid.modal.form.preset.options.two_end_fourth'),
                            ])
                            ->afterStateUpdatedJs(<<<'JS'
                                Object.entries({
                                    two: {
                                        columns: 2,
                                        isAsymmetric: false,
                                        startSpan: null,
                                        endSpan: null,
                                    },
                                    three: {
                                        columns: 3,
                                        isAsymmetric: false,
                                        startSpan: null,
                                        endSpan: null,
                                    },
                                    four: {
                                        columns: 4,
                                        isAsymmetric: false,
                                        startSpan: null,
                                        endSpan: null,
                                    },
                                    five: {
                                        columns: 5,
                                        isAsymmetric: false,
                                        startSpan: null,
                                        endSpan: null,
                                    },
                                    two_start_third: {
                                        isAsymmetric: true,
                                        startSpan: 1,
                                        endSpan: 2,
                                    },
                                    two_end_third: {
                                        isAsymmetric: true,
                                        startSpan: 2,
                                        endSpan: 1,
                                    },
                                    two_start_fourth: {
                                        isAsymmetric: true,
                                        startSpan: 1,
                                        endSpan: 3,
                                    },
                                    two_end_fourth: {
                                        isAsymmetric: true,
                                        startSpan: 3,
                                        endSpan: 1,
                                    },
                                }[$state] ?? {}).forEach(([key, value]) => $set(key, value))
                            JS)
                            ->dehydrated(false),
                        Select::make('fromBreakpoint')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.from_breakpoint.label'))
                            ->options([
                                'default' => __('filament-forms::components.rich_editor.actions.grid.modal.form.from_breakpoint.options.default'),
                                'sm' => __('filament-forms::components.rich_editor.actions.grid.modal.form.from_breakpoint.options.sm'),
                                'md' => __('filament-forms::components.rich_editor.actions.grid.modal.form.from_breakpoint.options.md'),
                                'lg' => __('filament-forms::components.rich_editor.actions.grid.modal.form.from_breakpoint.options.lg'),
                                'xl' => __('filament-forms::components.rich_editor.actions.grid.modal.form.from_breakpoint.options.xl'),
                                '2xl' => __('filament-forms::components.rich_editor.actions.grid.modal.form.from_breakpoint.options.2xl'),
                            ])
                            ->default('lg'),
                        Toggle::make('isAsymmetric')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.is_asymmetric.label'))
                            ->columnSpanFull(),
                        TextInput::make('columns')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.columns.label'))
                            ->integer()
                            ->minValue(2)
                            ->maxValue(12)
                            ->default(2)
                            ->hiddenJs(<<<'JS'
                                $get('isAsymmetric')
                            JS),
                        TextInput::make('startSpan')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.start_span.label'))
                            ->integer()
                            ->minValue(1)
                            ->maxValue(12)
                            ->visibleJs(<<<'JS'
                                $get('isAsymmetric')
                            JS),
                        TextInput::make('endSpan')
                            ->label(__('filament-forms::components.rich_editor.actions.grid.modal.form.end_span.label'))
                            ->integer()
                            ->minValue(1)
                            ->maxValue(12)
                            ->visibleJs(<<<'JS'
                                $get('isAsymmetric')
                            JS),
                    ]),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                if ($data['isAsymmetric'] ?? false) {
                    $columns = [(int) ($data['startSpan'] ?? 1), (int) ($data['endSpan'] ?? 1)];
                } else {
                    $columns = array_fill(0, max(2, (int) ($data['columns'] ?? 2)), 1);
                }

                $component->runCommands(
                    [
                        EditorCommand::make(
                            'insertGrid',
                            arguments: [[
                                'fromBreakpoint' => $data['fromBreakpoint'] ?? 'lg',
                                'columns' => $columns,
                            ]],
                        ),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
