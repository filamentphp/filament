<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

class BuilderSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('builder')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('builder')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('heading')
                                ->schema([
                                    TextInput::make('content')
                                        ->label('Heading')
                                        ->required(),
                                    Select::make('level')
                                        ->options([
                                            'h1' => 'Heading 1',
                                            'h2' => 'Heading 2',
                                            'h3' => 'Heading 3',
                                            'h4' => 'Heading 4',
                                            'h5' => 'Heading 5',
                                            'h6' => 'Heading 6',
                                        ])
                                        ->required(),
                                ])
                                ->columns(2),
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ]),
                            Builder\Block::make('image')
                                ->schema([
                                    FileUpload::make('url')
                                        ->label('Image')
                                        ->image()
                                        ->required(),
                                    TextInput::make('alt')
                                        ->label('Alt text')
                                        ->required(),
                                ]),
                        ])
                        ->default([
                            [
                                'type' => 'heading',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet',
                                    'level' => 'h2',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                            [
                                'type' => 'image',
                                'data' => [
                                    'url' => null,
                                    'alt' => 'Lorem ipsum dolor sit amet',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                            [
                                'type' => 'image',
                                'data' => [
                                    'url' => null,
                                    'alt' => 'Lorem ipsum dolor sit amet',
                                ],
                            ],
                        ]),
                ]),
            Group::make()
                ->id('labelledBuilder')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('labelledBuilder')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('heading')
                                ->schema([
                                    TextInput::make('content')
                                        ->label('Heading')
                                        ->required(),
                                    Select::make('level')
                                        ->options([
                                            'h1' => 'Heading 1',
                                            'h2' => 'Heading 2',
                                            'h3' => 'Heading 3',
                                            'h4' => 'Heading 4',
                                            'h5' => 'Heading 5',
                                            'h6' => 'Heading 6',
                                        ])
                                        ->required(),
                                ])
                                ->columns(2)
                                ->label(function (?array $state): string {
                                    if ($state === null) {
                                        return 'Heading';
                                    }

                                    return $state['content'] ?? 'Untitled heading';
                                }),
                        ])
                        ->default([
                            [
                                'type' => 'heading',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet',
                                    'level' => 'h2',
                                ],
                            ],
                            [
                                'type' => 'heading',
                                'data' => [
                                    'content' => null,
                                    'level' => 'h3',
                                ],
                            ],
                        ]),
                ]),
            Group::make()
                ->id('builderIcons')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-40 max-w-5xl',
                ])
                ->schema([
                    Builder::make('builderIcons')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Bars3BottomLeft),
                            Builder\Block::make('image')
                                ->icon(Heroicon::Photo),
                        ])
                        ->default([
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                        ]),
                ]),
            Group::make()
                ->id('builderBlockIcons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('builderBlockIcons')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Bars3BottomLeft),
                            Builder\Block::make('image')
                                ->icon(Heroicon::Photo),
                        ])
                        ->blockIcons()
                        ->default([
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies.',
                                ],
                            ],
                        ]),
                ]),
            Group::make()
                ->id('builderAddActionAlignment')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('builderAddActionAlignment')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Bars3BottomLeft),
                            Builder\Block::make('image')
                                ->icon(Heroicon::Photo),
                        ])
                        ->addActionAlignment(Alignment::Start)
                        ->default([
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies.',
                                ],
                            ],
                        ]),
                ]),
            Group::make()
                ->id('builderBlockPreviews')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('builderBlockPreviews')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('heading')
                                ->schema([
                                    TextInput::make('text')
                                        ->placeholder('Default heading'),
                                ])
                                ->preview('filament.content.block-previews.heading'),
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->preview('filament.content.block-previews.paragraph'),
                        ])
                        ->blockPreviews()
                        ->default([
                            [
                                'type' => 'heading',
                                'data' => [
                                    'text' => 'Introducing Filament v4',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Filament is a collection of full-stack components for accelerated Laravel development. They are beautifully designed, intuitive to use, and fully extensible.',
                                ],
                            ],
                        ]),
                ]),
            Group::make()
                ->id('builderReorderableWithButtons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('builderReorderableWithButtons')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Bars3BottomLeft),
                        ])
                        ->default([
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                        ])
                        ->reorderableWithButtons(),
                ]),
            Group::make()
                ->id('collapsibleBuilder')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('collapsibleBuilder')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Bars3BottomLeft),
                        ])
                        ->default([
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Filament is a collection of full-stack components for accelerated Laravel development.',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'They are beautifully designed, intuitive to use, and fully extensible.',
                                ],
                            ],
                        ])
                        ->collapsible(),
                ]),
            Group::make()
                ->id('collapsedBuilder')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('collapsedBuilder')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Bars3BottomLeft),
                        ])
                        ->default([
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                        ])
                        ->collapsed(),
                ]),
            Group::make()
                ->id('cloneableBuilder')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Builder::make('cloneableBuilder')
                        ->label('Content')
                        ->schema([
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Bars3BottomLeft),
                        ])
                        ->default([
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                            [
                                'type' => 'paragraph',
                                'data' => [
                                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                ],
                            ],
                        ])
                        ->cloneable(),
                ]),
            Group::make()
                ->id('builderBlockPickerColumns')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 max-w-5xl',
                    'style' => 'padding-bottom: 12rem',
                ])
                ->schema([
                    Builder::make('builderBlockPickerColumns')
                        ->label('Content')
                        ->blockPickerColumns(2)
                        ->blockPickerWidth('2xl')
                        ->schema([
                            Builder\Block::make('heading')
                                ->schema([
                                    TextInput::make('content')
                                        ->label('Heading')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Hashtag),
                            Builder\Block::make('paragraph')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Paragraph')
                                        ->required(),
                                ])
                                ->icon(Heroicon::Bars3BottomLeft),
                            Builder\Block::make('image')
                                ->schema([
                                    FileUpload::make('url')
                                        ->label('Image')
                                        ->image(),
                                ])
                                ->icon(Heroicon::Photo),
                            Builder\Block::make('quote')
                                ->schema([
                                    Textarea::make('content')
                                        ->label('Quote')
                                        ->required(),
                                ])
                                ->icon(Heroicon::ChatBubbleBottomCenterText),
                        ]),
                ]),
        ];
    }
}
