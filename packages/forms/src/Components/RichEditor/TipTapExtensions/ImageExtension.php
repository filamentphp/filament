<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

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
            'loading' => [],
        ];
    }
}
