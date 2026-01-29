<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class CustomBlockExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'customBlock';

    /**
     * @return array<string, mixed>
     */
    public function addOptions(): array
    {
        return [];
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'div[data-type="' . self::$name . '"]',
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function addAttributes(): array
    {
        return [
            'config' => [
                'parseHTML' => fn ($DOMNode) => json_decode($DOMNode->getAttribute('data-config')) ?: null,
                'renderHTML' => fn ($attributes) => ['data-config' => json_encode($attributes->config ?? null)],
            ],
            'id' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-id') ?: null,
                'renderHTML' => fn ($attributes) => ['data-id' => $attributes->id ?? null],
            ],
            'label' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-label') ?: null,
                'rendered' => false,
            ],
            'preview' => [
                'parseHTML' => fn ($DOMNode) => base64_decode($DOMNode->getAttribute('data-preview') ?: ''),
                'rendered' => false,
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
            HTML::mergeAttributes(
                ['data-type' => self::$name],
                $HTMLAttributes,
            ),
        ];
    }
}
