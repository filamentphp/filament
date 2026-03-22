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

class RichEditorSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('richEditor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditor')
                        ->label('Content'),
                ]),
            Group::make()
                ->id('richEditorCustomToolbar')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorCustomToolbar')
                        ->label('Content')
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            ['h2', 'h3'],
                            ['blockquote', 'bulletList', 'orderedList'],
                            ['undo', 'redo'],
                        ]),
                ]),
            Group::make()
                ->id('richEditorToolbarButtonGroup')
                ->extraAttributes([
                    'class' => 'p-16 pb-32 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorToolbarButtonGroup')
                        ->label('Content')
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            [ToolbarButtonGroup::make('Heading', ['h2', 'h3'])->icon('fi-o-heading')],
                            [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                            ['undo', 'redo'],
                        ]),
                ]),
            Group::make()
                ->id('richEditorTextualToolbarButtonGroup')
                ->extraAttributes([
                    'class' => 'p-16 pb-48 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorTextualToolbarButtonGroup')
                        ->label('Content')
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            [ToolbarButtonGroup::make('Paragraph', ['paragraph', 'h1', 'h2', 'h3'])->textualButtons()],
                            [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                            ['undo', 'redo'],
                        ]),
                ]),
            Group::make()
                ->id('richEditorMergeTags')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorMergeTags')
                        ->label('Email template')
                        ->mergeTags([
                            'first_name',
                            'last_name',
                            'company',
                            'unsubscribe_url',
                        ])
                        ->activePanel('mergeTags'),
                ]),
            Group::make()
                ->id('richEditorMentions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorMentions')
                        ->label('Content')
                        ->mentions([
                            MentionProvider::make('@')
                                ->items([
                                    1 => 'Dan Harrin',
                                    2 => 'Ryan Chandler',
                                    3 => 'Zep Fietje',
                                    4 => 'Dennis Koch',
                                    5 => 'Adam Weston',
                                    6 => 'Patrick Boivin',
                                ]),
                        ]),
                ]),
            Group::make()
                ->id('richEditorCustomBlocks')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorCustomBlocks')
                        ->label('Content')
                        ->customBlocks([
                            \App\RichContentBlocks\HeroBlock::class,
                            \App\RichContentBlocks\CallToActionBlock::class,
                            \App\RichContentBlocks\TestimonialBlock::class,
                        ]),
                ]),
            Group::make()
                ->id('richEditorFloatingToolbar')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorFloatingToolbar')
                        ->label('Content')
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            ['h2', 'h3'],
                            ['blockquote', 'bulletList', 'orderedList'],
                            ['undo', 'redo'],
                        ])
                        ->floatingToolbars([
                            'paragraph' => [
                                'bold', 'italic', 'underline',
                            ],
                        ])
                        ->default('<p>Filament is a collection of beautiful full-stack components for Laravel. It helps you build admin panels, customer-facing apps, and more.</p>'),
                ]),
            Group::make()
                ->id('richEditorTextColors')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorTextColors')
                        ->label('Content')
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'textColor'],
                            ['h2', 'h3'],
                            ['blockquote', 'bulletList', 'orderedList'],
                            ['undo', 'redo'],
                        ])
                        ->textColors([
                            'red' => TextColor::make('Red', '#ef4444'),
                            'orange' => TextColor::make('Orange', '#f97316'),
                            'green' => TextColor::make('Green', '#10b981'),
                            'sky' => TextColor::make('Sky', '#0ea5e9'),
                            'blue' => TextColor::make('Blue', '#3b82f6'),
                            'violet' => TextColor::make('Violet', '#8b5cf6'),
                        ]),
                ]),
        ];
    }
}
