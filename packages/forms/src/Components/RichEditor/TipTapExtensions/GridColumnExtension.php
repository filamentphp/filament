<?php

namespace Awcodes\Richie\Tiptap\Nodes;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class GridColumnExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'gridColumn';

    /**
     * @return array<array<string, mixed>>
     */
    public function addOptions(): array
    {
        return [
            'HTMLAttributes' => [
                'class' => 'fi-re-grid-column',
            ],
        ];
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function addAttributes(): array
    {
        return [
            'data-col-span' => [
                'default' => '1',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-col-span'),
                'renderHTML' => function ($attributes): array {
                    $attributes = (array) $attributes;

                    return [
                        'data-col-span' => $attributes['data-col-span'],
                        'style' => 'grid-column: span ' . $attributes['data-col-span'] . ';',
                    ];
                },
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
                'getAttrs' => fn ($DOMNode): bool => str_contains((string) $DOMNode->getAttribute('class'), 'fi-re-grid-column'),
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
