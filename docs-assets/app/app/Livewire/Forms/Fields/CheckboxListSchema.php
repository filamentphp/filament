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

class CheckboxListSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('checkboxList')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CheckboxList::make('checkboxList')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ]),
                ]),
            Group::make()
                ->id('checkboxListOptionDescriptions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CheckboxList::make('checkboxListOptionDescriptions')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->descriptions([
                            'tailwind' => 'A utility-first CSS framework for rapidly building modern websites without ever leaving your HTML.',
                            'alpine' => new HtmlString('A rugged, minimal tool for composing behavior <strong>directly in your markup</strong>.'),
                            'laravel' => str('A **web application** framework with expressive, elegant syntax.')->inlineMarkdown()->toHtmlString(),
                            'livewire' => 'A full-stack framework for Laravel building dynamic interfaces simple, without leaving the comfort of Laravel.',
                        ]),
                ]),
            Group::make()
                ->id('checkboxListColumns')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CheckboxList::make('checkboxListColumns')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->default(['tailwind', 'laravel'])
                        ->columns(2),
                ]),
            Group::make()
                ->id('checkboxListRows')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CheckboxList::make('checkboxListRows')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->default(['tailwind', 'laravel'])
                        ->columns(2)
                        ->gridDirection('row'),
                ]),
            Group::make()
                ->id('checkboxListHtmlLabels')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CheckboxList::make('checkboxListHtmlLabels')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => '<span style="color: #3b82f6">Tailwind CSS</span>',
                            'alpine' => '<span style="color: #22c55e">Alpine.js</span>',
                            'laravel' => '<span style="color: #ef4444">Laravel</span>',
                            'livewire' => '<span style="color: #ec4899">Livewire</span>',
                        ])
                        ->default(['tailwind', 'laravel'])
                        ->allowHtml(),
                ]),
            Group::make()
                ->id('searchableCheckboxList')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CheckboxList::make('searchableCheckboxList')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->default(['tailwind', 'laravel'])
                        ->searchable(),
                ]),
            Group::make()
                ->id('bulkToggleableCheckboxList')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CheckboxList::make('bulkToggleableCheckboxList')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->default(['tailwind', 'laravel'])
                        ->bulkToggleable(),
                ]),
            Group::make()
                ->id('checkboxListDisabledOptions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CheckboxList::make('checkboxListDisabledOptions')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->default(['tailwind', 'laravel'])
                        ->disableOptionWhen(static fn (string $value): bool => in_array($value, ['laravel', 'livewire'])),
                ]),
        ];
    }
}
