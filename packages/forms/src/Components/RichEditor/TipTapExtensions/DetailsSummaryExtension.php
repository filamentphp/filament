<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class DetailsSummaryExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'detailsSummary';

    /**
     * @return array<string, mixed>
     */
    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [],
        ];
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'summary',
            ],
        ];
    }

    /**
     * @param  object  $node
     * @param  array<string, mixed>  $HTMLAttributes
     * @return array<mixed>
     */
    public function renderHTML($node, $HTMLAttributes = []): array
    {
        return [
            'summary',
            HTML::mergeAttributes($this->options['HTMLAttributes'], $HTMLAttributes),
            0,
        ];
    }
}
