<?php

namespace App\Livewire\Forms\Fields;

use App\RichContentBlocks\AlertBlock;
use App\RichContentBlocks\BannerBlock;
use App\RichContentBlocks\CallToActionBlock;
use App\RichContentBlocks\HeroBlock;
use App\RichContentBlocks\ImageGalleryBlock;
use App\RichContentBlocks\TestimonialBlock;
use App\RichContentBlocks\VideoEmbedBlock;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Schemas\Components\Group;

class RichEditorSchema
{
    public static function schema(): array
    {
        $customBlockPreviewContent = [
            'type' => 'doc',
            'content' => [
                ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Made for everyday adventures']]],
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Discover thoughtfully made essentials for your next trip, from a weekend away to a walk around the neighbourhood.']]],
                ['type' => 'customBlock', 'attrs' => ['id' => 'alert', 'config' => ['message' => 'Free shipping on orders over £50.']]],
                ['type' => 'customBlock', 'attrs' => ['id' => 'testimonial', 'config' => ['quote' => 'My favourite bag for a weekend away. There is room for everything I need, and the thoughtful details make it just as useful on my daily commute.', 'author' => 'Alex Morgan']]],
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Explore the collection and find your new everyday favourite.']]],
            ],
        ];

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
                            HeroBlock::class,
                            CallToActionBlock::class,
                            TestimonialBlock::class,
                        ]),
                ]),
            Group::make()
                ->id('richEditorCustomBlockPreviews')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorCustomBlockPreviews')
                        ->label('Content')
                        ->json()
                        ->toolbarButtons([['bold', 'italic', 'link'], ['undo', 'redo']])
                        ->customBlocks([AlertBlock::class, TestimonialBlock::class])
                        ->default($customBlockPreviewContent),
                ]),
            Group::make()
                ->id('richEditorMinimalCustomBlockControls')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorMinimalCustomBlockControls')
                        ->label('Content')
                        ->json()
                        ->toolbarButtons([['bold', 'italic', 'link'], ['undo', 'redo']])
                        ->customBlocks([AlertBlock::class, TestimonialBlock::class])
                        ->minimalCustomBlockControls()
                        ->default($customBlockPreviewContent),
                ]),
            Group::make()
                ->id('richEditorGroupedCustomBlocks')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RichEditor::make('richEditorGroupedCustomBlocks')
                        ->label('Content')
                        ->customBlocks([
                            AlertBlock::class,
                            'Marketing' => [
                                HeroBlock::class,
                                CallToActionBlock::class,
                                BannerBlock::class,
                                TestimonialBlock::class,
                            ],
                            'Media' => [
                                ImageGalleryBlock::class,
                                VideoEmbedBlock::class,
                            ],
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
