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
                'class' => 'grid-layout',
            ],
        ];
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function addAttributes(): array
    {
        return [
            'data-cols' => [
                'default' => '2',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-cols'),
                'renderHTML' => function ($attributes): array {
                    $attributes = (array) $attributes;

                    // Security: `data-cols` originates from stored rich content, so cast it to a
                    // positive integer before interpolating it into the `style` attribute. The HTML
                    // sanitizer does not sanitize CSS, so a raw value could otherwise inject extra
                    // declarations (e.g. `position: fixed` / `background-image: url(...)`).
                    $columns = max(1, (int) ($attributes['data-cols'] ?? 2));

                    return [
                        'data-cols' => $columns,
                        'style' => "--cols: repeat({$columns}, minmax(0, 1fr))",
                    ];
                },
            ],
            'data-from-breakpoint' => [
                'default' => 'lg',
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-from-breakpoint'),
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
                'getAttrs' => fn ($DOMNode): bool => in_array('grid-layout', explode(' ', (string) $DOMNode->getAttribute('class'))),
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
