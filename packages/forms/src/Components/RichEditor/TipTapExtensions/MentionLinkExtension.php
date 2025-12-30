<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class MentionLinkExtension extends Node
{
    /**
     * @var string
     */
    public static $name = 'mentionLink';

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
                'tag' => 'a[data-type="mention"]',
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
            'href' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('href') ?: null,
                'renderHTML' => fn ($attributes) => ['href' => $attributes->href ?? null],
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
                'text' => "{$char} {$label}",
            ],
        ];

        return [
            'a',
            HTML::mergeAttributes(
                ['data-type' => 'mention'],
                $this->options['HTMLAttributes'],
                $HTMLAttributes,
            ),
            0,
        ];
    }
}
