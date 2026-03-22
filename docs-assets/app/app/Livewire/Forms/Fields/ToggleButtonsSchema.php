<?php

namespace App\Livewire\Forms\Fields;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ToggleButtonsSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('toggleButtons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtons')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->default('published'),
                ]),
            Group::make()
                ->id('toggleButtonsColors')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsColors')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->colors([
                            'draft' => 'info',
                            'scheduled' => 'warning',
                            'published' => 'success',
                        ])
                        ->default('draft'),
                ]),
            Group::make()
                ->id('toggleButtonsIcons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsIcons')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->icons([
                            'draft' => Heroicon::OutlinedPencil,
                            'scheduled' => Heroicon::OutlinedClock,
                            'published' => Heroicon::OutlinedCheckCircle,
                        ])
                        ->default('scheduled'),
                ]),
            Group::make()
                ->id('toggleButtonsHiddenLabels')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsHiddenLabels')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->icons([
                            'draft' => Heroicon::OutlinedPencil,
                            'scheduled' => Heroicon::OutlinedClock,
                            'published' => Heroicon::OutlinedCheckCircle,
                        ])
                        ->colors([
                            'draft' => 'info',
                            'scheduled' => 'warning',
                            'published' => 'success',
                        ])
                        ->hiddenButtonLabels()
                        ->default('published'),
                ]),
            Group::make()
                ->id('toggleButtonsTooltips')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsTooltips')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->icons([
                            'draft' => Heroicon::OutlinedPencil,
                            'scheduled' => Heroicon::OutlinedClock,
                            'published' => Heroicon::OutlinedCheckCircle,
                        ])
                        ->tooltips([
                            'draft' => 'Set as a draft before publishing.',
                            'scheduled' => 'Schedule publishing on a specific date.',
                            'published' => 'Publish now',
                        ])
                        ->default('draft'),
                ]),
            Group::make()
                ->id('toggleButtonsBoolean')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsBoolean')
                        ->label('Like this post?')
                        ->boolean()
                        ->default(true),
                ]),
            Group::make()
                ->id('toggleButtonsInline')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsInline')
                        ->label('Like this post?')
                        ->boolean()
                        ->inline()
                        ->default(false),
                ]),
            Group::make()
                ->id('toggleButtonsGrouped')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsGrouped')
                        ->label('Like this post?')
                        ->boolean()
                        ->grouped()
                        ->default(true),
                ]),
            Group::make()
                ->id('toggleButtonsMultiple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsMultiple')
                        ->label('Technologies')
                        ->multiple()
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->default(['tailwind', 'laravel']),
                ]),
            Group::make()
                ->id('toggleButtonsColumns')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsColumns')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->columns(2)
                        ->default('alpine'),
                ]),
            Group::make()
                ->id('toggleButtonsRows')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsRows')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->columns(2)
                        ->gridDirection('row')
                        ->default('alpine'),
                ]),
            Group::make()
                ->id('disabledOptionToggleButtons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('disabledOptionToggleButtons')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->disableOptionWhen(fn (string $value): bool => $value === 'published'),
                ]),
        ];
    }
}
