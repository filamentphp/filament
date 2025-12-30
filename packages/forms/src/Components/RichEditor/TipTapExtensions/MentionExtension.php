<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class MentionExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'mention';

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
            'label' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-label') ?: null,
                'renderHTML' => fn ($attributes) => ['data-label' => $attributes->label ?? null],
            ],
            'char' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-char') ?: '@',
                'renderHTML' => fn ($attributes) => ['data-char' => $attributes->char ?? '@'],
            ],
            'extra' => [
                'default' => null,
                'parseHTML' => fn ($DOMNode) => null,
                'renderHTML' => fn ($attributes) => [],
                'rendered' => false,
            ],
        ];
    }

    public function renderText($node): string
    {
        $char = $node->attrs->char ?? '@';
        $label = $node->attrs->label ?? '';

        return "{$char}{$label}";
    }

    /**
     * @param  object  $node
     * @param  array<string, mixed>  $HTMLAttributes
     * @return array<mixed>
     */
    public function renderHTML($node, $HTMLAttributes = []): array
    {
        $char = $node->attrs->char ?? '@';
        $label = $node->attrs->label ?? '';

        // Inject content into the node so tiptap-php can render it
        $node->content = [
            (object) [
                'type' => 'text',
                'text' => "{$char}\u{00A0}{$label}",
            ],
        ];

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
