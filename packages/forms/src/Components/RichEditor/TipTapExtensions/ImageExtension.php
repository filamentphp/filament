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
                'renderHTML' => function ($attributes) {
                    $width = $this->sanitizeStyleLength($attributes->width ?? null);

                    return [
                        'width' => $width,
                        'style' => filled($width) ? "width: {$width}" : null,
                    ];
                },
            ],
            'height' => [
                'parseHTML' => fn ($DOMNode) => $DOMNode->getAttribute('height') ?: $this->getStyleValue($DOMNode, 'height'),
                'renderHTML' => function ($attributes) {
                    $height = $this->sanitizeStyleLength($attributes->height ?? null);

                    return [
                        'height' => $height,
                        'style' => filled($height) ? "height: {$height}" : null,
                    ];
                },
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

    protected function sanitizeStyleLength(mixed $value): ?string
    {
        if (blank($value) || (! is_string($value) && ! is_int($value) && ! is_float($value))) {
            return null;
        }

        $value = trim((string) $value);

        // Security: `width` and `height` originate from stored rich content and are interpolated
        // into a `style` attribute. The HTML sanitizer does not sanitize CSS, so only allow a bare
        // number optionally followed by a CSS length unit; a value containing CSS metacharacters
        // (`;`, `:`, `(`, ...) is rejected so it cannot inject additional declarations (e.g.
        // `position: fixed` / `background-image: url(...)`).
        return preg_match('/^\d+(?:\.\d+)?(?:%|px|em|rem|vw|vh|vmin|vmax|pt|pc|cm|mm|in|ch|ex)?$/i', $value)
            ? $value
            : null;
    }
}
