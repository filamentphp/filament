<?php

namespace Filament\Forms\Components\RichEditor\TipTapExtensions;

use Tiptap\Core\Mark;

class SmallExtension extends Mark
{
    /**
     * @var string
     */
    public static $name = 'small';

    /**
     * @return array<array<string, mixed>>
     */
    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'small',
            ],
        ];
    }

    /**
     * @param  object  $mark
     * @param  array<string, mixed>  $HTMLAttributes
     * @return array<mixed>
     */
    public function renderHTML($mark, $HTMLAttributes = []): array
    {
        return [
            'small',
            $HTMLAttributes,
            0,
        ];
    }
}
