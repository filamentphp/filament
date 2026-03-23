<?php

namespace App\Livewire\Forms\Fields;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

class FieldSchema
{
    public static function afterMount(Component $component): void
    {
        $component->validate(
            ['data.aboveErrorMessage' => ['required'], 'data.belowErrorMessage' => ['required']],
            attributes: ['data.aboveErrorMessage' => 'name', 'data.belowErrorMessage' => 'name'],
        );
    }

    public static function schema(): array
    {
        return [
            Group::make()
                ->id('simple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('simple')
                        ->label('Name')
                        ->default('Dan Harrin'),
                ]),
            Group::make()
                ->id('disabled')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('disabled')
                        ->label('Name')
                        ->disabled()
                        ->default('Dan Harrin'),
                ]),
            Group::make()
                ->id('inlineLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('inlineLabel')
                        ->label('Name')
                        ->inlineLabel(),
                ]),
            Group::make()
                ->id('inlineLabelSection')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Section::make('Details')
                        ->schema([
                            TextInput::make('inlineLabelSectionName')
                                ->label('Name'),
                            TextInput::make('inlineLabelSectionEmail')
                                ->label('Email address'),
                            TextInput::make('inlineLabelSectionPhone')
                                ->label('Phone number'),
                        ])
                        ->inlineLabel(),
                ]),
            Group::make()
                ->id('hiddenLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('hiddenLabelSearch')
                        ->label('Search')
                        ->hiddenLabel()
                        ->placeholder('Search posts...')
                        ->prefixIcon('heroicon-m-magnifying-glass'),
                ]),
            Group::make()
                ->id('placeholder')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('placeholder')
                        ->label('Name')
                        ->placeholder('Dan Harrin'),
                ]),
            Group::make()
                ->id('fused')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FusedGroup::make([
                        TextInput::make('city')
                            ->placeholder('City'),
                        Select::make('country')
                            ->placeholder('Country'),
                    ]),
                ]),
            Group::make()
                ->id('fusedLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FusedGroup::make([
                        TextInput::make('city')
                            ->placeholder('City'),
                        Select::make('country')
                            ->placeholder('Country'),
                    ])
                        ->label('Location'),
                ]),
            Group::make()
                ->id('fusedColumns')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FusedGroup::make([
                        TextInput::make('city')
                            ->placeholder('City'),
                        Select::make('country')
                            ->placeholder('Country'),
                    ])
                        ->label('Location')
                        ->columns(2),
                ]),
            Group::make()
                ->id('fusedColumnsSpan')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FusedGroup::make([
                        TextInput::make('city')
                            ->placeholder('City')
                            ->columnSpan(2),
                        Select::make('country')
                            ->placeholder('Country'),
                    ])
                        ->label('Location')
                        ->columns(3),
                ]),
            Group::make()
                ->id('textBelowContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('name')
                        ->belowContent('This is the user\'s full name.'),
                ]),
            Group::make()
                ->id('componentBelowContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('name')
                        ->belowContent(Text::make('This is the user\'s full name.')->weight(FontWeight::Bold)),
                ]),
            Group::make()
                ->id('actionBelowContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('name')
                        ->belowContent(Action::make('generate')),
                ]),
            Group::make()
                ->id('belowContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('name')
                        ->belowContent([
                            Icon::make(Heroicon::InformationCircle),
                            'This is the user\'s full name.',
                            Action::make('generate'),
                        ]),
                ]),
            Group::make()
                ->id('belowContentAlignment')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('name')
                        ->belowContent(Schema::end([
                            Icon::make(Heroicon::InformationCircle),
                            'This is the user\'s full name.',
                            Action::make('generate'),
                        ])),
                    TextInput::make('name')
                        ->belowContent(Schema::between([
                            Icon::make(Heroicon::InformationCircle),
                            'This is the user\'s full name.',
                            Action::make('generate'),
                        ])),
                    TextInput::make('name')
                        ->belowContent(Schema::between([
                            Flex::make([
                                Icon::make(Heroicon::InformationCircle)
                                    ->grow(false),
                                'This is the user\'s full name.',
                            ]),
                            Action::make('generate'),
                        ])),
                ]),
            Group::make()
                ->id('aboveLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('aboveLabel')
                        ->label('Name')
                        ->aboveLabel([
                            Icon::make(Heroicon::Star),
                            'This is the content above the field\'s label',
                        ]),
                ]),
            Group::make()
                ->id('beforeLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('beforeLabel')
                        ->label('Name')
                        ->beforeLabel(Icon::make(Heroicon::Star)),
                ]),
            Group::make()
                ->id('afterLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('afterLabel')
                        ->label('Name')
                        ->afterLabel([
                            Icon::make(Heroicon::Star),
                            'This is the content after the field\'s label',
                        ]),
                ]),
            Group::make()
                ->id('afterLabelAlignedStart')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('afterLabelAlignedStart')
                        ->label('Name')
                        ->afterLabel(Schema::start([
                            Icon::make(Heroicon::Star),
                            'This is the content after the field\'s label',
                        ])),
                ]),
            Group::make()
                ->id('belowLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('belowLabel')
                        ->label('Name')
                        ->belowLabel([
                            Icon::make(Heroicon::Star),
                            'This is the content below the field\'s label',
                        ]),
                ]),
            Group::make()
                ->id('aboveContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('aboveContent')
                        ->label('Name')
                        ->belowLabel([
                            Icon::make(Heroicon::Star),
                            'This is the content above the field\'s content',
                        ]),
                ]),
            Group::make()
                ->id('beforeContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('beforeContent')
                        ->label('Name')
                        ->beforeContent(Icon::make(Heroicon::Star)),
                ]),
            Group::make()
                ->id('afterContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('afterContent')
                        ->label('Name')
                        ->afterContent(Icon::make(Heroicon::Star)),
                ]),
            Group::make()
                ->id('aboveErrorMessage')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('aboveErrorMessage')
                        ->label('Name')
                        ->required()
                        ->aboveErrorMessage([
                            Icon::make(Heroicon::Star),
                            'This is the content above the field\'s error message',
                        ]),
                ]),
            Group::make()
                ->id('belowErrorMessage')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('belowErrorMessage')
                        ->label('Name')
                        ->required()
                        ->belowErrorMessage([
                            Icon::make(Heroicon::Star),
                            'This is the content below the field\'s error message',
                        ]),
                ]),
            Group::make()
                ->id('fileUploadMultiple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUploadMultiple')
                        ->label('Attachments')
                        ->multiple(),
                ]),
            Group::make()
                ->id('fileUploadImage')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    FileUpload::make('fileUploadImage')
                        ->label('Image')
                        ->image()
                        ->imageEditor(),
                ]),
            Group::make()
                ->id('tagsInputSuggestions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                    'style' => 'padding-bottom: 12rem',
                ])
                ->schema([
                    TagsInput::make('tagsInputSuggestions')
                        ->label('Tags')
                        ->suggestions([
                            'tailwindcss',
                            'alpinejs',
                            'laravel',
                            'livewire',
                        ])
                        ->default(['tailwindcss']),
                ]),
            Group::make()
                ->id('tagsInputReorderable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TagsInput::make('tagsInputReorderable')
                        ->label('Tags')
                        ->reorderable()
                        ->default(['Tailwind CSS', 'Alpine.js', 'Laravel', 'Livewire']),
                ]),
            Group::make()
                ->id('suffixAction')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('suffixAction')
                        ->label('Cost')
                        ->prefix('€')
                        ->default('22.66')
                        ->suffixAction(
                            Action::make('copyCostToPrice')
                                ->icon(Heroicon::Clipboard),
                        ),
                ]),
        ];
    }
}
