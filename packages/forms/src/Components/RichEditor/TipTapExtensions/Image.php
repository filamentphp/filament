<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Nodes\Image as BaseImage;

class Image extends BaseImage
{
    /**
     * @return array<string, array<mixed>>
     */
    public function addAttributes(): array
    {
        return [
            ...parent::addAttributes(),
            'id' => [],
        ];
    }
}
