<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

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
                'class' => 'grid-layout-col',
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
                        'style' => "--col-span: span {$attributes['data-col-span']} / span {$attributes['data-col-span']}",
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
                'getAttrs' => fn ($DOMNode): bool => in_array('grid-layout-col', explode(' ', (string) $DOMNode->getAttribute('class'))),
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
