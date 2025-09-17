<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class MergeTagExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'mergeTag';

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
                'tag' => 'span[data-type="' . self::$name . '"]',
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function addAttributes(): array
    {
        return [
            'id' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-id') ?: null,
                'renderHTML' => fn ($attributes) => ['data-id' => $attributes->id ?? null],
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
            'span',
            HTML::mergeAttributes(
                ['data-type' => self::$name],
                $this->options['HTMLAttributes'],
                $HTMLAttributes,
            ),
            0,
        ];
    }
}
