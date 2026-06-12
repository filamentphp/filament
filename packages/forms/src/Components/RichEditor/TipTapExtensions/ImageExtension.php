<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use DOMElement;
use Tiptap\Nodes\Image as BaseImage;

class ImageExtension extends BaseImage
{
    /**
     * @return array<array<string, mixed>>
     */
    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'img[src]',
            ],
            [
                'tag' => 'img[data-id]',
            ],
        ];
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function addAttributes(): array
    {
        return [
            ...parent::addAttributes(),
            'id' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('data-id') ?: null,
                'renderHTML' => fn ($attributes) => ['data-id' => $attributes->id ?? null],
            ],
            'width' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('width') ?: $this->getStyleValue($DOMNode, 'width'),
                'renderHTML' => fn ($attributes) => [
                    'width' => $attributes->width ?? null,
                    'style' => isset($attributes->width) ? "width: {$attributes->width}" : null,
                ],
            ],
            'height' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('height') ?: $this->getStyleValue($DOMNode, 'height'),
                'renderHTML' => fn ($attributes) => [
                    'height' => $attributes->height ?? null,
                    'style' => isset($attributes->height) ? "height: {$attributes->height}" : null,
                ],
            ],
            'loading' => [],
        ];
    }

    protected function getStyleValue(DOMElement $DOMNode, string $property): ?string
    {
        $style = $DOMNode->getAttribute('style');

        if (blank($style)) {
            return null;
        }

        preg_match("/{$property}:\s*([^;]+)/", $style, $matches);

        return $matches[1] ?? null;
    }
}
