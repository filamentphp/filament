<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class LeadExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'lead';

    /**
     * @return array<string, mixed>
     */
    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [
                'class' => 'lead',
            ],
        ];
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'div',
                'getAttrs' => fn ($DOMNode): bool => str_contains((string) $DOMNode->getAttribute('class'), 'lead'),
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
            'div',
            HTML::mergeAttributes($this->options['HTMLAttributes'], $HTMLAttributes),
            0,
        ];
    }
}
