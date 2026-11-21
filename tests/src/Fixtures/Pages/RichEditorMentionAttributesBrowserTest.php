<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class RichEditorMentionAttributesBrowserTest extends Page
{
    protected string $view = 'pages.rich-editor-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->label('Content')
                    ->json()
                    ->disabled()
                    ->default([
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'content' => [[
                                'type' => 'mention',
                                'attrs' => [
                                    'id' => 'author-7',
                                    'label' => 'Alice Chen',
                                    'char' => '@',
                                    'extra' => [
                                        'autofocus' => '',
                                        'tabindex' => '0',
                                        'onfocus' => 'window.mentionFocusAttributeExecuted = true',
                                        'x-init' => 'window.mentionAlpineAttributeExecuted = true',
                                        'data-attacker' => 'stored',
                                    ],
                                ],
                            ]],
                        ]],
                    ])
                    ->mentions([
                        MentionProvider::make('@')
                            ->items(['author-7' => 'Alice Chen'])
                            ->extraAttributes(['data-provider' => 'trusted']),
                    ])
                    ->extraAttributes(['data-testid' => 'mention-attributes-rich-editor']),
            ])
            ->statePath('data');
    }
}
