<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class GridExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'grid';

    /**
     * @return array<array<string, mixed>>
     */
    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [
                'class' => 'fi-re-grid',
            ],
        ];
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function addAttributes(): array
    {
        return [
            'data-asymmetric' => [
                'default' => false,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-asymmetric'),
            ],
            'data-columns' => [
                'default' => '2',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-columns'),
                'renderHTML' => function ($attributes): array {
                    $attributes = (array) $attributes;

                    return [
                        'data-columns' => $attributes['data-columns'],
                        'style' => 'display: grid; gap: 1rem; grid-template-columns: repeat(' . $attributes['data-columns'] . ', 1fr);',
                    ];
                },
            ],
            'data-stack-at' => [
                'default' => 'md',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-stack-at'),
            ],
            'data-left-span' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-left-span'),
            ],
            'data-right-span' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-right-span'),
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
                'getAttrs' => fn ($DOMNode): bool => str_contains((string) $DOMNode->getAttribute('class'), 'fi-re-grid')
                    && ! str_contains((string) $DOMNode->getAttribute('class'), '-column'),
            ],
        ];
    }

    /**
     * @param  object  $node
     * @param  array<string, mixed>  $HTMLAttributes
     * @return array<mixed>
     */
    public function renderHTML($node, array $HTMLAttributes = []): array
    {
        return [
            'div',
            HTML::mergeAttributes($this->options['HTMLAttributes'], $HTMLAttributes),
            0,
        ];
    }
}
