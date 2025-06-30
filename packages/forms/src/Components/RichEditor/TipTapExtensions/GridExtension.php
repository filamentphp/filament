<?php

namespace Awcodes\Richie\Tiptap\Nodes;

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
            'data-type' => [
                'default' => 'symmetric',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-type'),
            ],
            'data-columns' => [
                'default' => '2',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-columns'),
                'renderHTML' => function ($attributes): array {
                    $attributes = (array) $attributes;

                    return [
                        'data-columns' => $attributes['data-columns'],
                        'style' => 'grid-template-columns: repeat(' . $attributes['data-columns'] . ', 1fr);',
                    ];
                },
            ],
            'data-stack-at' => [
                'default' => 'md',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-stack-at'),
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
    public function renderHTML($node, $HTMLAttributes = []): array
    {
        return [
            'div',
            HTML::mergeAttributes($this->options['HTMLAttributes'], $HTMLAttributes),
            0,
        ];
    }
}
