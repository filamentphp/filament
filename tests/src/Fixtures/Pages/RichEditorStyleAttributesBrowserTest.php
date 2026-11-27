<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\RichEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class RichEditorStyleAttributesBrowserTest extends Page
{
    protected string $view = 'pages.rich-editor-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $content = [];

        foreach (['start', 'center', 'end', 'justify', 'left', 'right', 'center; --alignment-injected: 1', 'inherit', '', null, 1, true, ['center'], ['toString' => 'center']] as $alignment) {
            foreach (['paragraph', 'heading'] as $type) {
                $content[] = [
                    'type' => $type,
                    'attrs' => ['textAlign' => $alignment, 'level' => 2],
                    'content' => [['type' => 'text', 'text' => 'Text alignment']],
                ];
            }

            $content[] = [
                'type' => 'table',
                'content' => [[
                    'type' => 'tableRow',
                    'content' => array_map(static fn (string $type): array => [
                        'type' => $type,
                        'attrs' => ['align' => $alignment],
                        'content' => [[
                            'type' => 'paragraph',
                            'content' => [['type' => 'text', 'text' => 'Cell alignment']],
                        ]],
                    ], ['tableHeader', 'tableCell']),
                ]],
            ];
        }

        $this->form->fill(['content' => ['type' => 'doc', 'content' => $content]]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->label('Content')
                    ->json()
                    ->disabled()
                    ->extraAttributes(['data-testid' => 'style-attributes-rich-editor']),
            ])
            ->statePath('data');
    }
}
